#!/bin/sh

cd /var/www/html/tests/E2E

npm install --unsafe-perm \
	&& npm upgrade \
	&& npm run install

exit $?
