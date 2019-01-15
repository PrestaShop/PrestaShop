#!/bin/bash

composer run-script integration-tests --timeout=0;
INTEGRATION=$?

if [[ "$INTEGRATION" == "0" ]]; then
  echo -e "\e[INTEGRATION TESTS OK"
else
  echo -e "\e[91mINTEGRATION TESTS FAILED"
fi
exit $INTEGRATION;
