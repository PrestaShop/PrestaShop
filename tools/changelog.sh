#!/bin/bash
set -e

if [[ -z "$1" ]]
then
    echo "You should specify a <remote/branch> (origin/develop, mbiloe/1.6.0.x, foo/1.7.0.x) as first argument";
    echo "Use: ./changelog.sh origin/1.7.0.x 20";
    exit;
fi;

if [[ -z "$2" ]]
then
    echo "You should specify a number of commits as second argument";
    echo "Use: ./changelog.sh origin/1.7.0.x 20";
    exit;
fi;

echo "# CHANGELOG";

git log $1 -$2 -E --grep="(BO|CO|IN|TE):" --pretty=format:'* %s <%an> [%h](https://github.com/PrestaShop/PrestaShop/commit/%h)' | sort