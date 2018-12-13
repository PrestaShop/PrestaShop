#!/bin/bash

DIR_PATH=$(mktemp -d)
BRANCH=$(curl http://metadata.google.internal/computeMetadata/v1/instance/attributes/TRAVIS_BRANCH -H "Metadata-Flavor: Google")

if [ -d $DIR_PATH ]; then
  rm -rf $DIR_PATH
fi

git clone https://github.com/PrestaShop/PrestaShop.git $DIR_PATH

cd $DIR_PATH
git checkout $BRANCH
mkdir -p $DIR_PATH/reports

cd "${DIR_PATH}/tests/E2E"
for directory in test/campaigns/full/* ; do
  if [ -d "${directory}" ]; then
    docker stop $(docker ps -qa)

    docker-compose up -d --build --force-recreate

    echo "Run ${TEST_PATH}"
    echo "Wait for docker-compose..."
    sleep 5

    TEST_PATH=${directory/test\/campaigns\//}
    docker-compose exec -e TEST_PATH=$TEST_PATH tests /tmp/wait-for-it.sh --timeout=720 --strict prestashop-web:80 -- bash /tmp/run-tests.sh

    # Push report to gcloud
    cp mochawesome-report/mochawesome.json "${DIR_PATH}/reports/${TEST_PATH//\//-}.json"

    docker-compose down
  fi
done

sudo halt -p
