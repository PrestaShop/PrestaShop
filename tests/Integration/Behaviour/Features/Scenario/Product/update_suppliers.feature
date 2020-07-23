# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-suppliers
@reset-database-before-feature
@update-suppliers
Feature: Update product suppliers from Back Office (BO)
  As a BO user
  I need to be able to update product suppliers from BO

  Scenario: I update product suppliers
    Given supplier
