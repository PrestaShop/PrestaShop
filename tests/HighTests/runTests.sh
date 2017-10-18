#!/usr/bin/env bash

PS_VERSION="${PS_VERSION:-1.7}"
URL="${URL:-prestashop}"
MODULE="${MODULE:-}"
SAUCELABS="${SAUCELABS:-}"
SCRIPT="${SCRIPT:-index.webdriverio.js}"
SELENIUM=""${SELENIUM_HOST:-}""

COMMAND="/tmp/node_modules/mocha/bin/mocha /tmp/test/itg/$PS_VERSION/$SCRIPT --URL=$URL"

if [ -n "$MODULE" ]; then
    COMMAND="$COMMAND --MODULE=$MODULE"
fi

if [ -n "$SAUCELABS" ]; then
    COMMAND="$COMMAND --SAUCELABS"
fi

if [ -n "$SELENIUM" ]; then
    COMMAND="$COMMAND --SELENIUM=$SELENIUM"
fi

echo $COMMAND
$COMMAND
