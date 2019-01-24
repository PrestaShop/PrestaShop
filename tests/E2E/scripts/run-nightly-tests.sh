#!/bin/bash
#
# DO NOT EXECUTE THIS SCRIPT ON YOUR COMPUTER
#
# This script exists only because of Google compute instance.
# Otherwise it will shutdown your computer.
#

set -x

BRANCH=$(curl http://metadata.google.internal/computeMetadata/v1/instance/attributes/TRAVIS_BRANCH -H "Metadata-Flavor: Google")
DIR_PATH=$(mktemp -d)
REPORT_PATH="${DIR_PATH}/campaigns"
REPORT_OUTPUT_NAME="$(date +%Y-%m-%d)-${BRANCH}"

exec &> >(tee -a "/var/log/ps-${REPORT_OUTPUT_NAME}.log")

if [ -d $DIR_PATH ]; then
  rm -rf $DIR_PATH
fi

git clone https://github.com/PrestaShop/PrestaShop.git $DIR_PATH/prestashop

cd $DIR_PATH/prestashop
git checkout $BRANCH
mkdir -p $REPORT_PATH

echo "Clear docker..."
docker system prune -f
docker image prune -f
docker volume prune -f


cd "${DIR_PATH}/prestashop/tests/E2E"
for test_directory in test/campaigns/full/*; do
  if [ -d "${test_directory}" ]; then
    if [ -z "$(docker ps -qa)" ]; then
      # Make sure all containers are stopped
      docker stop $(docker ps -qa)
    fi

    echo "Try to clear docker-compose instances..."
    docker-compose down -v -t 100 || true

    echo "Boot docker-compose instances..."
    docker-compose up -d --build --force-recreate

    echo "Run ${TEST_PATH}"
    echo "Wait for docker-compose..."
    sleep 10

    TEST_PATH=${test_directory/test\/campaigns\//}
    docker-compose exec -T -e TEST_PATH=$TEST_PATH tests /tmp/wait-for-it.sh --timeout=720 --strict prestashop-web:80 -- bash /tmp/run-tests.sh

    if [ -f "mochawesome-report/mochawesome.json" ]; then
      cp mochawesome-report/mochawesome.json "${REPORT_PATH}/${TEST_PATH//\//-}.json"
    fi
  fi
done

echo "Check for reports..."
if [ -n "$(ls ${REPORT_PATH})" ]; then
  mkdir -p "${DIR_PATH}/reports"
  ./scripts/combine-reports.py "${REPORT_PATH}" "${REPORT_PATH}/${REPORT_OUTPUT_NAME}.json"
  nodejs ./node_modules/mochawesome-report-generator/bin/cli.js "${REPORT_PATH}/${REPORT_OUTPUT_NAME}.json" -o "${DIR_PATH}/reports"
  gsutil cp -r "${DIR_PATH}/reports" gs://prestashop-core-nightly
fi

shutdown -h now
