#!/bin/bash

set -x

BRANCH=$(curl http://metadata.google.internal/computeMetadata/v1/instance/attributes/TRAVIS_BRANCH -H "Metadata-Flavor: Google")
CURRENT_DATE=$([ -z "$1" ] && date +%Y-%m-%d || echo $1)
DIR_PATH="/var/ps-reports/${CURRENT_DATE}"
REPORT_NAME="${CURRENT_DATE}-${BRANCH}"
REPORT_PATH="${DIR_PATH}/campaigns"

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

cd "${DIR_PATH}/prestashop/tests/E2E"
for test_directory in test/campaigns/full/*; do
  # Continue if it is not a directory
  [ -d "${test_directory}" ] || continue

  if [ -z "$(docker ps -qa)" ]; then
    # Make sure all containers are stopped
    docker stop $(docker ps -qa)
  fi

  echo "Boot docker-compose instances..."
  docker-compose up -d --build --force-recreate

  echo "Run ${TEST_PATH}"
  echo "Wait for docker-compose..."
  sleep 10

  TEST_PATH=${test_directory/test\/campaigns\//}
  docker-compose exec -T -e TEST_PATH="${TEST_PATH}/*" tests /tmp/wait-for-it.sh --timeout=720 --strict prestashop-web:80 -- bash /tmp/run-tests.sh

  if [ -f "mochawesome-report/mochawesome.json" ]; then
    cp mochawesome-report/mochawesome.json "${REPORT_PATH}/${TEST_PATH//\//-}.json"
  fi

  echo "Try to clear docker-compose instances..."
  docker-compose down -v -t 100 || true
done
