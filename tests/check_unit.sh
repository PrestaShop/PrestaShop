#!/bin/bash

composer run-script unit-tests --timeout=0;
UNIT=$?

if [[ "UNIT" == "0" ]]; then
  echo -e "\e[92mPHPUNIT TESTS OK"
  exit 0;
else
  echo -e "\e[91mPHPUNIT TESTS FAILED"
  exit 255;
fi
