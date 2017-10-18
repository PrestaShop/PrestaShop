FROM node:8

WORKDIR /tmp/

COPY package.json /tmp/
RUN npm install

COPY docker_prestashop/wait-for-it.sh /tmp/
COPY runTests.sh /tmp/
COPY test/ /tmp/test/

CMD /tmp/runTests.sh
