@restore-all-tables-before-feature
@reset-all-tables-before-scenario
# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import tests/Integration/Behaviour/Features/Scenario/Import/customer.feature
Feature: Import customers from a CSV file
  As an employee
  I must be able to import customers from a CSV file through the import façade
  This entity type has no modern handler yet: it is executed by the legacy controller

  Scenario: Insert new customers
    When I import "customers" from CSV file "customers_insert.csv" in language "en"
    Then the import should succeed
    And customer with email "behat.customer.a@example.com" should exist
    And customer with email "behat.customer.b@example.com" should exist

  Scenario: Force the row id
    When I import "customers" from CSV file "customers_forceids.csv" in language "en" with options:
      | forceIDs | true |
    Then the import should succeed
    And customer with email "behat.customer.forced@example.com" should exist
    And customer with email "behat.customer.forced@example.com" should have last name "Forced"

  Scenario: Validate-only mode persists nothing
    When I validate the import of "customers" from CSV file "customers_insert.csv" in language "en"
    Then the import should succeed
    And customer with email "behat.customer.a@example.com" should not exist
