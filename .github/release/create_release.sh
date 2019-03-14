#!/usr/bin/env bash

if [ -z "$1" ]
  then
    echo "Please provide version number"
fi

version=$1
rootPath=$(realpath $(pwd)/../..)


if tput setaf 1 &> /dev/null; then
  tput sgr0; # reset colors
  export c_bold=$(tput bold);
  export c_reset=$(tput sgr0);
  export c_purple=$(tput setaf 125);
else
  export c_bold='';
  export c_reset="\e[0m";
  export c_purple="\e[1;35m";
fi;

function e_arrow() { printf "\n\n${c_bold}${c_purple}➜ %s${c_reset}\n" "$@"
}


e_arrow "Replacing version number in files"
node replace_version.js --version $versiong


e_arrow "Downloading CLDR"
node download_cldr.js


e_arrow "Installing PHP dependencies with composer"
cd $rootPath; composer install;


e_arrow "Building assets with NPM"
cd $rootPath; cd themes/classic/_dev; npm update; npm run build;
cd $rootPath; cd admin-dev/themes/new-theme; npm update; npm run build;
cd $rootPath; cd admin-dev/themes/default; npm update; npm run build;


e_arrow "Removing folders and files"
cd $rootPath;

find $rootPath -type d -name "node_modules" -exec rm -rf {} \;
find $rootPath -type d -name ".svn" -exec rm -rf {} \;
find $rootPath -type d -name "tests" -exec rm -rf {} \;

find $rootPath -type f -name ".DS_Store" -exec rm -rf {} \;
find $rootPath -type f -name "*.map" -exec rm -rf {} \;

rm -f .gitignore
rm -f .gitmodules
rm -f .travis.yml

rm -rf var/cache; mkdir var/cache;
rm -rf var/logs; mkdir var/logs;

read -p "Do you want to delete the .git directory? " -n 1 -r
if [[ $REPLY =~ ^[Yy]$ ]]
then
    rm -rf .git
fi
