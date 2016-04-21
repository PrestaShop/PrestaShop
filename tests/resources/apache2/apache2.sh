#!/bin/bash
set -e

export APACHE_RUN_USER=travis
export APACHE_RUN_GROUP=travis
export APACHE_PID_FILE=$TRAVIS_BUILD_DIR/tests/resources/apache2/apache2.pid
export APACHE_LOCK_DIR=$TRAVIS_BUILD_DIR/tests/resources/apache2
export APACHE_LOG_DIR=$TRAVIS_BUILD_DIR/tests/resources/apache2

exec /usr/sbin/apache2 -f $TRAVIS_BUILD_DIR/tests/resources/apache2/configuration -k start