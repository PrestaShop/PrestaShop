FROM prestashop/base:7.2-apache

WORKDIR /tmp

ENV DEBIAN_FRONTEND noninteractive

# Install dependencies
RUN apt-get update
RUN apt-get install -y apt-utils
RUN apt install -y \
    gnupg2 \
    curl \
    git \
    software-properties-common \
    poppler-utils

RUN rm -rf /var/lib/apt/lists/*

RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
RUN apt-get update
RUN apt-get install -y nodejs npm

# Repository update
WORKDIR /var/www/html/tests/E2E

RUN usermod -u 1000 www-data

COPY /prestashop/wait-for-it.sh /tmp/
COPY run-tests.sh /tmp/
