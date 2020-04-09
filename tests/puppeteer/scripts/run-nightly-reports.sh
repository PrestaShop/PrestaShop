#!/bin/bash
#
# DO NOT EXECUTE THIS SCRIPT ON YOUR COMPUTER
#
# This script exists only because of Google compute instance.
# Otherwise it will shutdown your computer.
#

set -x

BRANCH=$(curl http://metadata.google.internal/computeMetadata/v1/instance/attributes/TRAVIS_BRANCH -H "Metadata-Flavor: Google")
TOKEN=$(curl http://metadata.google.internal/computeMetadata/v1/instance/attributes/NIGHTLY_TOKEN -H "Metadata-Flavor: Google")
CURRENT_DATE=$([ -z "$1" ] && date +%Y-%m-%d || echo $1)
NO_SHUTDOWN=$([ -z "$2" ] && echo "" || echo "yes")
DIR_PATH="/var/ps-reports/${CURRENT_DATE}"
REPORT_NAME="${CURRENT_DATE}-${BRANCH}"
REPORT_PATH="${DIR_PATH}/campaigns"
TESTS_DIR="${DIR_PATH}/prestashop/tests/puppeteer"

exec &> >(tee -a "/var/log/ps-${REPORT_NAME}.log")

if [ ! -d $DIR_PATH ]; then
  # Always exit 0 since this script is ran
  # under rc.local
  exit 0
fi

cd "${TESTS_DIR}"

echo "Check for reports..."
if [ -n "$(ls ${REPORT_PATH})" ]; then
  npm install --unsafe-perm=true --allow-root
  mkdir -p "${DIR_PATH}/reports"
  ./scripts/combine-reports.py "${REPORT_PATH}" "${DIR_PATH}/reports/${REPORT_NAME}.json"
  nodejs ./node_modules/mochawesome-report-generator/bin/cli.js "${DIR_PATH}/reports/${REPORT_NAME}.json" -o "${DIR_PATH}/reports" -f "${REPORT_NAME}.html"

  # Send file, remove directory, and shutdown if everything is ok
  gsutil cp -r "${DIR_PATH}/reports" gs://prestashop-core-nightly

  # Trigger event on nightly board
  curl -v "https://nightly.prestashop.com/hook/add?token=${TOKEN}&filename=${REPORT_NAME}.json"

  if [ -z "${NO_SHUTDOWN}" ]; then
    rm -rf $DIR_PATH
    shutdown -h now
  fi
fi
