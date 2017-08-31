#!/bin/bash

if [[ $EXTRA_TESTS != *"functional"* ]]; then
  exit 0
fi

echo "* Installing functional tests ...";

cd $TRAVIS_BUILD_DIR && echo "<?php\n\ndefine('_PS_MODE_DEV_', false);" > config/defines_custom.inc.php # Disable DEV MODE

git clone https://github.com/PrestaShop/PSFunctionalTests.git
cd $TRAVIS_BUILD_DIR/PSFunctionalTests && npm install && cd test/itg/1.6

echo "* Running functional tests ...";
$TRAVIS_BUILD_DIR/PSFunctionalTests/node_modules/mocha/bin/mocha index.webdriverio.js -c --URL=localhost -t 100000
exit $?
