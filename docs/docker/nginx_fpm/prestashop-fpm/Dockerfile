FROM php:7.1-fpm-alpine
ARG UID=1001

RUN apk add --no-cache \
        tzdata \
        zlib-dev \
        libjpeg-turbo-dev \
        libmcrypt-dev \
		pcre-dev \
		libpng-dev \
		freetype-dev \
		libxml2-dev \
		icu-dev

ENV TZ Europe/Paris

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install iconv intl pdo_mysql mbstring soap gd zip

RUN docker-php-source extract \
  && if [ -d "/usr/src/php/ext/mysql" ]; then docker-php-ext-install mysql; fi \
  && if [ -d "/usr/src/php/ext/mcrypt" ]; then docker-php-ext-install mcrypt; fi \
	&& if [ -d "/usr/src/php/ext/opcache" ]; then docker-php-ext-install opcache; fi \
	&& docker-php-source delete

COPY prestashop-php.ini /usr/local/etc/php/conf.d/prestashop-php.ini

RUN adduser -D -H -u ${UID} prestashop
