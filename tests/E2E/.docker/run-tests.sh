#!/usr/bin/env bash

PS_VERSION="${PS_VERSION:-1.7}"
URL="${URL:-prestashop}"
MODULE="${MODULE:-}"
SAUCELABS="${SAUCELABS:-}"
SCRIPT="${SCRIPT:-index.webdriverio.js}"
SELENIUM=""${SELENIUM_URL:-}""

COMMAND="npm run test -- --URL=$URL --HEADLESS"

if [ ! -z "$MODULE" ]; then
    COMMAND="$COMMAND --MODULE=$MODULE"
fi

if [ ! -z "$SAUCELABS" ]; then
    COMMAND="$COMMAND --SAUCELABS"
fi

if [ ! -z "$SELENIUM" ]; then
    COMMAND="$COMMAND --SELENIUM=$SELENIUM"
fi

pushd /var/www/html/tests/E2E/
echo $COMMAND
$COMMAND
