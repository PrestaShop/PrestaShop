@restore-all-tables-before-feature
@reset-all-tables-before-scenario
Feature: Import addresses from a CSV file
  As an employee
  I must be able to import customer addresses through the import façade
  This entity type has no modern handler yet: it is executed by the legacy controller

  Scenario: Insert a new address for an existing customer
    When I import "addresses" from CSV file "addresses_insert.csv" in language "en"
    Then the import should succeed
    And address "Behat Home" should exist for customer "pub@prestashop.com"
