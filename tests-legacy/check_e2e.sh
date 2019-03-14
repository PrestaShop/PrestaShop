#!/bin/bash

bash travis-scripts/run-selenium-tests;
SELENIUM=$?

# run-functional-tests must run before starter theme
bash travis-scripts/run-functional-tests;
FUNCTIONAL=$?

bash travis-scripts/test-startertheme;
STARTER=$?

if [[ "$SELENIUM" == "0" && "$FUNCTIONAL" == "0" && "$STARTER" == "0" ]]; then
  echo -e "\e[92mE2E TESTS OK"
  exit 0;
else
  echo -e "\e[91mE2E TESTS FAILED"
  exit 255;
fi
