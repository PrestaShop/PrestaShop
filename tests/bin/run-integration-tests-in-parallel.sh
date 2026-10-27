#!/usr/bin/env bash
#
# Runs the three integration PHPUnit suites concurrently.
#
# Tests reset shared files (img/, download/, tests/Resources/modules/, var/cache/), so each suite runs in its own
# copy of the project, with its own temp dir (dumps, resource backups) and its own database (PS_TEST_DB_SUFFIX).
# The isolated suite, the longest one, runs in the project itself.

set -u

ROOT_DIR=$(cd "$(dirname "$0")/../.." && pwd)
PHP=${PHP_BINARY:-php}
DB_SUFFIX=${PS_TEST_DB_SUFFIX:-}
WORK_DIR=$(mktemp -d "${RUNNER_TEMP:-${TMPDIR:-/tmp}}/ps-integration.XXXXXX")
trap 'rm -rf "$WORK_DIR"' EXIT

SUITES="main api isolated"

run_phpunit() {
    case $1 in
        main) "$PHP" -d date.timezone=UTC -d memory_limit=-1 ./vendor/phpunit/phpunit/phpunit -c tests/Integration/phpunit.xml ;;
        api) "$PHP" -d date.timezone=UTC -d memory_limit=-1 ./vendor/phpunit/phpunit/phpunit -c tests/Integration/phpunit-api.xml ;;
        isolated) "$PHP" -d date.timezone=UTC ./vendor/phpunit/phpunit/phpunit -c tests/Integration/phpunit-isolated.xml ;;
    esac
}

suite_dir() {
    if [ "$1" = isolated ]; then
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
    run_suite "$suite" > "$WORK_DIR/$suite.log" 2>&1 &
    pids="$pids $!"
done

failed=0
set -- $pids
for suite in $SUITES; do
    wait "$1" || failed=1
    shift
done

for suite in $SUITES; do
    echo "===== Suite $suite ====="
    cat "$WORK_DIR/$suite.log"
done
for suite in $SUITES; do
    tail -n 1 "$WORK_DIR/$suite.log"
done

exit $failed
