# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-shipping
@reset-database-before-feature
@update-shipping
Feature: Update product shipping options from Back Office (BO)
  As a BO user I must be able to update product shipping options from BO

  Scenario: I update product shipping
#@todo
