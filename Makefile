install: composer assets

composer:
	composer install
	./bin/console cache:clear --no-warmup

assets:
	./tools/assets/build.sh

front-core:
	./tools/assets/build.sh front-core

front-classic:
	./tools/assets/build.sh front-classic

admin-default:
	./tools/assets/build.sh admin-default

admin-new-theme:
	./tools/assets/build.sh admin-new-theme

admin: admin-default admin-new-theme

front: front-core front-classic

cs-fixer:
	./vendor/bin/php-cs-fixer fix

phpstan:
	./vendor/bin/phpstan analyse -c phpstan.neon.dist

scss-fixer:
	cd admin-dev/themes/new-theme && npm run scss-fix
	cd admin-dev/themes/default && npm run scss-fix
	cd themes/classic/_dev && npm run scss-fix

es-linter:
	cd admin-dev/themes/new-theme && npm run lint-fix
	cd admin-dev/themes/default && npm run lint-fix
	cd themes/classic/_dev && npm run lint-fix
	cd themes && npm run lint-fix

docker: docker-override
	docker compose up -d

docker-override:
	cp docker-compose.override.yml.dist docker-compose.override.yml

