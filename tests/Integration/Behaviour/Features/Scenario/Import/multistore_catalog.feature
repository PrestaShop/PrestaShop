@restore-all-tables-before-feature
@reset-all-tables-before-scenario
@reboot-kernel-before-scenario
@reboot-kernel-after-feature
@clear-cache-before-feature
Feature: Import catalog entities in a multistore context
  As an employee
  I must be able to import catalog entities and associate them to the shops named in the CSV
  Products and categories run through the modern Importer, combinations through the legacy controller

  Background:
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And I add a shop group "import_group" with name "Import Group"
    And I add a shop "shop2" with name "Import Shop 2" and color "red" for the group "import_group"
    And I copy "country" shop data from "test_shop" to "Import Shop 2"
    And I copy "currency" shop data from "test_shop" to "Import Shop 2"
    And multiple shop context is loaded

  Scenario: Import a product associated to the shops listed in the shop column
    When I import "products" from CSV file "products_multishop.csv" in language "en"
    Then the import should succeed
    And product with reference "BEHAT-MS" should be associated to shops "shop1,shop2"

  Scenario: Import a category associated to the shops listed in the shop column
    When I import "categories" from CSV file "categories_multishop.csv" in language "en"
    Then the import should succeed
    And category "Behat MS Category" should be associated to shops "shop1,shop2"

  Scenario: Import a combination associated to the shop listed in the shop column
    Given I import "products" from CSV file "products_for_combinations.csv" in language "en"
    When I import "combinations" from CSV file "combinations_multishop.csv" in language "en" with options:
      | match_ref | true |
    Then the import should succeed
    And product with reference "BEHAT-COMBO" should have a combination associated to shop "shop2"
