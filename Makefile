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
.PHONY        : help build up start down logs sh composer vendor sf cc test assets assets-dev admin front admin-default admin-new-theme front-core front-classic front-hummingbird install-prestashop

## —— 🎵 🐳 PrestaShop Docker Makefile 🐳 🎵 ———————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	export COMPOSE_BAKE=true
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach --force-recreate

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

## —— Assets 🎨 ———————————————————————————————————————————————————————————————
assets: admin front ## Build all assets
	@$(DOCKER_COMP) exec php tools/assets/build.sh all

assets-dev: ## Run dev-server for assets
	@$(DOCKER_COMP) exec php npx concurrently -c "#93c5fd,#c4b5fd,#fb7185,#fdba74" "cd admin-dev/themes/default && npm run watch" "cd admin-dev/themes/new-theme && npm run watch" "cd themes/classic/_dev && npm run watch" "cd themes/hummingbird && npm run watch" --names=admin-default,admin-new-theme,front-classic,front-hummingbird --kill-others

admin: admin-default admin-new-theme ## Build admin assets

front: front-core front-classic ## Build front assets

admin-default: ## Build azssets for default admin theme
	@$(DOCKER_COMP) exec php tools/assets/build.sh admin-default

admin-new-theme: ## Build azssets for new admin theme
	@$(DOCKER_COMP) exec php tools/assets/build.sh admin-new-theme

front-core: ## Build assets for core theme
	@$(DOCKER_COMP) exec php tools/assets/build.sh front-core

front-classic: ## Build assets for classic theme
	@$(DOCKER_COMP) exec php tools/assets/build.sh front-classic

front-hummingbird: ## Build assets for hummingbird theme
	@$(DOCKER_COMP) exec php tools/assets/build.sh hummingbird

## —— PrestaShop 🛒 ————————————————————————————————————————————————————————————
install-prestashop: ## Install PrestaShop
	@$(DOCKER_COMP) exec php tools/database/install.sh

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## -- Code quality 🧹 ——————————————————————————————————————————————————————————
test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

php-cs-fixer: ## Run php-cs-fixer
	@$(DOCKER_COMP) exec -e APP_ENV=test php vendor/bin/php-cs-fixer fix

php-cs-fixer-dry: ## Run php-cs-fixer with dry-run
	@$(DOCKER_COMP) exec -e APP_ENV=test php vendor/bin/php-cs-fixer fix --dry-run --diff

phpstan: ## Run phpstan analysis
	@$(DOCKER_COMP) exec -e APP_ENV=test php vendor/bin/phpstan analyse -c phpstan.neon.dist

scss-fixer: ## Run scss-fix
	@$(DOCKER_COMP) exec -e APP_ENV=test php cd admin-dev/themes/new-theme && npm run scss-fix
	@$(DOCKER_COMP) exec -e APP_ENV=test php cd admin-dev/themes/default && npm run scss-fix
	@$(DOCKER_COMP) exec -e APP_ENV=test php cd themes/classic/_dev && npm run scss-fix
	@$(DOCKER_COMP) exec -e APP_ENV=test php cd themes/hummingbird && npm run scss-fix

es-linter: ## Run lint-fix
	@$(DOCKER_COMP) exec -e APP_ENV=test php cd admin-dev/themes/new-theme && npm run lint-fix
	@$(DOCKER_COMP) exec -e APP_ENV=test php cd admin-dev/themes/default && npm run lint-fix
	@$(DOCKER_COMP) exec -e APP_ENV=test php cd themes/classic/_dev && npm run lint-fix
	@$(DOCKER_COMP) exec -e APP_ENV=test php cd themes && npm run lint-fix
