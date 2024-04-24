# To run files with the same group as your primary user
ARG VERSION

FROM prestashop/base:$VERSION

EXPOSE 443

ARG GROUP_ID
ARG USER_ID
ARG NODE_VERSION
ARG INSTALL_XDEBUG


RUN groupmod -g $GROUP_ID www-data \
  && usermod -u $USER_ID -g $GROUP_ID www-data

COPY .docker/wait-for-it.sh /tmp/

# Install mailutils to make sendmail work
RUN apt update && apt install -y mailutils

ENV NODE_VERSION       $NODE_VERSION

RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer && rm -rf /tmp/composer-setup.php

RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
        pecl install xdebug-3.1.3 \
        && docker-php-ext-enable xdebug \
        && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi

COPY .docker/docker_run_git.sh /tmp/
CMD ["/tmp/docker_run_git.sh"]
