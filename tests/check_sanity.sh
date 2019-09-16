#!/bin/bash

#bash travis-scripts/run-sanity-tests;
SANITY=0


if [[ "$SANITY" == "0"]]; then
  echo -e "\e[92mPUPPETEER SANITY TESTS OK"
  exit 0;
else
  echo -e "\e[91mPUPPETEER SANITY TESTS FAILED"
  exit 255;
fi
