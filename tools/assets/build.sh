#!/bin/bash

###
# This script rebuilds all the static assets, reinstalling dependencies when they changed
# Usage: ./tools/assets/build.sh [asset-name] [--force] [--force-install]
#   asset-name: admin-default, admin-new-theme, front-core, front-classic, front-hummingbird, or all
#   --force: Force rebuild even if assets already exist
#   --force-install: Force a clean reinstall of node_modules (implies --force)
#

#http://redsymbol.net/articles/unofficial-bash-strict-mode/
set -euo pipefail
PROJECT_PATH=$(cd "$( dirname "$0" )/../../" && pwd)
ADMIN_DIR="${PROJECT_PATH}/${ADMIN_DIR:-admin-dev}"

# Parse command line arguments
FORCE_BUILD=false
FORCE_INSTALL=false
ASSET_NAME=""

for arg in "$@"; do
  case $arg in
    --force)
      FORCE_BUILD=true
      ;;
    --force-install)
      FORCE_INSTALL=true
      # Dependencies are only installed as part of a build, and reinstalling them
      # without rebuilding would leave the previous assets in place, so this implies --force.
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

# Portable sha256 of stdin (sha256sum on Linux, shasum on macOS)
function sha256_stdin {
  if command -v sha256sum > /dev/null 2>&1; then
    sha256sum | cut -d' ' -f1
  else
    shasum -a 256 | cut -d' ' -f1
  fi
}

# Fingerprint of everything the content of node_modules depends on.
#
# - package-lock.json: the resolved dependency tree.
# - package.json: so a dependency bumped without regenerating the lockfile still runs
#   `npm ci`, which is what reports the two files being out of sync.
# - NODE_ENV: npm omits devDependencies when it is `production`, which yields a tree
#   that cannot build at all.
# - platform, libc, Node and npm versions, and whether we are inside the container:
#   native dependencies such as sass-embedded ship a prebuilt per-platform binary, and
#   docker-compose.yml bind-mounts the repository into the container, so the host and
#   the container share a single node_modules directory. `uname -sm` alone does not
#   separate a Linux host from the container running on it.
#
# Prints nothing when a manifest is missing, so install_dependencies falls through to
# `npm ci` and lets it report the real problem.
function install_stamp_value {
  if [[ ! -f package.json || ! -f package-lock.json ]]; then
    return 0
  fi

  {
    sha256_stdin < package.json
    sha256_stdin < package-lock.json
    uname -sm
    node -v
    npm -v
    echo "NODE_ENV=${NODE_ENV:-}"
    if [[ -f /.dockerenv ]]; then echo 'container'; else echo 'host'; fi
    if command -v ldd > /dev/null 2>&1; then
      ldd --version 2>&1 | head -1 || true
    fi
  } | sha256_stdin
}

# Install dependencies only when they are actually out of date.
#
# `npm ci` wipes and repopulates node_modules from scratch, which on large themes means
# tens of thousands of files, so it should only run when something it depends on changed.
# Note: `npm ci` removes node_modules itself, so no explicit `rm -rf` is needed.
function install_dependencies {
  local stamp="node_modules/.ps-install-stamp"
  local expected
  expected=$(install_stamp_value)

  if [[ -n "$expected" && "$FORCE_INSTALL" == "false" && -d "node_modules" && -f "$stamp" && "$(cat "$stamp")" == "$expected" ]]; then
    echo "> Dependencies already up to date, skipping npm ci (use --force-install to reinstall)"
    return 0
  fi

  npm ci
  if [[ -n "$expected" ]]; then
    echo "$expected" > "$stamp"
  fi
}

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

  touch buildLock
  chmod 664 buildLock
  install_dependencies
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
