#!/usr/bin/env bash
#
# Runs the integration PHPUnit suites in four concurrent lanes of similar duration, one per CPU of the CI runner:
# the main suite, each of the two Admin API endpoint variant classes (most of the isolated suite), and the api suite
# followed by the rest of the isolated suite.
#
# Tests reset shared files (img/, download/, tests/Resources/modules/, var/cache/), so each lane runs in its own
# copy of the project, with its own temp dir (dumps, resource backups) and its own database (PS_TEST_DB_SUFFIX).
# The main suite, the longest lane, runs in the project itself.

set -u

ROOT_DIR=$(cd "$(dirname "$0")/../.." && pwd)
PHP=${PHP_BINARY:-php}
DB_SUFFIX=${PS_TEST_DB_SUFFIX:-}
WORK_DIR=$(mktemp -d "${RUNNER_TEMP:-${TMPDIR:-/tmp}}/ps-integration.XXXXXX")
trap 'rm -rf "$WORK_DIR"' EXIT

SUITES="main versioned disabled api"
PHPUNIT="./vendor/phpunit/phpunit/phpunit"
VARIANT_CLASSES="VersionedEndpointsTest|DisabledEndpointsTest"

run_phpunit() {
    case $1 in
        main) "$PHP" -d date.timezone=UTC -d memory_limit=-1 $PHPUNIT -c tests/Integration/phpunit.xml ;;
        versioned) "$PHP" -d date.timezone=UTC $PHPUNIT -c tests/Integration/phpunit-isolated.xml tests/Integration/ApiPlatform/VersionedEndpointsTest.php ;;
        disabled) "$PHP" -d date.timezone=UTC $PHPUNIT -c tests/Integration/phpunit-isolated.xml tests/Integration/ApiPlatform/DisabledEndpointsTest.php ;;
        api) "$PHP" -d date.timezone=UTC -d memory_limit=-1 $PHPUNIT -c tests/Integration/phpunit-api.xml \
            && "$PHP" -d date.timezone=UTC $PHPUNIT -c tests/Integration/phpunit-isolated.xml --filter "^(?!.*($VARIANT_CLASSES)::)" ;;
    esac
}

suite_dir() {
    if [ "$1" = main ]; then
        echo "$ROOT_DIR"
    else
        echo "$WORK_DIR/project-$1"
    fi
}

copy_project() {
    mkdir -p "$1/var/cache" "$1/var/logs"
    tar -C "$ROOT_DIR" --exclude=./.git --exclude=node_modules --exclude=./var/cache --exclude=./var/logs -cf - . \
        | tar -C "$1" -xf -
}

run_suite() {
    local suite=$1 start
    start=$(date +%s)
    cd "$(suite_dir "$suite")" || return 1
    export PS_TEST_DB_SUFFIX="${DB_SUFFIX}_$suite"
    export TMPDIR="$WORK_DIR/tmp-$suite" TMP="$WORK_DIR/tmp-$suite" TEMP="$WORK_DIR/tmp-$suite"
    mkdir -p "$TMPDIR"

    "$PHP" tests/bin/create-test-db.php \
        && "$PHP" tests/bin/create-test-tables-dump.php \
        && run_phpunit "$suite"
    local status=$?
    echo "Suite $suite finished with exit code $status in $(($(date +%s) - start))s"

    return $status
}

start=$(date +%s)
for suite in $SUITES; do
    if [ "$(suite_dir "$suite")" != "$ROOT_DIR" ]; then
        copy_project "$(suite_dir "$suite")" &
    fi
done
wait
echo "Project copied in $(($(date +%s) - start))s"

pids=""
for suite in $SUITES; do
    # Live output prefixed by the lane, the full log of each lane is printed again unmixed at the end
    (
        set -o pipefail
        run_suite "$suite" 2>&1 | tee "$WORK_DIR/$suite.log" | sed -u "s/^/[$suite] /"
    ) &
    pids="$pids $!"
done

failed=0
set -- $pids
for suite in $SUITES; do
    wait "$1" || failed=1
    shift
done

for suite in $SUITES; do
    echo "::group::Suite $suite"
    cat "$WORK_DIR/$suite.log"
    echo "::endgroup::"
done
for suite in $SUITES; do
    tail -n 1 "$WORK_DIR/$suite.log"
done

exit $failed
