# To run files with the same group as your primary user
ARG VERSION

FROM prestashop/base:$VERSION

EXPOSE 443

ARG GROUP_ID
ARG USER_ID
ARG NODE_VERSION

RUN groupmod -g $GROUP_ID www-data \
  && usermod -u $USER_ID -g $GROUP_ID www-data

COPY .docker/wait-for-it.sh /tmp/

# Install mailutils to make sendmail work
RUN apt update && apt install -y mailutils

ENV NODE_VERSION       $NODE_VERSION

RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer && rm -rf /tmp/composer-setup.php

COPY .docker/docker_run_git.sh /tmp/
CMD ["/tmp/docker_run_git.sh"]
