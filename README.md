# Divine Intelligence — wersja produkcyjna

Strona wizytówka gotowa do wgrania na hosting. Wszystko jest statyczne poza jednym plikiem PHP,
który obsługuje formularz kontaktowy.

---

## Co jest w paczce

| Plik | Do czego służy |
|---|---|
| `index.html` | Strona główna — treść, style i skrypty w jednym pliku |
| `projekty.html` | Podstrona z pełnym portfolio (8 projektów w 4 branżach) |
| `formularz.php` | Odbiera formularz i wysyła zapytanie na `kontakt@divineintelligence.pl` |
| `404.html` | Strona błędu w identyfikacji marki |
| `.htaccess` | HTTPS, kompresja, pamięć podręczna, nagłówki bezpieczeństwa |
| `robots.txt` | Zgoda na indeksowanie + wskazanie mapy strony |
| `sitemap.xml` | Mapa strony dla Google |
| `favicon.svg` | Ikona w karcie przeglądarki — **w tej paczce nadpisana** nową plakietką DI (patrz niżej) |
| `apple-touch-icon.png` | Ikona po dodaniu do ekranu głównego na iPhonie (180 × 180, plakietka DI) |
| `og-image.png` | Obrazek pokazywany przy udostępnianiu linku (1200 × 630, pełny logotyp) |

Poprzednio te dwa ostatnie pliki były tylko wpięte w kod (`<meta>`, dane strukturalne), ale fizycznie
ich nie było w paczce — link wysłany na Messengerze czy WhatsAppie pokazywałby pusty podgląd. Teraz
oba realne obrazki są dołączone i gotowe do wgrania.

---

## SEO — co zmieniło się w tej paczce

- **`og-image.png`, `apple-touch-icon.png` i `.htaccess`** — wszystkie trzy były opisane w tym
  README jako część paczki, ale faktycznie w niej nie było — teraz naprawdę tam są. `.htaccess`
  wymusza HTTPS i jeden kanoniczny adres (bez „www.”, bez `index.html` w adresie), włącza
  kompresję i pamięć podręczną (szybsze ładowanie = lepsze Core Web Vitals) oraz podstawowe
  nagłówki bezpieczeństwa.
- **Dane strukturalne (`index.html`)** — doszły `logo` (wskazuje na `apple-touch-icon.png`) i
  `priceRange` w danych firmy — to dodatkowe sygnały, które Google może wykorzystać przy wynikach
  rozszerzonych i w panelu wiedzy.
- **Ikony social media w stopce** (`index.html` i `projekty.html`) — Instagram, Facebook, TikTok,
  na razie z pustym adresem (`href="#"`, oznaczone komentarzem `TODO` w kodzie). Miejsce i styl są
  gotowe, wystarczy podmienić trzy adresy, gdy konta powstaną — warto wtedy dopisać je też jako
  `sameAs` w danych strukturalnych na stronie głównej, bo to kolejny sygnał wiarygodności dla Google.
- **Przycisk WhatsApp** — nowy wiersz obok maila i telefonu w sekcji kontaktowej, z gotową,
  wstępnie wypełnioną wiadomością. Link działa od razu, bo wykorzystuje już istniejący numer telefonu.

