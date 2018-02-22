FROM node:8

RUN apt update && apt install -qq pdftk poppler-utils && rm -rf /var/lib/apt/lists/*

WORKDIR /tmp/

COPY package.json /tmp/
RUN npm install

COPY docker_prestashop/wait-for-it.sh /tmp/
COPY test/ /tmp/test/

