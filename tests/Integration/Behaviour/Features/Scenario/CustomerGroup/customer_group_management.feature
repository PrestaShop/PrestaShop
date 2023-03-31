# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_group --tags customer-group-management
@customer-group-management
@restore-all-tables-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@clear-cache-after-feature
Feature: CustomerGroup Management
  PrestaShop allows BO users to manage customer groups in the Customers > Customers > Groups page
  As a BO user
  I must be able to create customer groups

  Background:
    Given groups feature is activated
    And I enable multishop feature
    And language "fr" with locale "fr-FR" exists
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_shop2" and color "blue" for the group "default_shop_group"
    And I add a shop "shop3" with name "test_shop3" and color "red" for the group "default_shop_group"

  Scenario: Create a simple customer group
    When I create a Customer Group "CustomerGroup1" with the following details:
      | name[en-US]             | Name EN           |
      | name[fr-FR]             | Name FR           |
      | reduction               | 1.23              |
      | displayPriceTaxExcluded | true              |
      | showPrice               | true              |
      | shopIds                 | shop1,shop2,shop3 |
    # See CustomerGroupFeatureContext::transformEditableCustomerGroup
    When I query Customer Group "CustomerGroup1" I should get a Customer Group with properties:
      | customer group          | value             |
      | id                      | 4                 |
      | name[en-US]             | Name EN           |
      | name[fr-FR]             | Name FR           |
      | reduction               | 1.23              |
      | displayPriceTaxExcluded | true              |
      | showPrice               | true              |
      | shopIds                 | shop1,shop2,shop3 |
