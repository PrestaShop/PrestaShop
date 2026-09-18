#!/bin/bash

###
# This script rebuilds all the static assets, reinstalling dependencies when they changed
# Usage: ./tools/assets/build.sh [asset-name] [--force] [--force-install] [--watch]
#   asset-name: admin-default, admin-new-theme, front-core, front-classic, front-hummingbird, or all
#   --force: Force rebuild even if assets already exist
#   --force-install: Force a clean reinstall of node_modules (implies --force)
#   --watch: Rebuild the given asset on every change instead of building once.
#            Requires a single asset name, and writes development assets.
#            Incompatible with --force, which a watch makes meaningless.
#

#http://redsymbol.net/articles/unofficial-bash-strict-mode/
set -euo pipefail
PROJECT_PATH=$(cd "$( dirname "$0" )/../../" && pwd)
ADMIN_DIR="${PROJECT_PATH}/${ADMIN_DIR:-admin-dev}"

# Parse command line arguments
FORCE_BUILD=false
FORCE_INSTALL=false
WATCH_MODE=false
# Tracked apart from FORCE_BUILD, which --force-install also sets: only an explicit --force
# is meaningless next to --watch.
FORCE_REQUESTED=false
ASSET_NAME=""

for arg in "$@"; do
  case $arg in
    --force)
      FORCE_BUILD=true
      FORCE_REQUESTED=true
      ;;
    --watch)
      WATCH_MODE=true
      ;;
    --force-install)
      FORCE_INSTALL=true
      # Dependencies are only installed as part of a build, and reinstalling them
      # without rebuilding would leave the previous assets in place, so this implies --force.
      FORCE_BUILD=true
      ;;
    --*)
      echo "Unknown option $arg"
      exit 1
      ;;
    *)
      if [[ -z "$ASSET_NAME" ]]; then
        ASSET_NAME="$arg"
      else
        echo "Unexpected argument $arg, only one asset name is accepted"
        exit 1
      fi
      ;;
  esac
done

# A watch always rebuilds, so --force says nothing. Reject the pair rather than ignore it,
# the same way an unknown option is rejected. --force-install does apply: it reaches
# install_dependencies before the watch starts.
if [[ "$WATCH_MODE" == "true" && "$FORCE_REQUESTED" == "true" ]]; then
  echo "--force has no meaning with --watch, which always rebuilds. Use --force-install to reinstall node_modules."
  exit 1
fi

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
    # Tolerate a missing toolchain: the point here is to fingerprint it, and letting
    # `npm ci` fail with its own message beats aborting mid-fingerprint under `set -e`.
    node -v || true
    npm -v || true
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

# Maps an asset name to the directory holding its package.json.
function asset_dir {
  case $1 in
    admin-default)     echo "$ADMIN_DIR/themes/default" ;;
    admin-new-theme)   echo "$ADMIN_DIR/themes/new-theme" ;;
    front-core)        echo "$PROJECT_PATH/themes" ;;
    front-classic)     echo "$PROJECT_PATH/themes/classic/_dev" ;;
    front-hummingbird) echo "$PROJECT_PATH/themes/hummingbird" ;;
    *) return 1 ;;
  esac
}

ASSET_NAMES="admin-default, admin-new-theme, front-core, front-classic, front-hummingbird"

# Watch mode leaves development assets on disk. This marker records that, so the
# "already built" check below does not mistake them for a finished build.
#
# It lives at the theme root, not under node_modules: `npm ci` wipes node_modules, which
# would drop the marker while the development assets it describes are still in place.
DEV_BUILD_MARKER=".ps-dev-build"

# Rebuild one asset on every change.
#
# Deliberately does not go through build(): a watch never returns, so it must not create
# buildLock, which wait-build.sh and the container entrypoint wait on.
function watch_asset {
  local dir
  if ! dir=$(asset_dir "${1:-}"); then
    echo "--watch needs a single asset name among: $ASSET_NAMES"
    exit 1
  fi
  if [[ ! -d $dir ]]; then
    echo "$dir folder not found"
    exit 1
  fi

  pushd "$dir"
  install_dependencies
  touch "$DEV_BUILD_MARKER"
  echo ">>> Watching $1 — press Ctrl+C to stop"
  echo "!!! Watch produces unminified development assets. Run a normal build before committing or releasing."

  # NODE_ENV is scoped to this command on purpose. The classic theme resolves its mode
  # from NODE_ENV rather than from --mode, so without this a shell exporting
  # NODE_ENV=production would silently run a minified watch. The other themes pin --mode
  # on the command line, which wins, so this is a no-op for them.
  #
  # Exporting it instead would change install_stamp_value and make every switch between
  # build and watch reinstall node_modules.
  local status=0
  NODE_ENV=development npm run watch || status=$?
  popd

  # 130 is Ctrl+C, which is how a watch is meant to end.
  if [[ "$status" -ne 0 && "$status" -ne 130 ]]; then
    return "$status"
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
  # wait-build.sh and the container entrypoint block until this file disappears, so it
  # must not survive a failed install or build.
  local lock="$PWD/buildLock"
  trap "rm -f '$lock'" EXIT

  install_dependencies
  npm run build
  rm -f "$DEV_BUILD_MARKER"
  rm -f "$lock"
  trap - EXIT
  popd
}

# Check if asset needs to be built
should_build_asset() {
  local asset_type=$1

  if [[ "$FORCE_BUILD" == "true" ]]; then
    return 0
  fi

  # Assets left by a watch are development builds; the sentinel files below exist but
  # must not count as finished output.
  local dir
  if dir=$(asset_dir "$asset_type") && [[ -f "$dir/$DEV_BUILD_MARKER" ]]; then
    echo "> $asset_type currently holds a development build from --watch, rebuilding"
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
  esac
}

build_asset() {
  case $1 in
    all)
      build_asset admin-default & build_asset admin-new-theme & build_asset front-core & build_asset front-classic & build_asset front-hummingbird
    ;;
    admin-default|admin-new-theme|front-core|front-classic|front-hummingbird)
      if should_build_asset "$1"; then
        echo ">>> Building $1 assets..."
        build "$(asset_dir "$1")"
      else
        echo "> $1 already built (use --force to rebuild)"
      fi
    ;;
    *)
      echo "Unknown asset to build $1"
      echo "Available assets: $ASSET_NAMES, all"
      echo "Use --force to rebuild even if assets already exist"
      exit 1
    ;;
  esac
}

if [[ "$WATCH_MODE" == "true" ]]; then
  watch_asset "$ASSET_NAME"
  exit 0
fi

if [[ -n "$ASSET_NAME" ]]; then
  build_asset "$ASSET_NAME"
else
  build_asset all
fi

wait
echo "All done!"
