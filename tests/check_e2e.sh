#!/bin/bash

bash travis-scripts/run-functional-tests;
FUNCTIONAL=$?

if [[ "$FUNCTIONAL" == "0" ]]; then
  echo -e "\e[92mE2E TESTS OK"
  exit 0;
else
  echo -e "\e[91mE2E TESTS FAILED"
  exit 255;
fi
