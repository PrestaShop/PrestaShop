# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags add
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@add
Feature: Add basic alias from Back Office (BO)
  As a BO user
  I need to be able to add new alias with basic information from the BO

  Background:

