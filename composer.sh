#!/bin/bash

if [ "$TRAVIS_SECURE_ENV_VARS" = "true" ];
then
mkdir ~/.composer/
echo '{ "config": {"github-oauth":{"github.com": ' > ~/.composer/config.json
echo "\"$GITHUB_COMPOSER_AUTH\"" >> ~/.composer/config.json
echo '}}}' >> ~/.composer/config.json
fi