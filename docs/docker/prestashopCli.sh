#!/bin/bash

#http://redsymbol.net/articles/unofficial-bash-strict-mode/
set -euo pipefail


#Requirements
# composer git
# ext-curl ext-intl

PS_BRANCH=develop
PS_VERSION=1.7.4.0
DB_SERVER=db
DB_USER=prestashop
DB_PASSWORD=prestashop
CURRENT_DIR=`pwd`
PRESTASHOP_SOURCE="$CURRENT_DIR/prestashop-source"
PRESTASHOP_DEST="$CURRENT_DIR/nginx_fpm/prestashop"

function usage {
    echo "Usage: $0 [option...]"
    echo ""
    echo "Usage examples:"
    echo ""
    echo "Clone Prestashop from github to $PRESTASHOP_SOURCE directory:"
    echo ""
    echo "$0 -c "
    echo ""
    echo "Build version 1.7.4.0 from branch 1.7.4.x and deploy it to $CURRENT_DIR/nginx_fpm_supervisord/prestashop"
    echo ""
    echo "$0 -b 1.7.4.x -v 1.7.4.0 -d nginx_fpm_supervisord"
    echo ""
}


function clonePrestaShop() {
    echo "Cloning PrestaShop"
    git clone git@github.com:PrestaShop/PrestaShop.git $PRESTASHOP_SOURCE
}

function buildRelease() {
    echo "Building new Release: $PS_VERSION from branch  $PS_BRANCH"
    cd $PRESTASHOP_SOURCE
    git checkout $PS_BRANCH
    php tools/build/CreateRelease.php --version="$PS_VERSION" --no-zip --destination-dir=$PRESTASHOP_DEST
    mv $PRESTASHOP_DEST/prestashop/* $PRESTASHOP_DEST/
    rm -Rf $PRESTASHOP_DEST/prestashop
}

function __main() {

    local positional=()
    while [[ $# -gt 0 ]]
    do
    key="$1"
    case $key in
        -b|--branch)
            PS_BRANCH=$2
            shift 2
        ;;
        -c|--clone)
            clonePrestaShop
            shift 2
        ;;
        -v|--version)
            PS_VERSION=$2
            shift 2
        ;;
        -d|--destination)
            PRESTASHOP_DEST="$CURRENT_DIR/$2/prestashop"
            shift 2
        ;;
        -?|--help)
            usage
            shift
            exit 0;
        ;;
        --debug)
            set -x
            shift
        ;;
        *)
        positional+=("$1")
        shift
        ;;
    esac
    done
    set -- "${positional[@]:-}"

    buildRelease
}

__main "$@"