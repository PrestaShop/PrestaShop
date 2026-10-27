#!/usr/bin/env bash
#
# Runs test lanes concurrently, sourced by the run-*-in-parallel.sh scripts which define LANES, INSTALL_LANE and a
# run_lane function, then call run_lanes.
#
# Tests reset shared files (img/, download/, tests/Resources/modules/, var/cache/), so each lane runs in its own
# copy of the project, with its own temp dir (dumps, resource backups) and its own database (PS_TEST_DB_SUFFIX).
# The test database is installed once, by INSTALL_LANE in the project itself, then copied for the other lanes
# along with the project files and the dumps the installation writes.

set -u

ROOT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
PHP=${PHP_BINARY:-php}
DB_SUFFIX=${PS_TEST_DB_SUFFIX:-}
WORK_DIR=$(mktemp -d "${RUNNER_TEMP:-${TMPDIR:-/tmp}}/ps-lanes.XXXXXX")
trap 'rm -rf "$WORK_DIR"' EXIT

cd "$ROOT_DIR" || exit 1
mapfile -t DB < <("$PHP" -r '
    $parameters = (require "app/config/parameters.php")["parameters"];
    foreach (["database_host", "database_port", "database_user", "database_password", "database_name"] as $key) {
        echo $parameters[$key] ?? "", PHP_EOL;
    }
')
VERSION=$("$PHP" -r 'require "vendor/autoload.php"; echo PrestaShop\PrestaShop\Core\Version::VERSION;')

lane_dir() {
    if [ "$1" = "$INSTALL_LANE" ]; then
        echo "$ROOT_DIR"
    else
        echo "$WORK_DIR/project-$1"
    fi
}

lane_tmp() {
    echo "$WORK_DIR/tmp-$1"
}

lane_db() {
    echo "test_${DB[4]}${DB_SUFFIX}_$1"
}

use_lane_env() {
    export PS_TEST_DB_SUFFIX="${DB_SUFFIX}_$1"
    export TMPDIR="$(lane_tmp "$1")" TMP="$(lane_tmp "$1")" TEMP="$(lane_tmp "$1")"
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
    local lane=$1 source_db target_db source_tmp target_tmp file
    source_db=$(lane_db "$INSTALL_LANE")
    target_db=$(lane_db "$lane")
    source_tmp=$(lane_tmp "$INSTALL_LANE")
    target_tmp=$(lane_tmp "$lane")
    mkdir -p "$target_tmp"

    run_mysql -e "DROP DATABASE IF EXISTS \`$target_db\`; CREATE DATABASE \`$target_db\`" \
        && run_mysql "$target_db" < "$source_tmp/ps_dump_${source_db}_$VERSION.sql" || return 1

    for file in "$source_tmp/ps_dump_${source_db}_"*; do
        cp "$file" "$target_tmp/$(basename "$file" | sed "s/^ps_dump_${source_db}_/ps_dump_${target_db}_/")" || return 1
    done
    cp -R "$source_tmp"/ps_backup_test_* "$target_tmp/"
}

run_lane_with_timing() {
    local lane=$1 start status
    start=$(date +%s)
    cd "$(lane_dir "$lane")" || return 1
    use_lane_env "$lane"
    run_lane "$lane"
    status=$?
    echo "Lane $lane finished with exit code $status in $(($(date +%s) - start))s"

    return $status
}

run_lanes() {
    local start lane pid pids failed=0

    start=$(date +%s)
    if ! (
        use_lane_env "$INSTALL_LANE"
        "$PHP" tests/bin/create-test-db.php && "$PHP" tests/bin/create-test-tables-dump.php
    ) > "$WORK_DIR/install.log" 2>&1; then
        cat "$WORK_DIR/install.log"
        return 1
    fi
    echo "Test database installed in $(($(date +%s) - start))s"

    start=$(date +%s)
    pids=""
    for lane in $LANES; do
        if [ "$lane" != "$INSTALL_LANE" ]; then
            (copy_project "$(lane_dir "$lane")" && copy_install "$lane") &
            pids="$pids $!"
        fi
    done
    for pid in $pids; do
        wait "$pid" || { echo "Could not copy the installation"; return 1; }
    done
    echo "Installation copied in $(($(date +%s) - start))s"

    pids=""
    for lane in $LANES; do
        # Live output prefixed by the lane, the full log of each lane is printed again unmixed at the end
        (
            set -o pipefail
            run_lane_with_timing "$lane" 2>&1 | tee "$WORK_DIR/$lane.log" | sed -u "s/^/[$lane] /"
        ) &
        pids="$pids $!"
    done
    set -- $pids
    for lane in $LANES; do
        wait "$1" || failed=1
        shift
    done

    for lane in $LANES; do
        echo "::group::Lane $lane"
        cat "$WORK_DIR/$lane.log"
        echo "::endgroup::"
        if [ "$lane" != "$INSTALL_LANE" ]; then
            mkdir -p "$ROOT_DIR/var/logs/lane-$lane"
            cp -R "$(lane_dir "$lane")/var/logs/." "$ROOT_DIR/var/logs/lane-$lane/" 2>/dev/null
        fi
    done
    for lane in $LANES; do
        tail -n 1 "$WORK_DIR/$lane.log"
    done

    return $failed
}
