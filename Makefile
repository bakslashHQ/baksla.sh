ROOT_DIR := $(dir $(realpath $(lastword $(MAKEFILE_LIST))))
include $(ROOT_DIR)/tools/make/text.mk
include $(ROOT_DIR)/tools/make/help.mk

# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

## Docker 🐳 - Builds the Docker images
build:
	@$(DOCKER_COMP) build --pull

## Docker 🐳 - Start the docker hub in detached mode (no logs)
up:
	@$(DOCKER_COMP) up --wait --detach
	@echo "Web server running on"
	@echo "\thttp://localhost"
	@echo "\thttps://localhost"

## Docker 🐳 - Build and start the containers
start: build up

## Docker 🐳 - Stop the docker hub
down:
	@$(DOCKER_COMP) down --remove-orphans

## Docker 🐳 - Stop the docker hub (alias to down)
stop: down

## Docker 🐳 - Show live logs
logs:
	@$(DOCKER_COMP) logs --tail=0 --follow

## Docker 🐳 - Connect to the PHP container
sh:
	@$(PHP_CONT) sh

## Docker 🐳 - Connect to the PHP container via bash
bash:
	@$(PHP_CONT) bash

## App 💻 - Install the application
app.install:
	@$(call action, Installing PHP dependencies...)
	$(COMPOSER) install --prefer-dist

## Composer 🧙 - Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
composer:
	@$(eval c ?=)
	@$(COMPOSER) $(c)

## Symfony 🎵 - List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
sf:
	@$(eval c ?=)
	@$(SYMFONY) $(c)

## Symfony 🎵 - Clear the cache
cc: c=c:c ## Clear the cache
cc: sf

## Symfony 🎵 - Run TailwindCSS watcher
tailwind.watch:
	@$(SYMFONY) tailwind:build --watch

## Symfony 🎵 - Extract tranlations
translation.extract:
	@$(SYMFONY) translation:extract --format yaml --domain messages --force fr
	@$(SYMFONY) translation:extract --format yaml --domain messages --force en

## Quality Assurance 💯 - Run all QA checks
qa: refactor cs lint phpstan test

## Quality Assurance 💯 - Run all QA checks and fix issues
qa.fix: refactor.fix cs.fix lint.fix phpstan test

## —— Refactor 🔃 ———————————————————————————————————————————————————————————————

## Refactor 🔃 - Run all refactor checks
refactor: refactor.back

## Refactor 🔃 - Run all refactor checks and fix issues
refactor.fix: refactor.back.fix

## Refactor 🔃 - Run refactor checks for backend
refactor.back:
	$(PHP) vendor/bin/rector process --dry-run

## Refactor 🔃 - Run refactor checks for backend and fix issues
refactor.back.fix:
	$(PHP) vendor/bin/rector process

## Coding style 📝 - Run all coding style checks
cs: cs.back cs.front

## Coding style 📝 - Run all coding style checks and fix issues
cs.fix: cs.back.fix cs.front.fix

## Coding style 📝 - Run backend coding style checks
cs.back:
	$(PHP) vendor/bin/ecs check
	$(PHP) vendor/bin/twig-cs-fixer

## Coding style 📝 - Run backend coding style checks and fix issues
cs.back.fix:
	$(PHP) vendor/bin/ecs check --fix
	$(PHP) vendor/bin/twig-cs-fixer --fix

## Coding style 📝 - Run frontend coding style checks
cs.front:
	$(SYMFONY) biomejs:download
ifdef CI
	$(PHP_CONT) bin/biome ci . --linter-enabled=false
else
	$(PHP_CONT) bin/biome check . --linter-enabled=false
endif

## Coding style 📝 - Run frontend coding style checks and fix issues
cs.front.fix:
	$(SYMFONY) biomejs:download
	$(PHP_CONT) bin/biome check . --linter-enabled=false --write --unsafe

## Linter ✅ - Run all linters
lint: lint.back lint.front

## Linter ✅ - Run all linters and fix issues
lint.fix: lint.back lint.front.fix

## Linter ✅ - Run backend linters
lint.back:
	$(SYMFONY) lint:container
	$(SYMFONY) lint:xliff translations
	$(SYMFONY) lint:translations --locale en --locale fr
	$(SYMFONY) lint:yaml --parse-tags config
	$(SYMFONY) lint:twig templates

## Linter ✅ - Run frontend linters
lint.front:
	$(SYMFONY) biomejs:download
ifdef CI
	$(PHP_CONT) bin/biome ci . --formatter-enabled=false
else
	$(PHP_CONT) bin/biome check . --formatter-enabled=false
endif

## Linter ✅ - Run frontend linters and fix issues
lint.front.fix:
	$(SYMFONY) biomejs:download
	$(PHP_CONT) bin/biome check . --formatter-enabled=false --write

## PHPStan 🐘 - Run PHPStan
phpstan:
	$(PHP) vendor/bin/phpstan analyse --memory-limit=1G

## PHPStan 🐘 - Run PHPStan and update the baseline
phpstan.generate-baseline:
	$(PHP) vendor/bin/phpstan analyse --memory-limit=1G --generate-baseline

## Tests 🧑‍🔬 - Run all tests
test: test.back

## Tests 🧑‍🔬 - Start backend tests, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
test.back:
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c) --group smoke
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c) --exclude-group smoke
