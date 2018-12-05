#!/bin/bash

DATE=`date +%Y-%m-%d`
BRANCH=${1:-develop}
TARGET_DIRECTORY=/tmp/prestashop

git clone https://github.com/PrestaShop/PrestaShop.git $TARGET_DIRECTORY
pushd $TARGET_DIRECTORY
git checkout $BRANCH

cd tests/E2E

echo "Running docker-compose build..."
docker-compose build

echo "Running docker-compose up -d..."
docker-compose up -d

echo "Wait for docker-compose..."
sleep 5

docker-compose exec tests /tmp/wait-for-it.sh --timeout=1800 --strict prestashop-web:80 -- bash /tmp/run-tests.sh

popd
