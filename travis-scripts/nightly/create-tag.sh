#!/bin/sh

if [[ $TRAVIS_EVENT_TYPE != *"cron"* ]]; then
  exit 0
fi

GIT_TAG=nightly

setup_git() {
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "Travis CI"
}

create_and_push_tag() {
  git tag -f -a nightly -m "Nightly from Travis build: $TRAVIS_BUILD_NUMBER"
  git remote add origin-push https://${GITHUB_OAUTH_TOKEN}@github.com/PrestaShop/PrestaShop.git > /dev/null 2>&1
  git push --quiet origin-push :refs/tags/$GIT_TAG && git push --quiet origin-push $GIT_TAG
}

setup_git
create_and_push_tag