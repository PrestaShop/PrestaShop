#!/bin/bash

composer run-script unit-tests --timeout=0;
UNIT=$?

if [[ "$UNIT" == "0" ]]; then
  echo -e "\e[92mUNIT TESTS OK\e[0m"
else
  echo -e "\e[91mUNIT TESTS FAILED\e[0m"
fi
exit $UNIT;
