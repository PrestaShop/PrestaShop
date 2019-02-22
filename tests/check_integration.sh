#!/bin/bash

composer run-script integration-tests --timeout=0;
INTEGRATION=$?

if [[ "$INTEGRATION" == "0" ]]; then
  echo -e "\e[92mINTEGRATION TESTS OK\e[0m"
else
  echo -e "\e[91mINTEGRATION TESTS FAILED\e[0m"
fi
exit $INTEGRATION;
