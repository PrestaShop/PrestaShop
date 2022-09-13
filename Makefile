install: composer assets

composer:
	composer install

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
