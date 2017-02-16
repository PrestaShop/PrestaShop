#!/usr/bin/env bash

if [ -z "$1" ]
  then
    echo "Please provide version number"
fi

version=$1

node replace_version.js --version $versiong
node download_cldr.js

rootPath=$(realpath $(pwd)/../..)

cd $rootPath; composer install;

cd $rootPath; cd themes/classic/_dev; npm update; npm run build;
cd $rootPath; cd admin-dev/themes/new-theme; npm update; npm run build;
cd $rootPath; cd admin-dev/themes/default; npm update; npm run build;
