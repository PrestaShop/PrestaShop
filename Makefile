# Executables (local)
DOCKER_COMP = docker compose
PHP_CONT =
PHP_CONT_WITH_LOGIN = bash

# Determine if we are using docker
DOCKER_RUNNING := $(shell docker compose ps -q 2>/dev/null)
ifneq ($(strip $(DOCKER_RUNNING)),)
	PHP_CONT = $(DOCKER_COMP) exec -T prestashop-git runuser -u www-data -g www-data --
	PHP_CONT_WITH_LOGIN = $(DOCKER_COMP) exec -T prestashop-git runuser -u www-data -g www-data -- bash -l
endif

# Executables (local or docker)
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = install
.PHONY        : help docker-build docker-up docker-start docker-restart docker-down docker-logs docker-sh composer cc test test-unit test-integration test-integration-behaviour test-api-module assets wait-assets admin front admin-default admin-new-theme front-core front-classic front-hummingbird install install-prestashop cs-fixer cs-fixer-dry phpstan scss-fixer es-linter

## —— 🎵 🐳 PrestaShop Docker Makefile 🐳 🎵 ———————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
docker-build: ## Builds the Docker images
	COMPOSE_BAKE=true $(DOCKER_COMP) build --pull --no-cache

docker-up: ## Start the docker hub in detached mode (no logs)
	$(DOCKER_COMP) up --detach --force-recreate --remove-orphans

docker-start: docker-build docker-up ## Build and start the containers

docker-restart: docker-down docker-start ## Restart the docker hub

docker-down: ## Stop the docker hub
	$(DOCKER_COMP) down --remove-orphans

docker-logs: ## Show live logs
	$(DOCKER_COMP) logs --follow

docker-sh: ## Connect to the PHP container via bash so up and down arrows go to previous commands
	@$(DOCKER_COMP) exec -it prestashop-git runuser -u www-data -g www-data -- bash -l

## —— PrestaShop 🛒 ———————————————————————————————————————————————————————————
install: composer cc assets  ## Install PHP dependencies and build the static assets

install-prestashop: ## Install fresh PrestaShop database (requires containers to be running)
	$(PHP_CONT) .docker/install/database.sh

## —— Assets 🎨 ———————————————————————————————————————————————————————————————
assets: ## Build all assets
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh all --force

wait-assets: ## Wait for assets to be built
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/wait-build.sh

admin: ## Build admin assets
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh admin-default --force
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh admin-new-theme --force

front: ## Build front assets
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh front-core --force
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh front-classic --force
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh front-hummingbird --force

admin-default: ## Build assets for default admin theme
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh admin-default --force

admin-new-theme: ## Build assets for new admin theme
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh admin-new-theme --force

front-core: ## Build assets for core theme
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh front-core --force

front-classic: ## Build assets for classic theme
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh front-classic --force

front-hummingbird: ## Build assets for hummingbird theme
	$(PHP_CONT_WITH_LOGIN) ./tools/assets/build.sh front-hummingbird --force

## —— Composer & Symfony 🧙 ————————————————————————————————————————————————————
composer: ## Install PHP dependencies
	$(COMPOSER) install --no-interaction

cc: ## Clear Symfony cache
	$(SYMFONY) cache:clear --no-warmup

## —— Tests 🧪 —————————————————————————————————————————————————————————————————
test: ## Run all tests
	$(COMPOSER) run test-all

test-unit: ## Run unit tests
	$(COMPOSER) run unit-tests

test-integration: ## Run integration tests
	$(COMPOSER) run integration-tests

test-integration-behaviour: ## Run integration behaviour tests
	$(COMPOSER) run integration-behaviour-tests

test-api-module: ## Run api module tests
	$(COMPOSER) run api-module-tests

## -- Code quality 🧹 ——————————————————————————————————————————————————————————
cs-fixer: ## Run php-cs-fixer
	$(COMPOSER) run php-cs-fixer

cs-fixer-dry: ## Run php-cs-fixer with dry-run
	$(COMPOSER) run php-cs-fixer:dry

phpstan: ## Run phpstan analysis
	$(COMPOSER) run phpstan

scss-fixer: ## Run scss-fix
	$(PHP_CONT_WITH_LOGIN) -c "cd admin-dev/themes/new-theme && (test -d node_modules || npm install) && npm run scss-fix"
	$(PHP_CONT_WITH_LOGIN) -c "cd admin-dev/themes/default && (test -d node_modules || npm install) && npm run scss-fix"
	$(PHP_CONT_WITH_LOGIN) -c "cd themes/classic/_dev && (test -d node_modules || npm install) && npm run scss-fix"

es-linter: ## Run lint-fix
	$(PHP_CONT_WITH_LOGIN) -c "cd admin-dev/themes/new-theme && (test -d node_modules || npm install) && npm run lint-fix"
	$(PHP_CONT_WITH_LOGIN) -c "cd admin-dev/themes/default && (test -d node_modules || npm install) && npm run lint-fix"
	$(PHP_CONT_WITH_LOGIN) -c "cd themes/classic/_dev && (test -d node_modules || npm install) && npm run lint-fix"
	$(PHP_CONT_WITH_LOGIN) -c "cd themes && (test -d node_modules || npm install) && npm run lint-fix"
