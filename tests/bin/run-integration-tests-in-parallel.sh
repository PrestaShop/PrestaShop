#!/usr/bin/env bash
#
# Runs the integration PHPUnit suites in four concurrent lanes of similar duration, one per CPU of the CI runner:
# the main suite, each of the two Admin API endpoint variant classes (most of the isolated suite), and the api suite
# followed by the rest of the isolated suite.

source "$(dirname "$0")/parallel-lanes.sh"

LANES="main versioned disabled api"
INSTALL_LANE=main
PHPUNIT="./vendor/phpunit/phpunit/phpunit"
VARIANT_CLASSES="VersionedEndpointsTest|DisabledEndpointsTest"

run_lane() {
    case $1 in
        main) "$PHP" -d date.timezone=UTC -d memory_limit=-1 $PHPUNIT -c tests/Integration/phpunit.xml ;;
        versioned) "$PHP" -d date.timezone=UTC $PHPUNIT -c tests/Integration/phpunit-isolated.xml tests/Integration/ApiPlatform/VersionedEndpointsTest.php ;;
        disabled) "$PHP" -d date.timezone=UTC $PHPUNIT -c tests/Integration/phpunit-isolated.xml tests/Integration/ApiPlatform/DisabledEndpointsTest.php ;;
        api) "$PHP" -d date.timezone=UTC -d memory_limit=-1 $PHPUNIT -c tests/Integration/phpunit-api.xml \
            && "$PHP" -d date.timezone=UTC $PHPUNIT -c tests/Integration/phpunit-isolated.xml --filter "^(?!.*($VARIANT_CLASSES)::)" ;;
    esac
}

run_lanes
