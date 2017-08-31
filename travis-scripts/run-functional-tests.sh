#!/bin/bash

if [[ $EXTRA_TESTS != *"functional"* ]]; then
  exit 0
fi

echo "<?php define('_PS_MODE_DEV_', false);" > config/defines_custom.inc.php
bash $TRAVIS_BUILD_DIR/travis-scripts/install-prestashop.sh

echo "* Installing functional tests ...";

git clone https://github.com/PrestaShop/PSFunctionalTests.git
cd $TRAVIS_BUILD_DIR/PSFunctionalTests && npm install && cd test/itg/1.6

echo "* Running functional tests ...";
$TRAVIS_BUILD_DIR/PSFunctionalTests/node_modules/mocha/bin/mocha index.webdriverio.js -c --URL=localhost  --MODULE=None --SAUCELABS=None -t 200000
exit $?
