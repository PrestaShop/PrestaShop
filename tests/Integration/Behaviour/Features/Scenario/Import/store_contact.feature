@restore-all-tables-before-feature
@reset-all-tables-before-scenario
Feature: Import store contacts from a CSV file
  As an employee
  I must be able to import store contacts through the import façade
  This entity type has no modern handler yet: it is executed by the legacy controller

  Scenario: Insert new store contacts
    When I import "contacts" from CSV file "contacts_insert.csv" in language "en"
    Then the import should succeed
    And store "Behat Store A" should exist
    And store "Behat Store B" should exist
