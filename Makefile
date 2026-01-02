APP_NAME=php-db-boilerplate

COMPOSE_LOCAL=docker-compose.local.yaml
COMPOSE_LOCAL_DB=docker-compose.local-with-db.yaml
COMPOSE_DEV=docker-compose.dev.yaml

.PHONY: help
help:
	@printf "Targets:\n"
	@printf "  make up-local        Start app only (no DB)\n"
	@printf "  make up-local-db     Start app + MariaDB\n"
	@printf "  make up-dev          Start dev stack with code mounts\n"
	@printf "  make down            Stop and remove containers\n"
	@printf "  make logs            Tail app logs\n"
	@printf "  make build           Build app image\n"
	@printf "  make sh              Shell into app container\n"
	@printf "  make db-shell        Shell into MariaDB container\n"
	@printf "  make phpcs           Run PHP_CodeSniffer\n"
	@printf "  make clean           Remove containers and volumes (dev/local)\n"

.PHONY: up-local
up-local:
	docker compose -f $(COMPOSE_LOCAL) up --build --remove-orphans

.PHONY: up-local-db
up-local-db:
	docker compose -f $(COMPOSE_LOCAL_DB) up --build --remove-orphans

.PHONY: up-dev
up-dev:
	docker compose -f $(COMPOSE_DEV) up --build --remove-orphans

.PHONY: down
down:
	docker compose -f $(COMPOSE_LOCAL) down
	docker compose -f $(COMPOSE_LOCAL_DB) down
	docker compose -f $(COMPOSE_DEV) down

.PHONY: logs
logs:
	docker logs -f $(APP_NAME)

.PHONY: build
build:
	docker build -t $(APP_NAME) .

.PHONY: sh
sh:
	docker exec -it $(APP_NAME) sh

.PHONY: db-shell
db-shell:
	docker exec -it $(APP_NAME)-mariadb mariadb -uapp -papp $(shell grep ^DB_NAME .env 2>/dev/null | cut -d= -f2)

.PHONY: phpcs
phpcs:
	composer phpcs

.PHONY: clean
clean:
	docker compose -f $(COMPOSE_LOCAL) down -v
	docker compose -f $(COMPOSE_LOCAL_DB) down -v
	docker compose -f $(COMPOSE_DEV) down -v
