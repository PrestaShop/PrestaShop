#!/bin/bash

composer run-script integration-tests --timeout=0;
INTEGRATION=$?

composer run-script integration-behaviour-tests --timeout=0;
BEHAVIOUR=$?

if [[ "$INTEGRATION" == "0" ]]; then
  echo -e "\e[92mINTEGRATION TESTS OK\e[0m"
else
  echo -e "\e[91mINTEGRATION TESTS FAILED\e[0m"
fi

if [[ "$BEHAVIOUR" == "0" ]]; then
  echo -e "\e[92mBEHAVIOUR TESTS OK\e[0m"
else
  echo -e "\e[91mBEHAVIOUR TESTS FAILED\e[0m"
fi

if [[ "$INTEGRATION" == "0" && "$BEHAVIOUR" == "0" ]]; then
  exit 0;
else
  exit 255;
fi
