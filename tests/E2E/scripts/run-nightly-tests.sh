#!/bin/bash

DIR_PATH=$(mktemp -d)
REPORT_PATH=$(mktemp -p)
BRANCH=$(curl http://metadata.google.internal/computeMetadata/v1/instance/attributes/TRAVIS_BRANCH -H "Metadata-Flavor: Google")
OUTPUT_NAME="$(date +%Y-%m-%d)-${BRANCH}"

if [ -d $DIR_PATH ]; then
  rm -rf $DIR_PATH
fi

git clone https://github.com/PrestaShop/PrestaShop.git $DIR_PATH

cd $DIR_PATH
git checkout $BRANCH
mkdir -p "${DIR_PATH}/campaigns"

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

    cp mochawesome-report/mochawesome.json "${REPORT_PATH}/campaigns/${TEST_PATH//\//-}.json"

    docker-compose down
  fi
done

./scripts/combine-reports.py "${REPORT_PATH}/campaigns" "${REPORT_PATH}/${OUTPUT_NAME}.json"
./node_modules/mochawesome-report-generator/bin/cli.js "${REPORT_PATH}/${OUTPUT_NAME}.json" -o "${REPORT_PATH}/reports"
gsutil cp -r "${REPORT_PATH}/reports" gs://prestashop-core-nightly

sudo halt -p
