#!/usr/bin/env bash

PS_VERSION="${PS_VERSION:-1.7}"
URL="${URL:-prestashop}"
MODULE="${MODULE:-}"
SAUCELABS="${SAUCELABS:-}"
SCRIPT="${SCRIPT:-index.webdriverio.js}"
COMMAND="npm run test -- --URL=$URL --HEADLESS"

if [ ! -z "$MODULE" ]; then
    COMMAND="$COMMAND --MODULE=$MODULE"
fi

if [ ! -z "$SAUCELABS" ]; then
    COMMAND="$COMMAND --SAUCELABS"
fi

if [ ! -z "$SELENIUM_PROTOCOL" ]; then
    COMMAND="$COMMAND --SELENIUM_PROTOCOL=$SELENIUM_PROTOCOL"
fi

if [ ! -z "$SELENIUM_HOST" ]; then
    COMMAND="$COMMAND --SELENIUM_HOST=$SELENIUM_HOST"
fi

if [ ! -z "$SELENIUM_PORT" ]; then
    COMMAND="$COMMAND --SELENIUM_PORT=$SELENIUM_PORT"
fi

pushd /var/www/html/tests/E2E/
npm install
echo $COMMAND
$COMMAND
