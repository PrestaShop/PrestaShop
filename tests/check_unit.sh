#!/bin/bash

composer run-script unit-tests --timeout=0;
UNIT=$?

if [[ "$UNIT" == "0" ]]; then
  echo -e "\e[UNIT TESTS OK"
  exit 0;
else
  echo -e "\e[91mUNIT TESTS FAILED"
  exit 255;
fi