- **Sekcja „Zaufanie"** — nowa, nad FAQ na stronie głównej. Zamiast pustego miejsca na opinię
  (puste „cudzysłowy" bardziej podkreślają brak historii, niż budują zaufanie) pokazuje trzy
  konkretne, sprawdzalne zasady: płatność po akceptacji, zatwierdzasz projekt przed produkcją,
  prawa do filmu w 100% Twoje. Gotowy blok na pierwszą prawdziwą opinię czeka **zakomentowany**
  w kodzie `index.html` — szukaj `<!-- OPINIA: ... -->` na końcu tej sekcji. Gdy dostaniesz
  pierwszą opinię (np. od gabinetu stomatologicznego), odkomentuj blok `.review-slot`, wstaw
  cytat i podpis, i dopisz `.review-slot` z powrotem do listy selektorów w `querySelectorAll(...)`
  na dole strony (linia zaczynająca się od `document.querySelectorAll('.head, .stat, ...`),
  żeby dostał tę samą animację wjazdu co reszta sekcji.

Rzeczy, których nie da się zrobić z poziomu samego kodu strony, a które realnie wpływają na
widoczność — Wizytówka Google (Profil Firmy), zgłoszenie w Google Search Console, wpisy w
katalogach branżowych — zostają do zrobienia osobno, poza tą paczką.

---

## Nowe logo — sam napis zamiast ikony

Zniknęła dotychczasowa ikona (pierścień halo). W jej miejsce na obu stronach i w stopce jest teraz
sam napis „DIVINE / INTELLIGENCE" w kroju DM Serif Display — jak w logotypach domów mody
(Dior, Prada), bez osobnego symbolu. Dołączony `favicon.svg` to jego mała, kwadratowa wersja —
plakietka z inicjałami „DI" w cienkiej ramce — do karty przeglądarki i jako punkt wyjścia do
awatara w social media. Jeśli masz już `apple-touch-icon.png` z poprzednią ikoną, warto go
podmienić na wersję z tą samą plakietką DI, żeby wszystko było spójne.

---

## Wgranie na hosting

1. Wrzuć **całą zawartość tego katalogu** do głównego folderu domeny — na większości
   polskich hostingów nazywa się `public_html`, czasem `htdocs` albo `www`.
2. Sprawdź, czy `index.html` leży bezpośrednio w tym folderze, a nie w podkatalogu.
3. Wejdź na `https://divineintelligence.pl` — strona powinna się otworzyć.
4. Wyślij testowe zapytanie z formularza i sprawdź, czy mail dotarł.

Plik `.htaccess` zaczyna się od kropki, więc bywa niewidoczny w menedżerach plików —
w FileZilli włącz *Serwer → Wymuś wyświetlanie ukrytych plików*.

---

## Zanim ruszysz — dwie rzeczy do podmiany

### 1. Adres nadawcy w formularzu
W `formularz.php`, w linii `const NADAWCA`, wpisz adres **istniejący w Twojej domenie**
(np. `formularz@divineintelligence.pl`). Załóż tę skrzynkę w panelu hostingu, nawet jeśli nigdy do niej
nie zajrzysz. Jeśli nadawcą będzie adres z obcej domeny (Gmail, WP), poczta odbiorcy
uzna wiadomość za podszywanie się i wrzuci ją do spamu.

### 2. Filmy w portfolio
Trzy kafelki w sekcji *Realizacje* (na stronie głównej) mają teraz tła gradientowe —
po jednym przykładzie z trzech branż: `.t1` (gabinety — Klinika Aurea), `.t5`
(kancelarie — Kancelaria Nord), `.t6` (salony — Studio Reflex). Sekcja była wcześniej
sześciokafelkowa; zmniejszyliśmy ją do trzech, żeby portfolio na stronie głównej było
zwięzłym skrótem, a nie pełną listą (pełna lista dalej jest na podstronie projektów).
Po nagraniu materiałów podmień w CSS te klasy na miniatury:

```css
.t1{background:url('/realizacje/klinika-aurea.jpg') center/cover}
```

a w HTML zamień `<a href="#kontakt" class="tile t1">` na link do filmu albo podepnij odtwarzacz.

### 3. To samo w `projekty.html`
Podstrona projektów ma własny, osobny komplet ośmiu kafelków (`.t1`–`.t8`, zdefiniowane
niezależnie od strony głównej) — nie są z niczym współdzielone, więc zmniejszenie
strony głównej do trzech kafelków niczego tu nie zmieniło. Podmieniaj je tak samo jak
wyżej, osobno w obu plikach. Podstrona ma pod spodem dopisek, że to przykłady stylu,
a nie gotowe realizacje — jeśli podmienisz kafelki na prawdziwe filmy klientów, warto
ten dopisek zaktualizować albo usunąć.

---

## Formularz — jak działa

Domyślnie wysyła dane do `formularz.php`, a ten wysyła maila na `kontakt@divineintelligence.pl`.
Zabezpieczenia w środku: ukryte pole-pułapka na boty, limit jednego zgłoszenia na 25 sekund
z jednego adresu IP, walidacja e-maila i ochrona przed wstrzykiwaniem nagłówków.

Wysyłka idzie w tle, bez przeładowania strony — potwierdzenie pojawia się pod przyciskiem.
Adres nadawcy zapytania trafia w pole *Reply-To*, więc odpowiadasz zwykłym „Odpowiedz”.

### Pole „Interesujący pakiet”
Formularz ma teraz dodatkowe, pierwsze pole — `<select name="pakiet">` z opcjami *Basic — 329 zł*,
*Indywidualny — wycena* i domyślną *Jeszcze się zastanawiam*. Przyciski „Zamawiam Basic” (w karcie
pakietu Basic) i „Opowiedz o swoim pomyśle” (w karcie Indywidualny) mają atrybut `data-pakiet` —
klik ustawia to pole na odpowiednią wartość i przewija do formularza, więc odwiedzający nie musi
niczego wybierać ręcznie. Wszystko — niezależnie od tego, którym przyciskiem ktoś wszedł do
formularza — idzie tą samą, jedną drogą przez `formularz.php` na `kontakt@divineintelligence.pl`; nie ma
już osobnej ścieżki przez program pocztowy. Wybrany pakiet trafia też do treści maila i do tematu
(np. `[Basic] Zapytanie ze strony — ...`), więc łatwo go wyłapać na pierwszy rzut oka w skrzynce.

**Jeśli hosting nie ma PHP** (GitHub Pages, Netlify, Vercel): formularz automatycznie przełącza się
na otwarcie programu pocztowego. Działa, ale konwertuje gorzej. Lepiej wtedy założyć darmowe konto
w [Formspree](https://formspree.io) i podmienić w `index.html`:

```html
<form id="kontakt-form" action="https://formspree.io/f/TWOJ_KLUCZ" method="POST" novalidate>
```

---

## Po wgraniu — pierwsze kroki w Google

1. **Google Search Console** — dodaj `divineintelligence.pl`, potwierdź własność i zgłoś `sitemap.xml`.
2. **Wizytówka Google** (Profil Firmy) — przy usługach lokalnych to często większe źródło
   zapytań niż sama strona.
3. **Sprawdź dane strukturalne** w [Rich Results Test](https://search.google.com/test/rich-results) —
   strona ma oznaczenia `ProfessionalService` i `FAQPage`, dzięki którym pytania mogą się
   rozwijać bezpośrednio w wynikach wyszukiwania.
4. **Sprawdź podgląd linku** w [Facebook Debugger](https://developers.facebook.com/tools/debug/) —
   powinien pokazać obrazek `og-image.png`, nie sam adres.

---

## Co strona ma pod maską

- **Dostępność:** wszystkie kontrasty spełniają WCAG AA lub AAA, nawigacja klawiaturą
  z widocznym obrysem, link pomijający nawigację, opisy grafik, poprawne etykiety pól.
- **Wydajność:** brak bibliotek zewnętrznych, jedyny zewnętrzny zasób to fonty Google.
  Około 60 klatek na sekundę przy przewijaniu, zerowe przesunięcia układu.
- **Ruch:** animacje respektują systemowe ustawienie *ogranicz ruch*. Bez JavaScriptu
  cała treść jest widoczna.
- **Loader i przejścia:** obie strony (`index.html`, `projekty.html`) mają krótki ekran
  ładowania z logo (znika samo, bez JavaScriptu też — wtedy jest ukryty od razu przez
  `<noscript>`) oraz płynne przenikanie między podstronami przez natywny mechanizm
  przeglądarki (`@view-transition`, bez bibliotek). W starszych przeglądarkach po prostu
  nie zauważysz różnicy — strona działa jak zwykle, przejście jest wyłącznie kosmetyczne.
- **Dane strukturalne:** `ProfessionalService`, `WebSite` i `FAQPage`.

---

## Uwagi prawne

W stopce jest informacja o przetwarzaniu danych z formularza, wskazująca **Aleksandrę Front**
jako administratorkę. Zweryfikuj z księgową limit przychodu w działalności nierejestrowanej —
przy tej formie obowiązuje miesięczny próg, po którego przekroczeniu trzeba zarejestrować firmę.
To ważniejsze niż wcześniej: pakiet Basic ma teraz stałą, widoczną cenę, więc łatwiej o realne,
częste transakcje od pierwszego dnia — warto pilnować progu na bieżąco, a nie dopiero przy
podsumowaniu miesiąca.

Pakiet **Basic ma stałą cenę (329 zł)** i widnieje wprost na stronie oraz w danych
strukturalnych (`price` w `hasOfferCatalog`). Pakiet **Indywidualny** nie ma ceny — prowadzi
do wyceny po rozmowie, tak jak wcześniej działały wszystkie pakiety.
