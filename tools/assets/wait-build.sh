#!/bin/bash

###
# This script waits for assets to be built by checking the presence of the buildLock file
#

#http://redsymbol.net/articles/unofficial-bash-strict-mode/
set -euo pipefail
PROJECT_PATH=$(cd "$( dirname "$0" )/../../" && pwd)
ADMIN_DIR="${PROJECT_PATH}/${ADMIN_DIR:-admin-dev}"

if [[ ! -d $ADMIN_DIR ]]; then
  echo "Could not find directory '$ADMIN_DIR'. Make sure to launch this script from the root directory of PrestaShop"
  return 1
fi

if test $# -gt 0; then
  case $1 in
    admin-default)
      echo ">>> Waiting for admin default theme..."
      buildLocks="$ADMIN_DIR/themes/default/buildLock"
    ;;
    admin-new-theme)
      echo ">>> Waiting for admin new theme..."
      buildLocks="$ADMIN_DIR/themes/new-theme/buildLock"
    ;;
    front-core)
      echo ">>> Waiting for core theme assets..."
      buildLocks="$PROJECT_PATH/themes/buildLock"
    ;;
    front-classic)
      echo ">>> Waiting for classic theme assets..."
      buildLocks="$PROJECT_PATH/themes/classic/_dev/buildLock"
    ;;
    all)
      echo ">>> Waiting for all assets..."
      buildLocks="$ADMIN_DIR/themes/default/buildLock $ADMIN_DIR/themes/new-theme/buildLock $PROJECT_PATH/themes/classic/_dev/buildLock $PROJECT_PATH/themes/buildLock"
    ;;
    *)
      echo "Unknown asset to wait $1"
      exit 1
    ;;
  esac
else
  echo ">>> Waiting for all assets..."
  buildLocks="$ADMIN_DIR/themes/default/buildLock $ADMIN_DIR/themes/new-theme/buildLock $PROJECT_PATH/themes/classic/_dev/buildLock $PROJECT_PATH/themes/buildLock"
fi

echo Checking for all these lock files $buildLocks
for lockFile in $buildLocks; do
  if [ -f $lockFile ]; then
    echo Wait for $lockFile to be removed
    sleep 1
    while [ -f $lockFile ]; do
      echo $lockFile still present wait a bit more
      sleep 1
    done
  fi
  echo $lockFile is no longer present
done
