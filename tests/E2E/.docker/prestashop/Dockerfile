FROM prestashop/prestashop-git:latest

ENV PS_HANDLE_DYNAMIC_DOMAIN 0
ENV PS_INSTALL_AUTO 1
ENV PS_DEV_MODE 1
ENV PS_COUNTRY fr
ENV TRAVIS_BUILD_DIR /var/www/html
ENV DEBIAN_FRONTEND noninteractive

COPY /wait-for-it.sh /tmp/wait-for-it.sh

# Repository update
WORKDIR /var/www/html
RUN git fetch -p
RUN git checkout $GIT_BRANCH
RUN git pull
RUN /usr/local/bin/composer install
