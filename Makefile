install: composer assets

composer:
	composer install

assets:
	./tools/assets/build.sh
