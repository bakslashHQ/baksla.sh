# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Baksla.sh Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --wait --detach
	@echo "Web server running on"
	@echo "\thttp://localhost"
	@echo "\thttps://localhost"

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

stop: down ## Alias to down

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c) --group smoke
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c) --exclude-group smoke

## —— App \\ ——————————————————————————————————————————————————————————————
app.install: ## Install the application
	@$(call action, Installing PHP dependencies...)
	$(COMPOSER) install --prefer-dist

	@$(call action, Running DB migrations...)
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --all-or-nothing --allow-no-migration

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

tailwind.watch: ## Watch Tailwind CSS
	@$(SYMFONY) tailwind:build --watch

###########
# PHPStan #
###########

## PHPStan - Run PHPStan
phpstan:
	$(PHP) vendor/bin/phpstan analyse --memory-limit=1G

## PHPStan - Run PHPStan and update the baseline
phpstan.generate-baseline:
	$(PHP) vendor/bin/phpstan analyse --memory-limit=1G --generate-baseline
