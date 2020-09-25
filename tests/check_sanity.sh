#!/bin/bash

bash travis-scripts/run-sanity-tests;
SANITY=$?


if [[ "$SANITY" == "0" ]]; then
  echo -e "\e[92mSANITY TESTS OK\e[0m"
  exit 0;
else
  echo -e "\e[91mSANITY TESTS FAILED\e[0m"
  exit 255;
fi
