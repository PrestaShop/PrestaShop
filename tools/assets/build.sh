#!/bin/bash

###
# This script rebuilds all the static assets, running npm install-clean as needed
# Usage: ./tools/assets/build.sh [asset-name] [--force]
#   asset-name: admin-default, admin-new-theme, front-core, front-classic, front-hummingbird, or all
#   --force: Force rebuild even if assets already exist
#

#http://redsymbol.net/articles/unofficial-bash-strict-mode/
set -euo pipefail
PROJECT_PATH=$(cd "$( dirname "$0" )/../../" && pwd)
ADMIN_DIR="${PROJECT_PATH}/${ADMIN_DIR:-admin-dev}"

# Parse command line arguments
FORCE_BUILD=false
ASSET_NAME=""

for arg in "$@"; do
  case $arg in
    --force)
      FORCE_BUILD=true
      ;;
    *)
      if [[ -z "$ASSET_NAME" ]]; then
        ASSET_NAME="$arg"
      fi
      ;;
  esac
done

if [[ ! -d $ADMIN_DIR ]]; then
  echo "Could not find directory '$ADMIN_DIR'. Make sure to launch this script from the root directory of PrestaShop"
  return 1
fi

function build {
  if [[ -z "$1" ]]; then
    echo "Parameter is empty"
    exit 1
  fi
  if [[ ! -d $1 ]]; then
     echo $1 folder not found
     exit 1
  fi

  pushd $1
  if [[ -d "node_modules" ]]; then
    rm -rf node_modules
  fi

  touch buildLock
  chmod 664 buildLock
  npm ci
  npm run build
  rm buildLock
  popd
}

# Check if asset needs to be built
should_build_asset() {
  local asset_type=$1
  
  if [[ "$FORCE_BUILD" == "true" ]]; then
    return 0
  fi
  
  case $asset_type in
    admin-default)
      [[ ! -f "$ADMIN_DIR/themes/default/public/theme.css" ]]
      ;;
    admin-new-theme)
      [[ ! -f "$ADMIN_DIR/themes/new-theme/public/theme.css" ]]
      ;;
    front-core)
      [[ ! -f "$PROJECT_PATH/themes/core.js" ]]
      ;;
    front-classic)
      [[ ! -f "$PROJECT_PATH/themes/classic/assets/css/theme.css" ]]
      ;;
    front-hummingbird)
      [[ ! -f "$PROJECT_PATH/themes/hummingbird/assets/css/theme.css" ]]
      ;;
    *)
      return 0
      ;;
  esac
}

build_asset() {
  case $1 in
    admin-default)
      if should_build_asset "admin-default"; then
        echo ">>> Building admin default theme..."
        build "$ADMIN_DIR/themes/default"
      else
        echo "> Admin default theme already exists (use --force to rebuild)"
      fi
    ;;
    admin-new-theme)
      if should_build_asset "admin-new-theme"; then
        echo ">>> Building admin new theme..."
        build "$ADMIN_DIR/themes/new-theme"
      else
        echo "> Admin new theme already exists (use --force to rebuild)"
      fi
    ;;
    front-core)
      if should_build_asset "front-core"; then
        echo ">>> Building core theme assets..."
        build "$PROJECT_PATH/themes"
      else
        echo "> Front core already exists (use --force to rebuild)"
      fi
    ;;
    front-classic)
      if should_build_asset "front-classic"; then
        echo ">>> Building classic theme assets..."
        build "$PROJECT_PATH/themes/classic/_dev"
      else
        echo "> Front classic already exists (use --force to rebuild)"
      fi
    ;;
    front-hummingbird)
      if should_build_asset "front-hummingbird"; then
        echo ">>> Building hummingbird theme assets..."
        build "$PROJECT_PATH/themes/hummingbird"
      else
        echo "> Front hummingbird already exists (use --force to rebuild)"
      fi
    ;;
    all)
      build_asset admin-default & build_asset admin-new-theme & build_asset front-core & build_asset front-classic & build_asset front-hummingbird
    ;;
    *)
      echo "Unknown asset to build $1"
      echo "Available assets: admin-default, admin-new-theme, front-core, front-classic, front-hummingbird, all"
      echo "Use --force to rebuild even if assets already exist"
      ;;
  esac
}

if [[ -n "$ASSET_NAME" ]]; then
  build_asset "$ASSET_NAME"
else
  build_asset all
fi

wait
echo "All done!"
