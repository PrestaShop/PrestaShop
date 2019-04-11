#!/bin/sh

PS_VERSION="${PS_VERSION:-1.7}"
URL="${URL:-prestashop}"
MODULE="${MODULE:-}"
SAUCELABS="${SAUCELABS:-}"
SCRIPT="${SCRIPT:-index.webdriverio.js}"
DIR="${DIR:-`mktemp -d`}/"
COMMAND_PARAMETERS="--URL=$URL --HEADLESS --DIR=$DIR"

if [ ! -z "$MODULE" ]; then
  COMMAND_PARAMETERS="$COMMAND_PARAMETERS --MODULE=$MODULE"
fi

if [ ! -z "$SAUCELABS" ]; then
  COMMAND_PARAMETERS="$COMMAND_PARAMETERS --SAUCELABS"
fi

if [ ! -z "$SELENIUM_PROTOCOL" ]; then
  COMMAND_PARAMETERS="$COMMAND_PARAMETERS --SELENIUM_PROTOCOL=$SELENIUM_PROTOCOL"
fi

if [ ! -z "$SELENIUM_HOST" ]; then
  COMMAND_PARAMETERS="$COMMAND_PARAMETERS --SELENIUM_HOST=$SELENIUM_HOST"
fi

if [ ! -z "$SELENIUM_PORT" ]; then
  COMMAND_PARAMETERS="$COMMAND_PARAMETERS --SELENIUM_PORT=$SELENIUM_PORT"
fi

if [ ! -z "$TEST_PATH" ]; then
  COMMAND="npm run specific-test"
else
  if [ "$1" = "high" ]; then
    COMMAND="npm run high-test"
    shift
  else
    COMMAND="npm run test"
  fi
fi


cd /var/www/html/tests/E2E/
npm install
echo $COMMAND -- $COMMAND_PARAMETERS $@
$COMMAND -- $COMMAND_PARAMETERS $@
