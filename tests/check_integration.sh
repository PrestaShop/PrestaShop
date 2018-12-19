#!/bin/bash

composer run-script integration-survival-tests --timeout=0;
UNIT=$?

if [[ "$UNIT" == "0" ]]; then
  echo -e "\e[INTEGRATION TESTS OK"
else
  echo -e "\e[91mINTEGRATION TESTS FAILED"
fi
exit $UNIT;
