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
    And I add a shop "shop4" with name "test_shop4" and color "red" for the group "default_shop_group"

  Scenario: Create a simple customer group
    When I create a customer group "CustomerGroup1" with the following details:
      | name[en-US]             | Name EN           |
      | name[fr-FR]             | Name FR           |
      | reduction               | 1.23              |
      | displayPriceTaxExcluded | true              |
      | showPrice               | true              |
      | shopIds                 | shop1,shop2,shop4 |
    # See CustomerGroupFeatureContext::transformEditableCustomerGroup
    Then customer group "CustomerGroup1" have the following values:
      | customer group          | value             |
      | reference_id            | CustomerGroup1    |
      | name[en-US]             | Name EN           |
      | name[fr-FR]             | Name FR           |
      | reduction               | 1.23              |
      | displayPriceTaxExcluded | true              |
      | showPrice               | true              |
      | shopIds                 | shop1,shop2,shop4 |

  Scenario: Update the created customer group
    When I update customer group "CustomerGroup1" with the following details:
      | name[en-US]             | New Name EN |
      | name[fr-FR]             | New Name FR |
      | reduction               | 2.56        |
      | displayPriceTaxExcluded | false       |
      | showPrice               | false       |
      | shopIds                 | shop2,shop3 |
    Then customer group "CustomerGroup1" have the following values:
      | customer group          | value          |
      | reference_id            | CustomerGroup1 |
      | name[en-US]             | New Name EN    |
      | name[fr-FR]             | New Name FR    |
      | reduction               | 2.56           |
      | displayPriceTaxExcluded | false          |
      | showPrice               | false          |
      | shopIds                 | shop2,shop3    |
    # Test partial update
    When I update customer group "CustomerGroup1" with the following details:
      | displayPriceTaxExcluded | true |
      | showPrice               | true |
    Then customer group "CustomerGroup1" have the following values:
      | customer group          | value          |
      | reference_id            | CustomerGroup1 |
      | name[en-US]             | New Name EN    |
      | name[fr-FR]             | New Name FR    |
      | reduction               | 2.56           |
      | displayPriceTaxExcluded | true           |
      | showPrice               | true           |
      | shopIds                 | shop2,shop3    |
    # Test partial update
    When I update customer group "CustomerGroup1" with the following details:
      | name[en-US] | Partial New Name EN |
    Then customer group "CustomerGroup1" have the following values:
      | customer group          | value               |
      | reference_id            | CustomerGroup1      |
      | name[en-US]             | Partial New Name EN |
      | name[fr-FR]             | New Name FR         |
      | reduction               | 2.56                |
      | displayPriceTaxExcluded | true                |
      | showPrice               | true                |
      | shopIds                 | shop2,shop3         |

  Scenario: Delete the customer group
    Given customer group CustomerGroup1 exists
    When I delete customer group CustomerGroup1
    Then customer group CustomerGroup1 does not exist

  Scenario: Toggle show prices for a customer group
    When I create a customer group "GroupToggle" with the following details:
      | name[en-US]             | Toggle Group EN |
      | name[fr-FR]             | Toggle Group FR |
      | reduction               | 0.00            |
      | displayPriceTaxExcluded | false           |
      | showPrice               | true            |
      | shopIds                 | shop1           |
    When I toggle show prices for customer group "GroupToggle"
    Then customer group "GroupToggle" have the following values:
      | customer group          | value           |
      | reference_id            | GroupToggle     |
      | name[en-US]             | Toggle Group EN |
      | name[fr-FR]             | Toggle Group FR |
      | reduction               | 0.00            |
      | displayPriceTaxExcluded | false           |
      | showPrice               | false           |
      | shopIds                 | shop1           |
    When I toggle show prices for customer group "GroupToggle"
    Then customer group "GroupToggle" have the following values:
      | customer group          | value           |
      | reference_id            | GroupToggle     |
      | name[en-US]             | Toggle Group EN |
      | name[fr-FR]             | Toggle Group FR |
      | reduction               | 0.00            |
      | displayPriceTaxExcluded | false           |
      | showPrice               | true            |
      | shopIds                 | shop1           |

  Scenario: Bulk delete customer groups
    When I create a customer group "BulkGroup1" with the following details:
      | name[en-US]             | Bulk Group 1 EN |
      | name[fr-FR]             | Bulk Group 1 FR |
      | reduction               | 0.00            |
      | displayPriceTaxExcluded | false           |
      | showPrice               | true            |
      | shopIds                 | shop1           |
    And I create a customer group "BulkGroup2" with the following details:
      | name[en-US]             | Bulk Group 2 EN |
      | name[fr-FR]             | Bulk Group 2 FR |
      | reduction               | 0.00            |
      | displayPriceTaxExcluded | false           |
      | showPrice               | true            |
      | shopIds                 | shop1           |
    When I bulk delete customer groups "BulkGroup1,BulkGroup2"
    Then customer group "BulkGroup1" does not exist
    And customer group "BulkGroup2" does not exist

  Scenario: Set category reduction on a customer group
    Given category "homeCategory" in default language named "Home" exists
    And I create a customer group "GroupCategoryReductions" with the following details:
      | name[en-US]             | Category Reductions EN |
      | name[fr-FR]             | Category Reductions FR |
      | reduction               | 0.00                   |
      | displayPriceTaxExcluded | false                  |
      | showPrice               | true                   |
      | shopIds                 | shop1                  |
    When I set 15% category reduction for category "homeCategory" on customer group "GroupCategoryReductions"
    Then category "homeCategory" should have a 15% reduction for customer group "GroupCategoryReductions"
    When I clear category reductions for customer group "GroupCategoryReductions"
    Then category "homeCategory" should not have a reduction for customer group "GroupCategoryReductions"

  Scenario: Module restrictions for a new customer group
    When I create a customer group "GroupModules" with the following details:
      | name[en-US]             | Modules Group EN |
      | name[fr-FR]             | Modules Group FR |
      | reduction               | 0.00             |
      | displayPriceTaxExcluded | false            |
      | showPrice               | true             |
      | shopIds                 | shop1            |
    Then customer group "GroupModules" has no authorized modules
