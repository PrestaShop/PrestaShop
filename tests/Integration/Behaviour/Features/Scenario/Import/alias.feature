@restore-all-tables-before-feature
@reset-all-tables-before-scenario
Feature: Import search aliases from a CSV file
  As an employee
  I must be able to import search aliases through the import façade
  This entity type has no modern handler yet: it is executed by the legacy controller

  Scenario: Insert new aliases
    When I import "alias" from CSV file "aliases_insert.csv" in language "en"
    Then the import should succeed
    And alias "behatalias" should exist with search "behatsearch"
    And alias "behatalias2" should exist with search "behatsearch2"
