FROM prestashop/base:7.2-apache

WORKDIR /tmp

ENV PS_HANDLE_DYNAMIC_DOMAIN 0
ENV PS_INSTALL_AUTO 1
ENV PS_COUNTRY fr
ENV TRAVIS_BUILD_DIR /var/www/html
ENV DEBIAN_FRONTEND noninteractive

# Install dependencies
RUN apt-get update
RUN apt-get install -y apt-utils
RUN apt install -y \
    gnupg2 \
    curl \
    git \
    software-properties-common \
    nodejs \
    poppler-utils

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

COPY ["./.docker/prestashop/wait-for-it.sh", "./.docker/prestashop/docker_run.sh", "/tmp/"]

# Repository update
WORKDIR /var/www/html

RUN usermod -u 1000 www-data
CMD /tmp/wait-for-it.sh --timeout=60 --strict db:3306 -- /tmp/docker_run.sh
