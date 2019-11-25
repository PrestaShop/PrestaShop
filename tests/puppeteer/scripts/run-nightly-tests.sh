#!/bin/bash

set -x

BRANCH=$(curl http://metadata.google.internal/computeMetadata/v1/instance/attributes/TRAVIS_BRANCH -H "Metadata-Flavor: Google")
CURRENT_DATE=$([ -z "$1" ] && date +%Y-%m-%d || echo $1)
DIR_PATH="/var/ps-reports/${CURRENT_DATE}"
REPORT_NAME="${CURRENT_DATE}-${BRANCH}"
REPORT_PATH="${DIR_PATH}/campaigns"
TESTS_DIR="${DIR_PATH}/prestashop/tests/puppeteer"

exec &> >(tee -a "/var/log/ps-${REPORT_NAME}.log")

if [ ! -d $DIR_PATH ]; then
  mkdir -p $DIR_PATH
  mkdir -p $REPORT_PATH
  git clone https://github.com/PrestaShop/PrestaShop.git $DIR_PATH/prestashop
  cd $DIR_PATH/prestashop
  git checkout $BRANCH
fi

echo "Clear docker..."
docker system prune -a -f
docker image prune -f
docker volume prune -f

cd "${TESTS_DIR}"

for command in "sanity-tests" "functional-tests"; do
  if [ -z "$(docker ps -qa)" ]; then
    # Make sure all containers are stopped
    docker stop $(docker ps -qa)
  fi

  echo "Boot docker-compose instances..."
  docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml up -d --build --force-recreate

  echo "Wait for docker-compose..."
  sleep 10
  docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml exec /tmp/wait-for-it.sh --timeout=720 --strict prestashop-web:80

  # Running command
  echo "Run ${command}"
  docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml exec -e COMMAND="${command}" tests bash /tmp/run-tests.sh

  # Rename mochawesome Report
  if [ -f "${TESTS_DIR}/mochawesome-report/mochawesome.json" ]; then
    cp "${TESTS_DIR}/mochawesome-report/mochawesome.json" "${REPORT_PATH}/${command}.json"
  fi

  echo "Try to clear docker-compose instances..."
  docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml down -v -t 100 || true
done
