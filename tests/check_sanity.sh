#!/bin/bash

bash travis-scripts/run-sanity-tests;
SANITY=$?


if [[ "$SANITY" == "0" ]]; then
  echo -e "\e[92mPUPPETEER SANITY TESTS OK\e[0m"
  exit 0;
else
  echo -e "\e[91mPUPPETEER SANITY TESTS FAILED\e[0m"
  exit 255;
fi
