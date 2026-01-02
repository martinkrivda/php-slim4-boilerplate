# PHP Slim 4 Boilerplate

Minimalist, production-ready skeleton for a Slim 4 app running on Nginx + PHP-FPM in a single Docker image.

## Features

- Single Docker image with Nginx + PHP-FPM
- Slim 4 front controller with clean routes
- External MariaDB only (no DB provisioning)
- Credentials pulled exclusively from ENV (ready for Vault injection)
- Coolify-friendly (listens on port 80)

## Quickstart: Local with MariaDB (for quick dev only)

Prerequisites (step-by-step):

1. Install Git:
   - macOS (Homebrew): install Homebrew only if it's not available (https://brew.sh):
     `/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"`, then `brew install git`
   - macOS (no Homebrew): `xcode-select --install`
   - Windows: download Git from https://git-scm.com/download/win
   - Linux: install via your package manager (e.g. `apt install git` or `dnf install git`)
2. Install Docker:
   - macOS: download Docker Desktop https://www.docker.com/products/docker-desktop/, then install
   - Windows: Docker Desktop (WSL2 enabled) https://www.docker.com/products/docker-desktop/, then install
   - Linux: install Docker Engine + Compose plugin via https://docs.docker.com/engine/install/

Steps:

1. Clone the repo:

```sh
git clone <repo-url>
cd php-slim4-boilerplate
```

2. Start the stack (no `.env` needed):

```sh
docker compose -f docker-compose.local-with-db.yaml up --build
```

3. Open:
   - `http://localhost:8080/`
   - `http://localhost:8080/health`
   - `http://localhost:8080/db-check`

## Structure

- `public/` - public web root (front controller + assets)
- `src/` - application code (Application/Domain/Infrastructure)
- `app/` - bootstrap, dependencies, routes, middleware
- `config/` - settings
- `var/` - runtime (logs, cache)
- `docs/` - migration guide and docs
- `docker/` - Nginx, PHP config, supervisor, startup

## Environment variables

Required:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

Optional:

- `DB_PORT` (default `3306`)
- `DB_CHARSET` (default `utf8mb4`)
- `APP_LOG_LEVEL` (default `info`)
- `APP_LOG_MAX_FILES` (default `7`, daily rotation, gz compressed)

## Run locally

```sh
cp .env.example .env
# edit .env values

docker build -t php-db-boilerplate .

docker run --rm -p 8080:80 --env-file .env php-db-boilerplate
```

Visit:

- `http://localhost:8080/`
- `http://localhost:8080/health`
- `http://localhost:8080/db-check`

## Docker Compose

Local without MariaDB:

```sh
docker compose -f docker-compose.local.yaml up --build
```

Local with MariaDB (for quick dev only):

```sh
docker compose -f docker-compose.local-with-db.yaml up --build
```

Local dev with live code updates:

```sh
docker compose -f docker-compose.dev.yaml up --build
```

Production / Coolify (example):

```sh
docker compose -f docker-compose.prod.yaml up -d
```

## Code style (PHP_CodeSniffer)

Install dependencies:

```sh
composer install
```

Run checks:

```sh
./vendor/bin/phpcs
```

## Unit tests (PHPUnit)

Install dev dependencies:

```sh
composer install
```

Run tests:

```sh
composer test
```

## External DB only

If your MariaDB already runs elsewhere, use `docker-compose.local.yaml` or the `docker run` example and point `DB_HOST` to your external database host. Do not start the local MariaDB service.

## GUI client (local MariaDB via docker-compose.local-with-db.yaml)

This requires the MariaDB service to be exposed on `127.0.0.1:3306` (already set in `docker-compose.local-with-db.yaml`).

macOS (Sequel Pro):

1. Install Sequel Pro: https://sequelpro.com/download
2. Start the stack:

```sh
docker compose -f docker-compose.local-with-db.yaml up --build
```

3. Connect:
   - Host: `127.0.0.1`
   - Port: `3306`
   - Username: `app`
   - Password: `app`
   - Database: `php-boilerplate-db`

Windows (HeidiSQL):

1. Install HeidiSQL: https://www.heidisql.com/download.php
2. Start the stack:

```sh
docker compose -f docker-compose.local-with-db.yaml up --build
```

3. Create a new MariaDB/MySQL session with:
   - Host: `127.0.0.1`
   - Port: `3306`
   - User: `app`
   - Password: `app`
   - Database: `php-boilerplate-db`

## Create DB and user (MariaDB)

Example SQL to create a database and user for this app:

```sql
CREATE DATABASE `php-boilerplate-db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'app'@'%' IDENTIFIED BY 'change_me';
GRANT ALL PRIVILEGES ON `php-boilerplate-db`.* TO 'app'@'%';
FLUSH PRIVILEGES;
```

## Coolify notes

- Set environment variables in Coolify (or via Vault).
- Point the service to port 80.
- Ensure network access to your external MariaDB instance.

## Vault (example)

This app reads DB credentials only from environment variables. If you use HashiCorp Vault, inject the following variables into the runtime environment (Coolify or sidecar). In Vault you can keep credentials for multiple engines (MariaDB, PostgreSQL) and map only the MariaDB values to this app.

Required:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

Optional:

- `DB_PORT`
- `DB_CHARSET`

Example mapping (pseudo):

```
DB_HOST={{vault:secret/data/db#host}}
DB_NAME={{vault:secret/data/db#name}}
DB_USER={{vault:secret/data/db#user}}
DB_PASS={{vault:secret/data/db#pass}}
```

You can keep separate secrets for other databases, e.g.:

```
DB_HOST={{vault:secret/data/mariadb/app1#host}}
DB_NAME={{vault:secret/data/mariadb/app1#name}}
DB_USER={{vault:secret/data/mariadb/app1#user}}
DB_PASS={{vault:secret/data/mariadb/app1#pass}}
```

If Vault hosts secrets for multiple environments, keep them isolated by environment prefix (e.g. `ofeed`, `chc`, `claudox`, `infra`) to avoid mixing credentials:

```
DB_HOST={{vault:secret/data/ofeed/mariadb/app1#host}}
DB_NAME={{vault:secret/data/ofeed/mariadb/app1#name}}
DB_USER={{vault:secret/data/ofeed/mariadb/app1#user}}
DB_PASS={{vault:secret/data/ofeed/mariadb/app1#pass}}
```
