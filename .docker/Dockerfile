# To run files with the same group as your primary user
ARG VERSION="8.1-apache"

FROM prestashop/base:$VERSION

# Declare a new time the ARG VERSION
## Because "An ARG declared before a FROM is outside of a build stage, so it can't be used in any instruction after a FROM."
ARG VERSION

EXPOSE 443

ARG GROUP_ID
ARG USER_ID
ARG NODE_VERSION
ARG INSTALL_XDEBUG
ARG TIMEZONE

ENV TZ=$TIMEZONE
ENV NODE_VERSION=$NODE_VERSION

RUN groupmod -g $GROUP_ID www-data \
  && usermod -u $USER_ID -g $GROUP_ID www-data

# Install mailutils to make sendmail work
# Install tzdata to define the timezone
RUN apt update && apt install -y mailutils tzdata

## Define timezone
RUN ln -sf /usr/share/zoneinfo/$TIMEZONE /etc/localtime \
  && echo $TZ > /etc/timezone

RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer && rm -rf /tmp/composer-setup.php

RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
    pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
fi

COPY --link --chmod=755 .docker/docker_run_git.sh /tmp/
COPY --link --chmod=755 .docker/wait-for-it.sh /tmp/
COPY --link --chmod=755 .docker/install /var/www/html/.docker/install

CMD ["/tmp/docker_run_git.sh"]
