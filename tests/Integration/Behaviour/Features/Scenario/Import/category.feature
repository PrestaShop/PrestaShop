@restore-all-tables-before-feature
@reset-all-tables-before-scenario
# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import tests/Integration/Behaviour/Features/Scenario/Import/category.feature
Feature: Import categories from a CSV file
  As an employee
  I must be able to import categories from a CSV file through the import façade
  This entity type is executed by the modern Importer

  Scenario: Insert new categories
    When I import "categories" from CSV file "categories_insert.csv" in language "en"
    Then the import should succeed
    And category "Behat Category A" should exist
    And category "Behat Category B" should exist
    And category "Behat Category C" should exist

  Scenario: Validate-only mode persists nothing
    When I validate the import of "categories" from CSV file "categories_insert.csv" in language "en"
    Then the import should succeed
    And category "Behat Category A" should not exist

  Scenario: Truncate then import replaces the existing categories
    When I import "categories" from CSV file "categories_insert.csv" in language "en"
    And I import "categories" from CSV file "categories_truncate.csv" in language "en" with options:
      | truncate | true |
    Then category "Behat Category A" should not exist
    And category "Behat Truncated X" should exist
    And category "Behat Truncated Y" should exist

  Scenario: A malformed row is reported and the neighbouring rows still import
    When I import "categories" from CSV file "categories_malformed.csv" in language "en"
    Then the import should report at least 1 error
    And category "Behat Valid One" should exist
    And category "Behat Valid Two" should exist
