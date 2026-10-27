#!/usr/bin/env bash
#
# Runs the integration PHPUnit suites in four concurrent lanes of similar duration, one per CPU of the CI runner:
# the main suite, each of the two Admin API endpoint variant classes (most of the isolated suite), and the api suite
# followed by the rest of the isolated suite.
#
# Tests reset shared files (img/, download/, tests/Resources/modules/, var/cache/), so each lane runs in its own
# copy of the project, with its own temp dir (dumps, resource backups) and its own database (PS_TEST_DB_SUFFIX).
# The test database is installed once, by the main lane in the project itself, then copied for the other lanes
# along with the project files and the dumps the installation writes.

set -u

ROOT_DIR=$(cd "$(dirname "$0")/../.." && pwd)
PHP=${PHP_BINARY:-php}
DB_SUFFIX=${PS_TEST_DB_SUFFIX:-}
WORK_DIR=$(mktemp -d "${RUNNER_TEMP:-${TMPDIR:-/tmp}}/ps-integration.XXXXXX")
trap 'rm -rf "$WORK_DIR"' EXIT

SUITES="main versioned disabled api"
INSTALL_SUITE=main
PHPUNIT="./vendor/phpunit/phpunit/phpunit"
VARIANT_CLASSES="VersionedEndpointsTest|DisabledEndpointsTest"

cd "$ROOT_DIR" || exit 1
mapfile -t DB < <("$PHP" -r '
    $parameters = (require "app/config/parameters.php")["parameters"];
    foreach (["database_host", "database_port", "database_user", "database_password", "database_name"] as $key) {
        echo $parameters[$key] ?? "", PHP_EOL;
    }
')
VERSION=$("$PHP" -r 'require "vendor/autoload.php"; echo PrestaShop\PrestaShop\Core\Version::VERSION;')

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
    if [ "$1" = "$INSTALL_SUITE" ]; then
        echo "$ROOT_DIR"
    else
        echo "$WORK_DIR/project-$1"
    fi
}

suite_tmp() {
    echo "$WORK_DIR/tmp-$1"
}

suite_db() {
    echo "test_${DB[4]}${DB_SUFFIX}_$1"
}

use_suite_env() {
    export PS_TEST_DB_SUFFIX="${DB_SUFFIX}_$1"
    export TMPDIR="$(suite_tmp "$1")" TMP="$(suite_tmp "$1")" TEMP="$(suite_tmp "$1")"
    mkdir -p "$TMPDIR"
}

run_mysql() {
    MYSQL_PWD="${DB[3]}" mysql -h "${DB[0]}" ${DB[1]:+-P "${DB[1]}"} -u "${DB[2]}" "$@"
}

copy_project() {
    mkdir -p "$1/var/cache" "$1/var/logs"
    tar -C "$ROOT_DIR" --exclude=./.git --exclude=node_modules --exclude=./var/cache --exclude=./var/logs -cf - . \
        | tar -C "$1" -xf -
}

# Copies the installed database, its dumps (renamed after the target database) and the resource backups
copy_install() {
    local suite=$1 source_db target_db source_tmp target_tmp file
    source_db=$(suite_db "$INSTALL_SUITE")
    target_db=$(suite_db "$suite")
    source_tmp=$(suite_tmp "$INSTALL_SUITE")
    target_tmp=$(suite_tmp "$suite")
    mkdir -p "$target_tmp"

    run_mysql -e "DROP DATABASE IF EXISTS \`$target_db\`; CREATE DATABASE \`$target_db\`" \
        && run_mysql "$target_db" < "$source_tmp/ps_dump_${source_db}_$VERSION.sql" || return 1

    for file in "$source_tmp/ps_dump_${source_db}_"*; do
        cp "$file" "$target_tmp/$(basename "$file" | sed "s/^ps_dump_${source_db}_/ps_dump_${target_db}_/")" || return 1
    done
    cp -R "$source_tmp"/ps_backup_test_* "$target_tmp/"
}

run_suite() {
    local suite=$1 start
    start=$(date +%s)
    cd "$(suite_dir "$suite")" || return 1
    use_suite_env "$suite"
    run_phpunit "$suite"
    local status=$?
    echo "Suite $suite finished with exit code $status in $(($(date +%s) - start))s"

    return $status
}

start=$(date +%s)
if ! (
    use_suite_env "$INSTALL_SUITE"
    "$PHP" tests/bin/create-test-db.php && "$PHP" tests/bin/create-test-tables-dump.php
) > "$WORK_DIR/install.log" 2>&1; then
    cat "$WORK_DIR/install.log"
    exit 1
fi
echo "Test database installed in $(($(date +%s) - start))s"

start=$(date +%s)
pids=""
for suite in $SUITES; do
    if [ "$suite" != "$INSTALL_SUITE" ]; then
        (copy_project "$(suite_dir "$suite")" && copy_install "$suite") &
        pids="$pids $!"
    fi
done
for pid in $pids; do
    wait "$pid" || { echo "Could not copy the installation"; exit 1; }
done
echo "Installation copied in $(($(date +%s) - start))s"

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
