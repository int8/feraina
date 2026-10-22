# Udaina – landing page i zapisy do bety

Jedna strona z 11 sekcjami, osobny ekran zapisu do bety (`/beta`), panel wewnętrzny (`/panel`) z rankingiem integracji i eksportem CSV. Stack: Laravel 13, Tailwind 4, Vite, czysty JS (bez frameworka front-end). Dane trzymamy we własnej bazie, maile wysyłamy własnym SMTP – zero zewnętrznych narzędzi do formularzy.

## Uruchomienie lokalne

```sh
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate
npm install && npm run build      # albo `npm run dev` podczas pracy
php artisan serve
```

Testy: `php artisan test` (27 testów pokrywających kryteria akceptacji: walidację, limit 3 integracji, brak duplikatów, UTM, CSV, zmianę listy integracji w pliku).

## Treści: wszystko w `content/`, bez developera

| Plik | Co zawiera |
|---|---|
| `content/site.json` | Copy wszystkich sekcji, nazwa produktu, dane firmy do stopki, liczba miejsc w becie, cennik, bio założyciela |
| `content/integrations.json` | Lista grup i narzędzi. **Z tej jednej listy** budują się karty sekcji „Integracje” i checkboxy w formularzu bety |
| `content/form.json` | Typy firm, przedziały wydatków, treści zgód, klauzula RODO |
| `content/faq.json` | Pytania i odpowiedzi FAQ |
| `content/legal/*.md` | Polityka prywatności i regulamin bety (Markdown, osadzane na podstronach) |

Wartości w nawiasach kwadratowych (`[X]`, `[NAZWA]`, `[CENA]`…) to placeholdery do uzupełnienia. `[X]`, `[NAZWA]`, `[NAZWA FIRMY]`, `[NIP]`, `[ADRES]`, `[E-MAIL KONTAKTOWY]` są podmieniane automatycznie w tekstach na podstawie `site.json → product` i `company`.

Dodanie narzędzia = nowy wpis `{ "id": "...", "name": "..." }` w odpowiedniej grupie. `id` trafia do bazy jako identyfikator głosu, więc nie zmieniaj istniejących `id` po starcie.

## Proces zapisu do bety

1. `POST /beta` – e-mail (hero, sekcja końcowa, `/beta`). Rekord powstaje od razu ze statusem `niedokonczony`. Ten sam e-mail z pełnym zapisem → „Ten adres jest już na liście.” Ten sam e-mail niedokończony → wznowienie tego samego rekordu.
2. `/beta/ankieta` – 4 kroki (typ firmy → integracje → brakujące + wydatki → zgody), pasek postępu, „Wstecz”. Po każdym kroku JS zapisuje postęp (`POST /beta/ankieta/postep`) razem z numerem kroku. Bez JS formularz pokazuje wszystkie kroki naraz i działa normalnie.
3. `POST /beta/ankieta` – pełna walidacja po stronie serwera, zapis treści i daty zgód, status `kompletny`, mail double opt-in.
4. `/beta/potwierdz/{token}` → status `potwierdzony`. `/wypisz/{token}` (link w każdym mailu + nagłówek `List-Unsubscribe`) → anonimizacja: e-mail i wolny tekst usunięte, odpowiedzi zostają do statystyk, status `wypisany`.

Ochrona przed spamem: honeypot (`firma_www`) + limity na IP (`config/beta.php`). Bez CAPTCHA.

## Panel wewnętrzny

`/panel` – HTTP Basic Auth, dane w `.env` (`ADMIN_USER`, `ADMIN_PASSWORD`; puste = panel zwraca 404). Pokazuje liczbę zgłoszeń wg statusu, ranking integracji (tylko zgłoszenia kompletne i potwierdzone), podział na typy firm, krok przerwania dla niedokończonych. `/panel/eksport.csv` – pełny eksport (średnik jako separator, BOM dla Excela). Strona publiczna nigdzie nie pokazuje wyników.

## Plausible

Ustaw `PLAUSIBLE_DOMAIN` w `.env`; pusty = skrypt nie jest ładowany. Zdarzenia: `cta_click` (prop `location`), `beta_start` (prop `location`), `beta_step` (prop `step`), `beta_complete`. Domena skryptu jest automatycznie dodawana do CSP.

## Maile i hosting (do zrobienia przed startem)

- Dostawca SMTP z serwerami w UE, konfiguracja w `.env` (`MAIL_*`). `MAIL_FROM_ADDRESS` w domenie produktu.
- DNS domeny: SPF, DKIM, DMARC – bez tego double opt-in trafi do spamu. Po konfiguracji sprawdź dostarczalność na Gmailu i Outlooku.
- HTTPS – HSTS jest ustawiany tylko na żądaniach HTTPS. CSP, X-Frame-Options, Referrer-Policy: `app/Http/Middleware/SecurityHeaders.php`.
- Produkcja: `APP_ENV=production`, `APP_DEBUG=false`, `php artisan config:cache route:cache view:cache`, `npm run build`. Baza SQLite wystarczy na start; MySQL/Postgres przez `DB_*`.
- Brak cookies śledzących (tylko sesja Laravel, niezbędna do ankiety). Jeśli dojdzie piksel Meta, trzeba dodać banner zgód.

## Do uzupełnienia przed startem

- [ ] Nazwa produktu i domena (`site.json → product`)
- [ ] Liczba miejsc w becie (`product.beta_slots`)
- [ ] Cena docelowa i cena bety, brutto/netto (`site.json → pricing`)
- [ ] Szacunki oszczędzonego czasu (`site.json → does.cards[].saved`)
- [ ] Odpowiedzi FAQ (`faq.json`)
- [ ] Dane firmy (`site.json → company`) – trafiają do stopki, klauzuli RODO i treści zgód
- [ ] Polityka prywatności i regulamin bety (`content/legal/`)
- [ ] Zdjęcie (`public/…`, ścieżka w `about.founder_photo`) i bio założyciela
- [ ] Identyfikacja wizualna: kolory i fonty w `resources/css/app.css` (`@theme`), logo w `sections/nav.blade.php`, favicon `public/favicon.svg`
