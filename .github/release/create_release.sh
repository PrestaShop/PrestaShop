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


cd $rootPath;

find $rootPath -type d -name "node_modules" -exec rm -rf {} \;
find $rootPath -type d -name ".svn" -exec rm -rf {} \;
find $rootPath -type d -name "tests" -exec rm -rf {} \;

find $rootPath -type f -name ".DS_Store" -exec rm -rf {} \;
find $rootPath -type f -name "*.map" -exec rm -rf {} \;

rm -f .gitignore
rm -f .gitmodules
rm -f .travis.yml

rm -rf app/cache; mkdir app/cache;
rm -rf app/logs; mkdir app/logs;

read -p "Do you want to delete the .git directory? " -n 1 -r
if [[ $REPLY =~ ^[Yy]$ ]]
then
    rm -rf .git
fi
