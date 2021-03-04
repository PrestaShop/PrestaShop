#!/bin/bash

###
# This script rebuilds all the static assets, running npm install as needed
#

#http://redsymbol.net/articles/unofficial-bash-strict-mode/
set -euo pipefail
PROJECT_PATH=$(cd "$( dirname "$0" )/../../" && pwd)
ADMIN_DIR="${PROJECT_PATH}/${ADMIN_DIR:-admin-dev}"

if [[ ! -d $ADMIN_DIR ]]; then
  echo "Could not find directory '$ADMIN_DIR'. Make sure to launch this script from the root directory of PrestaShop"
  return 1
fi

function build {
  if [[ -z "$1" ]]; then
    echo "Parameter is empty"
    exit 1
  fi

  pushd $1
  if [[ -d "node_modules" ]]; then
    rm -rf node_modules
  fi

  npm install
  npm run build
  popd
}

echo ">>> Building admin default theme..."
build "$ADMIN_DIR/themes/default"

echo ">>> Building admin new theme..."
build "$ADMIN_DIR/themes/new-theme"

echo ">>> Building core theme assets..."
build "$PROJECT_PATH/themes"

echo ">>> Building classic theme assets..."
build "$PROJECT_PATH/themes/classic/_dev"

echo "All done!"
