#syntax=docker/dockerfile:1

ARG PHP_VERSION=8.4
ARG NODE_VERSION=20

# Versions
FROM php:${PHP_VERSION}-apache AS php_dev

WORKDIR /app

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -

# persistent / runtime deps
# hadolint ignore=DL3008
RUN apt-get update && apt-get install --no-install-recommends -y \
	acl \
	file \
	gettext \
	git \
	nodejs \
	&& rm -rf /var/lib/apt/lists/*

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

RUN set -eux; \
	install-php-extensions \
		@composer \
		apcu \
		gd \
		intl \
		opcache \
		pdo_mysql \
		xdebug \
		zip \
	;

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY --link .docker/conf.d/10-app.ini $PHP_INI_DIR/app.conf.d/
COPY --link .docker/conf.d/20-app.dev.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 .docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link .docker/conf.d/vhost.conf /etc/apache2/sites-available/000-default.conf

ENV APP_ENV=dev
ENV XDEBUG_MODE=off

ENTRYPOINT ["docker-entrypoint"]

HEALTHCHECK --start-period=60s CMD curl -f http://localhost || exit 1

CMD ["apache2-foreground"]

# Apache configuration
RUN if [ -x "$(command -v apache2-foreground)" ]; then a2enmod rewrite; fi
