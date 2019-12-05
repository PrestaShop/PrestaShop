#!/bin/bash

# run-functional-tests must run before starter theme
bash travis-scripts/run-functional-tests;
FUNCTIONAL=$?

if [[ $FUNCTIONAL -ne 0 ]]; then
  echo -e "\e[91mE2E TESTS FAILED\e[0m"
  exit 255;
fi

echo -e "\e[92mE2E TESTS OK\e[0m"
exit 0;
