#!/bin/bash

###
# This script rebuilds all the static assets, running npm install as needed
#

#http://redsymbol.net/articles/unofficial-bash-strict-mode/
set -euo pipefail

if [[ ! -d "admin-dev" ]]; then
  echo "Could not find directory 'admin-dev'. Make sure to launch this script from the root directory of PrestaShop"
  return 1
fi

BASE_DIRECTORY=$(pwd)

function build {
  if [[ ! -d "node_modules" ]]; then
    npm install
  fi

  npm run build
}

#echo ">>> Building admin bundle..."

#cd "$BASE_DIRECTORY/admin-dev"

#build

echo ">>> Building admin default theme..."

cd "$BASE_DIRECTORY/admin-dev/themes/default"

build

echo ">>> Building admin new theme..."

cd "$BASE_DIRECTORY/admin-dev/themes/new-theme"

build

echo ">>> Building core theme assets..."

cd "$BASE_DIRECTORY/themes"

build

echo ">>> Building classic theme assets..."

cd "$BASE_DIRECTORY/themes/classic/_dev"

build

echo "All done!"
