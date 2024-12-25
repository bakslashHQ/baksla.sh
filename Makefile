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

############
# Refactor #
############

## Refactor - Run all refactor checks
refactor: refactor.back

## Refactor - Run all refactor checks and fix issues
refactor.fix: refactor.back.fix

## Refactor - Run refactor checks for backend
refactor.back:
	$(PHP) vendor/bin/rector process --dry-run

## Refactor - Run refactor checks for backend and fix issues
refactor.back.fix:
	$(PHP) vendor/bin/rector process

################
# Coding style #
################

## Coding style - Run all coding style checks
cs: cs.back cs.front

## Coding style - Run all coding style checks and fix issues
cs.fix: cs.back.fix cs.front.fix

## Coding style - Check backend coding style
cs.back:
	$(PHP) vendor/bin/ecs check
	$(PHP) vendor/bin/twig-cs-fixer

## Coding style - Check backend coding style and fix issues
cs.back.fix:
	$(PHP) vendor/bin/ecs check --fix
	$(PHP) vendor/bin/twig-cs-fixer --fix

## Coding style - Check frontend coding style
cs.front:
ifdef CI
	$(SYMFONY) biomejs:ci . --linter-enabled=false
else
	$(SYMFONY) biomejs:check . --linter-enabled=false
endif

## Coding style - Check frontend coding style and fix issues
cs.front.fix:
	$(SYMFONY) biomejs:check . --linter-enabled=false --write --unsafe

##########
# Linter #
##########

## Linter - Run all linters
lint: lint.back lint.front

## Linter - Run all linters and fix issues
lint.fix: lint.back lint.front.fix

## Linter - Run linters for backend
lint.back:
	$(SYMFONY) lint:container
	$(SYMFONY) lint:xliff translations
	$(SYMFONY) lint:translations
	$(SYMFONY) lint:yaml --parse-tags config
	$(SYMFONY) lint:twig templates
	# TODO: Uncomment when the project has Doctrine entities
	#$(SYMFONY) doctrine:schema:validate

## Linter - Lint front files
lint.front:
ifdef CI
	$(SYMFONY) biomejs:ci . --formatter-enabled=false
else
	$(SYMFONY) biomejs:check . --formatter-enabled=false
endif

## Linter - Lint front files and fix issues
lint.front.fix:
	$(SYMFONY) biomejs:check . --formatter-enabled=false --write

###########
# PHPStan #
###########

## PHPStan - Run PHPStan
phpstan:
	$(PHP) vendor/bin/phpstan analyse --memory-limit=1G

## PHPStan - Run PHPStan and update the baseline
phpstan.generate-baseline:
	$(PHP) vendor/bin/phpstan analyse --memory-limit=1G --generate-baseline

#########
# Tests #
#########

## Tests - Run all tests
test: test.back

## Tests - Run backend tests
test.back: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c) --group smoke
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c) --exclude-group smoke
