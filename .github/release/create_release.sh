#!/usr/bin/env bash

if [ -z "$1" ]
  then
    echo "Please provide version number"
fi

version=$1

node replace_version.js --version $version
