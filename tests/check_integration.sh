#!/bin/bash

composer run-script integration-domain-tests --timeout=0;
DOMAIN=$?

if [[ "$DOMAIN" == "0" ]]; then
  echo -e "\e[92mINTEGRATION TESTS OK"
else
  echo -e "\e[91mINTEGRATION TESTS FAILED"
fi

exit $DOMAIN

