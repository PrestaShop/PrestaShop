FROM prestashop/base:7.4-apache

WORKDIR /tmp

# Install dependencies
ENV DEBIAN_FRONTEND="noninteractive"
RUN apt-get update
RUN apt-get install -y  \
    apt-utils \
    mailutils
RUN apt install -y \
    gnupg2 \
    curl \
    git \
    software-properties-common \
    poppler-utils

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt update
RUN apt install -y nodejs

COPY ["./.docker/prestashop/wait-for-it.sh", "./.docker/prestashop/docker_run.sh", "/tmp/"]

# Repository update
WORKDIR /var/www/html

RUN usermod -u 1000 www-data
CMD /tmp/wait-for-it.sh --timeout=60 --strict db:3306 -- /tmp/docker_run.sh
