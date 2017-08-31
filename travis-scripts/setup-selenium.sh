#!/bin/bash

if [[ $EXTRA_TESTS != *"functional"* ]]; then
  exit 0
fi

echo "* Preparing Selenium ...";

/sbin/start-stop-daemon --start --quiet --pidfile /tmp/custom_xvfb_10.pid --make-pidfile --background --exec /usr/bin/Xvfb -- :10 -ac -screen 0 1600x1200x16

npm install selenium-standalone@latest
cd node_modules/selenium-standalone/bin/
./selenium-standalone install --silent
DISPLAY=:10 ./selenium-standalone start &> /dev/null &

status=$?
cd $TRAVIS_BUILD_DIR
exit $status
