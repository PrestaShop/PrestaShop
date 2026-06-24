@restore-all-tables-before-feature
@reset-all-tables-before-scenario
@reboot-kernel-after-feature
@clear-cache-before-feature
Feature: Import customers in a multistore context
  As an employee
  I must be able to import customers into specific shops and shop groups
  This entity type is executed by the legacy controller

  Background:
    Given I enable multishop feature
    And I add a shop group "import_group" with name "Import Group"
    And I add a shop "shop2" with name "Import Shop 2" and color "red" for the group "import_group"

  Scenario: Customer is imported into a specific shop through the id_shop column
    When I import "customers" from CSV file "customers_multishop.csv" in language "en"
    Then the import should succeed
    And customer with email "behat.multishop@example.com" should exist
    And customer with email "behat.multishop@example.com" should be in shop "shop2"

  Scenario: Customer is shared across a shop group when share_customer is enabled
    Given the shop group "import_group" shares its customers
    When I import "customers" from CSV file "customers_shared.csv" in language "en"
    Then the import should succeed
    And customer with email "behat.shared@example.com" should be shared in shop group "import_group"
