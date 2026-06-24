@restore-all-tables-before-feature
@reset-all-tables-before-scenario
Feature: Import suppliers from a CSV file
  As an employee
  I must be able to import suppliers through the import façade
  This entity type has no modern handler yet: it is executed by the legacy controller

  Scenario: Insert new suppliers
    When I import "suppliers" from CSV file "suppliers_insert.csv" in language "en"
    Then the import should succeed
    And supplier "Behat Supplier A" should exist
    And supplier "Behat Supplier B" should exist
