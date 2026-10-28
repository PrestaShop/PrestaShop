#!/usr/bin/env bash
#
# Runs the Behat suites in four concurrent lanes, one per CPU of the CI runner. The suite durations are not known
# in advance, so each lane takes the next suite of a shared queue, sorted from the most scenarios to the least.

source "$(dirname "$0")/parallel-lanes.sh"

LANES="1 2 3 4"
INSTALL_LANE=1
BEHAT_CONFIG=tests/Integration/Behaviour/behat.yml
QUEUE="$WORK_DIR/suites"

"$PHP" tests/bin/list-behaviour-suites.php "$BEHAT_CONFIG" > "$QUEUE" || exit 1
echo "$(wc -l < "$QUEUE") Behat suites to run"

next_suite() {
    flock "$QUEUE.lock" sh -c 'head -n 1 "$1" && sed -i 1d "$1"' _ "$QUEUE"
}

run_lane() {
    local suite failed=0
    while suite=$(next_suite) && [ -n "$suite" ]; do
        echo "Suite $suite"
        if ! "$PHP" -d date.timezone=UTC ./vendor/bin/behat -c "$BEHAT_CONFIG" --suite="$suite" --format progress --no-snippets --strict; then
            echo "Suite $suite failed"
            failed=1
        fi
    done

    return $failed
}

run_lanes
