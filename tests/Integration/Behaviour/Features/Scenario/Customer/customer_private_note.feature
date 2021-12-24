# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer --tags customer-private-note
@restore-all-tables-before-feature
@customer-private-note
Feature: Setting private note about customer
  In order to have private notes about FO customers
  As a BO user
  I must be able to set private note about customer

  Scenario: Set private note about customer
    Given there is customer "customer1" with email "pub@prestashop.com"
    And private note is not set about customer "customer1"
    When I set "Suspected card fraud by customer" private note about customer "customer1"
    Then customer "customer1" private note should be "Suspected card fraud by customer"
