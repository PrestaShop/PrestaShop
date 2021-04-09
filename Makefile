install: composer assets

composer:
	composer install --ansi --prefer-dist --no-interaction --no-progress --classmap-authoritative

assets:
	./tools/assets/build.sh
