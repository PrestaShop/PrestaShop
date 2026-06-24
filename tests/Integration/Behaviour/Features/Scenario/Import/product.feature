@restore-all-tables-before-feature
@reset-all-tables-before-scenario
# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import tests/Integration/Behaviour/Features/Scenario/Import/product.feature
Feature: Import products from a CSV file
  As an employee
  I must be able to import products from a CSV file through the import façade
  This entity type is executed by the modern Importer

  Scenario: Insert new products
    When I import "products" from CSV file "products_insert.csv" in language "en"
    Then the import should succeed
    And product "Behat Product A" should exist
    And product "Behat Product B" should exist
    And product with reference "BEHAT-A" should exist

  Scenario: Update existing products matched by reference
    When I import "products" from CSV file "products_insert.csv" in language "en"
    And I import "products" from CSV file "products_update_by_reference.csv" in language "en" with options:
      | match_ref | true |
    Then the import should succeed
    And product "Behat Product A" should have price 15.50

  Scenario: Force the row id
    When I import "products" from CSV file "products_forceids.csv" in language "en" with options:
      | forceIDs | true |
    Then the import should succeed
    And product with id 9991 should exist

  Scenario: Update an existing product matched by id
    When I import "products" from CSV file "products_forceids.csv" in language "en" with options:
      | forceIDs | true |
    And I import "products" from CSV file "products_update_by_id.csv" in language "en"
    Then the import should succeed
    And product "Behat Forced Product" should have price 25.00

  Scenario: Validate-only mode does not update an existing product
    When I import "products" from CSV file "products_insert.csv" in language "en"
    And I validate the import of "products" from CSV file "products_update_by_reference.csv" in language "en" with options:
      | match_ref | true |
    Then the import should succeed
    And product "Behat Product A" should have price 10.00

  Scenario: A malformed row is skipped and the neighbouring rows still import
    When I import "products" from CSV file "products_malformed.csv" in language "en"
    Then product "Behat Product Valid One" should exist
    And product "Behat Product Valid Two" should exist
