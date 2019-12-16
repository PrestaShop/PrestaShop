#!/bin/bash

bash travis-scripts/run-linkchecker-tests;
LINKCHECKER=$?


if [[ "$LINKCHECKER" == "0" ]]; then
  echo -e "\e[92mLinkChecker TESTS OK\e[0m"
  exit 0;
else
  echo -e "\e[91mLinkChecker TESTS FAILED\e[0m"
  exit 255;
fi
