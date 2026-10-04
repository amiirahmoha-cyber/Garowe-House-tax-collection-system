# GHTCS — Laravel Version (Garowe House Tax Collection System)

⚠️ **MUHIIM:** Faylashan waa koodhka app-ka (Models, Controllers, Views, Routes,
Migrations) — **maaha** Laravel framework-ka oo dhan (packages-ka `vendor/`
folder-ka). Sababtoo ah composer wuxuu u baahan yahay internet si uu
framework-ka uga soo dejiyo, waana in aad ku samaysaa tallaabadan
**computer-kaaga oo internet leh**.

---

## TALLAABADA 1 — Samee Laravel skeleton cusub

Computer-kaaga (mid internet leh), fur CMD/Terminal oo qor:

```bash
composer create-project laravel/laravel ghtcs-laravel
```

Waxay samayn doontaa folder cusub `ghtcs-laravel` oo leh dhammaan
framework-ka Laravel (`vendor/`, `artisan`, iwm).

---

## TALLAABADA 2 — Ku dul-shub fayllada aan kuu sameeyay

Ka koobbi (copy/overwrite) folder-ka ZIP-ka aan kuu soo diray ee la yidhaahdo
`ghtcs-laravel` (kan aad hadda ka akhrinayso README-gan), oo ku dul-dubo
folder-ka Laravel ee cusub ee aad Tallaabada 1 samaysay. Waxaad dul-dubaysaa:

```
app/                    → dul-dub (Models, Controllers, Middleware, Services, Console)
database/migrations/    → dul-dub
database/seeders/       → dul-dub
resources/views/        → dul-dub
routes/web.php          → dul-dub
routes/console.php      → dul-dub
bootstrap/app.php       → dul-dub
config/sms.php          → koobbi (fayl cusub)
public/css/style.css    → koobbi
public/img/logo.png     → koobbi
.env.example            → eeg qiimayaasha, ku daabac .env-kaaga
```

---

## TALLAABADA 3 — Diyaari database-ka

1. Fur phpMyAdmin (ama MySQL Workbench), samee database madhan magaciisu yahay `ghtcs_laravel`
2. Fur `.env` (ku dhex jira folder-ka `ghtcs-laravel`), hubi:
   ```
   DB_DATABASE=ghtcs_laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

---

## TALLAABADA 4 — Amarrada Artisan (terminal-ka, gudaha folder-ka ghtcs-laravel)

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan db:seed --class=SampleDataSeeder   # ikhtiyaari — xog tijaabo ah
php artisan serve
```

Fur browser-ka: **http://localhost:8000**

**Login:**
- Administrator: `admin` / `admin123` (wax kasta buu arki karaa)
- Collector: `collector` / `collector123` (kaliya Dashboard, Owners, Properties, Payments)

---

## Waxa Cusub ee lagu daray (sida aad codsatay)

### 1. Property Classes — bog cusub oo class laga abuuro
`Settings → Property Classes` — waxaad halkaas ka abuuri kartaa class cusub
(magaca + rate per square meter), ma aha kuwo go'an (hardcoded).

### 2. Property Types — bog cusub oo type laga abuuro
`Settings → Property Types` — Residential/Commercial waa horey loo sameeyay,
laakiin waxaad ku dari kartaa nooc kale (tusaale Industrial).

### 3. Villages/Xaafado — bog cusub oo xaafad laga abuuro
`Settings → Villages / Xaafado` — sida Wadajir, Israac, iwm, waxaad ku dari
kartaa xaafado cusub, isla habka Owner-ka loo diiwaan geliyo.

### 4. Canshuurta waxaa laga xisaabiyaa cabirka guriga (square meters)
Marka guri la diiwaan geliyo AMA la generate-gareeyo tax-ka, `annual_tax`
waxaa si **toos ah** looga xisaabiyaa `square_meters × rate_per_sqm` (class-ka
la doortay) — mar walba xogta ugu dambeysa, ma aha qiime hore loo keydiyay.

### 5. Tax Records — meel sanad cusub laga abuuro
Bogga `Tax Records`, waxaad haysataa labo qaybood:
- **View Year** dropdown (sanadyada horey loo generate-gareeyay)
- **Generate for Year** — geli SANAD KASTA (tusaale 2027, 2028...) oo riix
  "Generate Tax" si guryaha oo dhan loo abuuro canshuurtooda sannadkaas.

### 6. Users — Collector waxay arki karaan KALIYA Payments/Properties/Owners
Role-ka `Collector` (ama `Viewer`) marka la login gareeyo:
- **Waxay arki karaan:** Dashboard, Owners, Properties, Payments, Receipts
- **Waa laga qariyay:** Tax Records, Reports, Activity Logs, Users, SMS
  Reminders, Settings (Property Classes/Types/Villages)

Xakamaynta waxaa lagu sameeyay `app/Http/Middleware/RoleMiddleware.php` iyo
`routes/web.php` (`->middleware('role:Administrator')`).

---

## SMS Gateway — sida loo dhigo mid REAL ah

Fur `.env`, ku beddel:
```
SMS_ENABLED=true
SMS_API_URL=<endpoint-ka provider-kaaga>
SMS_API_USERNAME=<username/API key>
SMS_API_PASSWORD=<password/API secret>
```

**Golis Telecom** (Puntland/Garowe) — la xiriir xafiiskooda Bosaso (Biyo
Kulule Road) ama https://golistelecom.com/contact-us/ si aad u weydiisato
Corporate/Business SMS API.

**Ama** isticmaal aggregator (isdiiwaangelin degdeg ah): **D7 Networks**
(d7networks.com) ama **SMS.to** (sms.to) — labaduba waxay taageeraan
shabakadaha Soomaaliya.

Haddii provider-kaagu isticmaalo magacyo field ka duwan, ku beddel
`app/Services/SmsGateway.php` (function `callGateway()`).

---

## Automation — SMS Otomaatig ah maalin kasta

Command-ka `php artisan ghtcs:send-reminders` wuxuu:
1. Astaamaa canshuuraha dhaafay **Overdue**
2. Diraa SMS xasuusin milkiilayaasha canshuurtoodu dhow tahay (7 maalmood)
   ama horeba u dhaaftay

### Sida loo shido si maalin kasta u socoto (Windows)

Fur **Task Scheduler**, samee task cusub:
- Trigger: Daily, 08:00 AM
- Action: Start a Program
  - Program: `C:\path-to\php.exe`
  - Arguments: `artisan schedule:run`
  - Start in: `C:\path-to\ghtcs-laravel`

(Laravel wuxuu isku diyaariyaa jadwalka `routes/console.php` — `schedule:run`
wuxuu hubiyaa maalin kasta in la gaadhay 08:00, kadibna socodsiiyaa
`ghtcs:send-reminders`.)

Waad ku tijaabin kartaa gacanta:
```bash
php artisan ghtcs:send-reminders
```

---

## Dashboard, Receipts, Buttons (View/Edit/Delete)

Dhammaan features-kii hore (Dashboard charts, printable Receipts, action
buttons oo midabaysan — View=cagaar, Edit=cad, Delete=buluug) way sii
jiraan, hadda oo Laravel MVC ku dhisan.
