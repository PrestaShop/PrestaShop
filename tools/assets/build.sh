#!/bin/bash

###
# This script rebuilds all the static assets, running npm install-clean as needed
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

  npm ci
  npm run build
  popd
}

build_asset() {
  case $1 in
    admin-puik-theme)
      echo ">>> Building admin puik theme..."
      build "$ADMIN_DIR/themes/puik-theme"
    ;;
    admin-default)
      echo ">>> Building admin default theme..."
      build "$ADMIN_DIR/themes/default"
    ;;
    admin-new-theme)
      echo ">>> Building admin new theme..."
      build "$ADMIN_DIR/themes/new-theme"
    ;;
    front-core)
      echo ">>> Building core theme assets..."
      build "$PROJECT_PATH/themes"
    ;;
    front-classic)
      echo ">>> Building classic theme assets..."
      build "$PROJECT_PATH/themes/classic/_dev"
    ;;
    all)
      build_asset admin-puik-theme & build_asset admin-default & build_asset admin-new-theme & build_asset front-core & build_asset front-classic
    ;;
    *)
      echo "Unknown asset to build $1"
    ;;
  esac
}

if test $# -gt 0; then
  build_asset $1
else
  build_asset all
fi

wait
echo "All done!"
