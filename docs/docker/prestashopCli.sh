#!/bin/bash

#http://redsymbol.net/articles/unofficial-bash-strict-mode/
set -euo pipefail


#Requirements
# composer git
# ext-curl ext-intl

PS_BRANCH=1.7.3.x
PS_VERSION=1.7.3.2
SHOPNAME=ready173
DB_SERVER=db
DB_USER=prestashop
DB_PASSWORD=prestashop
CURRENT_DIR=`pwd`
PRESTASHOP_DEST="$CURRENT_DIR/nginx_fpm/prestashop"
PRESTASHOP_SOURCE="$CURRENT_DIR/prestashop-source"

function clonePrestaShop() {
    echo "Cloning PrestaShop"
    git clone git@github.com:PrestaShop/PrestaShop.git $PRESTASHOP_SOURCE
    cd $PRESTASHOP_SOURCE
    git checkout $PS_BRANCH
}

function buildRelease() {
    echo "Building new Release: $PS_VERSION from branch  $PS_BRANCH"
    cd $PRESTASHOP_SOURCE
    php tools/build/CreateRelease.php --version="$PS_VERSION" --no-zip --destination-dir=$PRESTASHOP_DEST
    mv $PRESTASHOP_DEST/prestashop/* $PRESTASHOP_DEST/
    rm -Rf $PRESTASHOP_DEST/prestashop
}

clonePrestaShop
buildRelease