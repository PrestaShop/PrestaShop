#!/bin/bash

DIR_PATH=$(mktemp -d)
BRANCH=$(curl http://metadata.google.internal/computeMetadata/v1/instance/attributes/TRAVIS_BRANCH -H "Metadata-Flavor: Google")

if [ -d $DIR_PATH ]; then
  rm -rf $DIR_PATH
fi

git clone https://github.com/PrestaShop/PrestaShop.git $DIR_PATH

cd $DIR_PATH
git checkout $BRANCH

cd "${DIR_PATH}/tests/E2E"
for directory in test/campaigns/full/* ; do
  if [ -d "${directory}" ]; then
    docker stop $(docker ps -qa)

    docker-compose up -d --build --force-recreate

    echo "Wait for docker-compose..."
    sleep 5

    docker-compose exec -e TEST_PATH=${directory/test\/campaigns\//} tests /tmp/wait-for-it.sh --timeout=720 --strict prestashop-web:80 -- bash /tmp/run-tests.sh

    # Push report to gcloud
    # mochawesome-report/mochawesome.html
    # mochawesome-report/mochawesome.json
    docker-compose down
  fi
done

sudo halt -p
