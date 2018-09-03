#!/bin/bash

composer run-script phpunit-legacy --timeout=0;
LEGACY=$?

composer run-script phpunit-admin --timeout=0;
ADMIN=$?

composer run-script phpunit-sf --timeout=0;
SF=$?

composer run-script phpunit-controllers --timeout=0;
CONTROLLERS=$?

if [[ "$LEGACY" == "0" && "$ADMIN" == "0" && "$SF" == "0" && "$CONTROLLERS" == "0" ]]; then
  echo -e "\e[92mPHPUNIT TESTS OK"
  exit 0;
else
  echo -e "\e[91mPHPUNIT TESTS FAILED"
  exit 255;
fi
