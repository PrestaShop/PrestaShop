FROM prestashop/base:7.2-apache

WORKDIR /tmp

# Install dependencies
RUN apt-get update
RUN apt install -y \
     gnupg2 \
     curl \
     git \
     software-properties-common \
     nodejs

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

ENV PS_HANDLE_DYNAMIC_DOMAIN 0
ENV PS_INSTALL_AUTO 1
ENV PS_DEV_MODE 1
ENV PS_COUNTRY fr
ENV TRAVIS_BUILD_DIR /var/www/html
ENV DEBIAN_FRONTEND noninteractive

COPY /wait-for-it.sh /tmp/wait-for-it.sh

# Repository update
WORKDIR /var/www/html

RUN usermod -u 1000 www-data

CMD /tmp/wait-for-it.sh --timeout=600 --strict db:3306 -- /tmp/docker_run.sh
