help: ## Display this help menu
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: composer assets  ## Install PHP dependencies and build the static assets

composer: ## Install PHP dependencies
	composer install
	./bin/console cache:clear --no-warmup

assets:  ## Rebuilds all the static assets, running npm install-clean as needed
	./tools/assets/build.sh

front-core: ## Building core theme assets
	./tools/assets/build.sh front-core

front-classic: ## Building classic theme assets
	./tools/assets/build.sh front-classic

admin-default: ## Building admin default theme assets
	./tools/assets/build.sh admin-default

admin-new-theme: ## Building admin new theme assets
	./tools/assets/build.sh admin-new-theme

admin-puik-theme:
	./tools/assets/build.sh admin-puik-theme

admin: admin-default admin-new-theme ## Building admin assets

front: front-core front-classic ## Building front assets

cs-fixer: ## Run php-cs-fixer
	./vendor/bin/php-cs-fixer fix

phpstan: ## Run phpstan analysis
	./vendor/bin/phpstan analyse -c phpstan.neon.dist

scss-fixer: ## Run scss-fix
	cd admin-dev/themes/new-theme && npm run scss-fix
	cd admin-dev/themes/default && npm run scss-fix
	cd themes/classic/_dev && npm run scss-fix

es-linter: ## Run lint-fix
	cd admin-dev/themes/new-theme && npm run lint-fix
	cd admin-dev/themes/default && npm run lint-fix
	cd themes/classic/_dev && npm run lint-fix
	cd themes && npm run lint-fix

.PHONY: help install composer assets front-core front-classic admin-default admin-new-theme admin front cs-fixer phpstan scss-fixer es-linter

.DEFAULT_GOAL := install
