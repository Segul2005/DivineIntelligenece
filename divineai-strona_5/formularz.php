<?php
/**
 * Divine Intelligence: obsługa formularza kontaktowego.
 * Wysyła zapytanie ze strony na ODBIORCA (poniżej).
 *
 * Wymaga hostingu z PHP i włączoną funkcją mail(), mają ją praktycznie
 * wszystkie polskie hostingi współdzielone (cyberFolks, nazwa.pl, home.pl, OVH, LH.pl).
 * Nic więcej nie trzeba konfigurować.
 */

declare(strict_types=1);

const ODBIORCA   = 'kontakt@divineintelligence.pl';
const NADAWCA    = 'formularz@divineintelligence.pl';   // musi być adresem w Twojej domenie (SPF)
const NAZWA_STRONY = 'divineintelligence.pl';
const LIMIT_SEKUND = 25;                       // minimalny odstęp między wysyłkami z jednego IP
const KATALOG_LIMITOW = __DIR__ . '/.limity';  // własny katalog zamiast systemowego /tmp (bywa dzielony na hostingu)

header('X-Content-Type-Options: nosniff');

/** Odpowiedź: JSON dla fetch, przekierowanie dla przeglądarki bez JS. */
function odpowiedz(int $kod, string $komunikat, bool $ok = false): never {
    $chceJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
             || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    if ($chceJson) {
        http_response_code($kod);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'komunikat' => $komunikat], JSON_UNESCAPED_UNICODE);
    } else {
        $cel = $ok ? 'index.html#kontakt&wyslano=1' : 'index.html#kontakt&blad=1';
        http_response_code($ok ? 303 : 303);
        header('Location: /' . $cel);
    }
    exit;
}

/** Usuwa znaki nowej linii, blokuje wstrzykiwanie nagłówków maila. */
function bezpieczny(string $v): string {
    return trim(str_replace(["\r", "\n", "%0a", "%0d"], ' ', $v));
}

/**
 * Adres IP klienta. Uwzględnia ewentualny reverse proxy (Cloudflare i inne) —
 * bez tego, po podpięciu takiej usługi, limit częstotliwości widziałby jeden
 * adres proxy zamiast adresów odwiedzających i blokował wszystkich naraz.
 */
function adresIp(): string {
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $klucz) {
        if (!empty($_SERVER[$klucz])) {
            // X-Forwarded-For bywa lista "klient, proxy1, proxy2" — bierzemy pierwszy adres
            $wartosc = trim(explode(',', (string) $_SERVER[$klucz])[0]);
            if (filter_var($wartosc, FILTER_VALIDATE_IP)) {
                return $wartosc;
            }
        }
    }
    return 'nieznany';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    odpowiedz(405, 'Nieobsługiwana metoda.');
}

// --- pułapka na boty: pole ukryte, człowiek go nie wypełni ---
if (!empty($_POST['strona_www'])) {
    odpowiedz(200, 'Dziękujemy.', true);   // bot dostaje sukces, mail nie leci
}

// --- prosty limit częstotliwości (własny katalog, nie systemowy /tmp) ---
if (!is_dir(KATALOG_LIMITOW)) {
    @mkdir(KATALOG_LIMITOW, 0700);
    // .htaccess na wypadek, gdyby ktoś zgadł nazwę katalogu — blokuje dostęp z przeglądarki
    @file_put_contents(KATALOG_LIMITOW . '/.htaccess',
        "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n" .
        "<IfModule !mod_authz_core.c>\nOrder deny,allow\nDeny from all\n</IfModule>\n"
    );
}
$plikLimitu = KATALOG_LIMITOW . '/di_' . md5(adresIp());
if (is_file($plikLimitu) && (time() - (int) filemtime($plikLimitu)) < LIMIT_SEKUND) {
    odpowiedz(429, 'Chwila, zapytanie już poszło. Spróbuj za moment.');
}
@touch($plikLimitu);

// --- dane z formularza ---
$placowka  = bezpieczny((string) ($_POST['placowka'] ?? ''));
$email     = bezpieczny((string) ($_POST['email'] ?? ''));
$telefon   = bezpieczny((string) ($_POST['telefon'] ?? ''));
$pakiet    = bezpieczny((string) ($_POST['pakiet'] ?? 'brak'));
$zakres    = bezpieczny((string) ($_POST['zakres'] ?? 'brak'));
$wiadomosc = trim((string) ($_POST['wiadomosc'] ?? ''));
$zgoda     = !empty($_POST['zgoda']);
if ($pakiet === '') { $pakiet = 'brak'; }

if ($placowka === '' || mb_strlen($placowka) > 160) {
    odpowiedz(422, 'Podaj nazwę firmy.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    odpowiedz(422, 'Adres e-mail wygląda na niepoprawny.');
}
if (!$zgoda) {
    odpowiedz(422, 'Potrzebujemy zgody na kontakt.');
}
if (mb_strlen($wiadomosc) > 4000) {
    $wiadomosc = mb_substr($wiadomosc, 0, 4000) . ' […]';
}
if (mb_strlen($telefon) > 40) {
    $telefon = mb_substr($telefon, 0, 40);
}

// --- treść wiadomości ---
$prefiks = ($pakiet !== 'brak') ? "[{$pakiet}] " : '';
$temat = $prefiks . 'Zapytanie ze strony: ' . $placowka;
$tresc = "Nowe zapytanie z " . NAZWA_STRONY . "\n"
       . str_repeat('-', 44) . "\n\n"
       . "Firma:     {$placowka}\n"
       . "E-mail:    {$email}\n"
       . "Telefon:   " . ($telefon !== '' ? $telefon : '(nie podano)') . "\n"
       . "Pakiet:    {$pakiet}\n"
       . "Zakres:    {$zakres}\n\n"
       . "Wiadomość:\n" . ($wiadomosc !== '' ? $wiadomosc : '(nie podano)') . "\n\n"
       . str_repeat('-', 44) . "\n"
       . "Data: " . date('Y-m-d H:i:s') . "\n"
       . "IP:   " . adresIp() . "\n";

$naglowki = [
    'From: Formularz ' . NAZWA_STRONY . ' <' . NADAWCA . '>',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Mailer: PHP/' . phpversion(),
];

$wyslano = @mail(
    ODBIORCA,
    '=?UTF-8?B?' . base64_encode($temat) . '?=',
    $tresc,
    implode("\r\n", $naglowki),
    '-f' . NADAWCA
);

if ($wyslano) {
    odpowiedz(200, 'Dziękujemy, odezwiemy się w 24 godziny w dni robocze.', true);
}
odpowiedz(500, 'Nie udało się wysłać. Napisz proszę bezpośrednio na ' . ODBIORCA . '.');
