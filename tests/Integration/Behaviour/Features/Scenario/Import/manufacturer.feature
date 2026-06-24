@restore-all-tables-before-feature
@reset-all-tables-before-scenario
Feature: Import manufacturers from a CSV file
  As an employee
  I must be able to import manufacturers (brands) through the import façade
  This entity type has no modern handler yet: it is executed by the legacy controller

  Scenario: Insert new manufacturers
    When I import "manufacturers" from CSV file "manufacturers_insert.csv" in language "en"
    Then the import should succeed
    And manufacturer "Behat Brand A" should exist
    And manufacturer "Behat Brand B" should exist
