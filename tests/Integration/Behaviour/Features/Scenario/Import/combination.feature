@restore-all-tables-before-feature
@reset-all-tables-before-scenario
Feature: Import combinations from a CSV file
  As an employee
  I must be able to import product combinations through the import façade
  This entity type has no modern handler yet: it is executed by the legacy controller

  Scenario: Insert a new combination on an existing product
    Given I import "products" from CSV file "products_for_combinations.csv" in language "en"
    When I import "combinations" from CSV file "combinations_insert.csv" in language "en" with options:
      | match_ref | true |
    Then the import should succeed
    And product with reference "BEHAT-COMBO" should have a combination with reference "BEHAT-COMBO-XL"
