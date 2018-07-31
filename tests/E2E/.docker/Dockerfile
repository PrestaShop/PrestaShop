FROM prestashop/prestashop-git:latest

WORKDIR /tmp/

# Install dependencies
RUN apt-get update
RUN apt install -y \
     gnupg2 \
     software-properties-common \
     nodejs


RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
RUN apt-get update
RUN apt-get install -y nodejs
RUN npm i npm@latest -g

# Repository update
WORKDIR /var/www/html
RUN git fetch -p
RUN git checkout $GIT_BRANCH
RUN git pull
RUN /usr/local/bin/composer install

WORKDIR /var/www/html/tests/E2E
RUN npm install

COPY /prestashop/wait-for-it.sh /tmp/
COPY run-tests.sh /tmp/
