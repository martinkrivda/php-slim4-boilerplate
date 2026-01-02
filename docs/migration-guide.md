# Migration guide: procedural PHP -> Slim 4

Tento navod popisuje krok za krokem, jak prevest beznou proceduralni PHP aplikaci do frameworku Slim 4. Je zamereny na male a stredni projekty, kde chcete udrzet jednoduchost, ale ziskat jednotny front controller, routovani a lepsi strukturu.

## 1) Zmapuj soucasny stav

- Seznam vsech vstupnich souboru (`index.php`, `health.php`, `db-check.php`, apod.)
- Vypis sdilene include soubory (DB, env, helpers)
- Zkontroluj pristup k env promennym a error handling

## 2) Vytvor cilovou strukturu

Doporucena struktura pro Slim 4 (Clean Architecture styl):

```
public/          # public web root (front controller + assets)
src/             # aplikacni kod
  Application/   # HTTP akcni vrstvy, response helpery
  Domain/        # use cases / business logika
  Infrastructure/# DB, externi sluzby, persistence
  Support/       # pomocne utility
app/             # bootstrap, dependencies, routes, middleware
config/          # settings
var/             # runtime (logs, cache)
docs/            # dokumentace
```

## 3) Nainstaluj Slim 4 zavislosti

Do `composer.json` pridej:

- `slim/slim`
- `slim/psr7`
- `php-di/php-di` (doporučeno pro DI container)

Pak spust:

```
composer install
```

## 4) Vytvor front controller

V `app/bootstrap.php` vytvor vstupni bod, ktery:

- nacte autoload
- postavi DI container
- prida middleware
- nacte routes
- spusti aplikaci

## 5) Presun sdileneho kodu do `src/`

Proceduralni funkce rozdel na tridy:

- `Env` helper -> `src/Support/Env.php`
- DB konektor -> `src/Infrastructure/Database.php`
- HTML helper -> `src/Support/Html.php`

Tento krok zjednodusi testovani a pozdejsi refaktoring.

## 6) Nahrazeni endpointu routami

Kazdy soubor typu `health.php` preved na action tridu:

- `src/Action/HealthAction.php`
- `src/Action/DbCheckAction.php`
- `src/Action/HomeAction.php`

Routy zaregistruj v `app/routes.php`.

## 7) Middleware a error handling

V `app/middleware.php` zapni:

- routing middleware
- error middleware (s `displayErrorDetails` podle env)

Pokud chces zakladni logovani, pridej middleware pro logovani requestu a loguj do `var/logs/app.log`.

## 8) Uprav webserver

Nginx musi smerovat vsechny pozadavky na `public/index.php`:

```
location / {
  try_files $uri /index.php$is_args$args;
}
```

## 9) Testy a kontrola

- uprav bootstrap v `phpunit.xml` na `vendor/autoload.php`
- aktualizuj testy, aby pouzivaly nove tridy
- over, ze endpointy vraci stejne vysledky jako pred migraci

## 10) Doporučeny navrhovy vzor pro mensi projekty

Pro projekty tohoto rozsahu je prakticky jednoduchy vzor:

- **MVP (Model-View-Presenter)**: Action/Presenter pripravuje data, View je render HTML
- **Action-Service-Repository**: Action je jen HTTP vrstva, logika je ve Service, data v Repository

Minimalni a dobre udrzovatelny pristup:

1. `Action` pouze mapuje request -> service -> response
2. `Service` obsahuje doménovou logiku
3. `Repository` resi pristup k DB

Pokud jde o maly projekt bez slozite logiky, staci `Action + Infrastructure` vrstvy a lehke helpery.

## 11) Postupny pristup (bez velkeho big-bang refaktoru)

- zacni s jednim endpointem (napr. `/health`)
- pridavej routes postupne
- stare `*.php` soubory muzes ponechat, ale doporucuje se je postupne odstranit

---

Tato repo uz obsahuje Slim 4 strukturu, DI container a ukazkove endpointy (`/health`, `/db-check`). Pouzij ji jako referenci pro migraci dalsich casti aplikace.
