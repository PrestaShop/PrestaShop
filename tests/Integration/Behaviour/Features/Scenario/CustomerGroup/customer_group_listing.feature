# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_group --tags customer-group-listing
@customer-group-listing
@restore-all-tables-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@clear-cache-after-feature
Feature: Customer groups listing actions
  PrestaShop allows BO users to manage customer groups in the Shop Parameters > Customer Settings > Groups page
  As a BO user
  I must be able to delete customer groups and choose the default groups

  Background:
    Given groups feature is activated
    And I enable multishop feature
    And language "en" with locale "en-US" exists
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_shop2" and color "blue" for the group "default_shop_group"
    And the default customer groups for all shops are referenced as "visitors_group", "guests_group" and "customers_group"

  Scenario: The default customer groups cannot be deleted
    When I delete customer group "visitors_group"
    Then I should get an error that a default customer group cannot be deleted
    And customer group "visitors_group" exists
    When I delete customer group "guests_group"
    Then I should get an error that a default customer group cannot be deleted
    And customer group "guests_group" exists
    When I delete customer group "customers_group"
    Then I should get an error that a default customer group cannot be deleted
    And customer group "customers_group" exists

  Scenario: The default customer group of another shop cannot be deleted
    Given I create a customer group "shop2_visitors_group" with the following details:
      | name[en-US]             | Shop2 visitors |
      | reduction               | 0              |
      | displayPriceTaxExcluded | false          |
      | showPrice               | true           |
      | shopIds                 | shop1,shop2    |
    And I create a customer group "shop2_other_group" with the following details:
      | name[en-US]             | Shop2 other |
      | reduction               | 0           |
      | displayPriceTaxExcluded | false       |
      | showPrice               | true        |
      | shopIds                 | shop2       |
    And I set the default customer groups for shop "shop2" to "shop2_visitors_group", "shop2_other_group" and "shop2_other_group"
    And single shop "shop1" context is loaded
    When I delete customer group "shop2_visitors_group"
    Then I should get an error that a default customer group cannot be deleted
    And customer group "shop2_visitors_group" exists
    When I set the default customer groups for shop "shop2" to "shop2_other_group", "shop2_other_group" and "shop2_other_group"
    And I delete customer group "shop2_visitors_group"
    Then customer group "shop2_visitors_group" does not exist

  Scenario: Bulk delete customer groups skips the default groups and deletes the others
    Given I create a customer group "bulk_group_1" with the following details:
      | name[en-US]             | Bulk group 1 |
      | reduction               | 0            |
      | displayPriceTaxExcluded | false        |
      | showPrice               | true         |
      | shopIds                 | shop1        |
    And I create a customer group "bulk_group_2" with the following details:
      | name[en-US]             | Bulk group 2 |
      | reduction               | 5            |
      | displayPriceTaxExcluded | true         |
      | showPrice               | false        |
      | shopIds                 | shop1,shop2  |
    When I bulk delete customer groups "bulk_group_1, guests_group, bulk_group_2"
    Then I should get a bulk customer group exception refusing 1 default group
    And customer group "bulk_group_1" does not exist
    And customer group "bulk_group_2" does not exist
    And customer group "guests_group" exists

  Scenario: Bulk delete requires at least one customer group
    When I bulk delete an empty list of customer groups
    Then I should get an error that the customer group id is invalid

  Scenario: Update the default customer groups for all shops
    Given I create a customer group "new_customers_group" with the following details:
      | name[en-US]             | New customers |
      | reduction               | 0             |
      | displayPriceTaxExcluded | false         |
      | showPrice               | true          |
      | shopIds                 | shop1,shop2   |
    When I set the default customer groups for all shops to "visitors_group", "guests_group" and "new_customers_group"
    Then the default customer groups for all shops should be "visitors_group", "guests_group" and "new_customers_group"
    When I set the default customer groups for all shops to "visitors_group", "guests_group" and "customers_group"
    Then the default customer groups for all shops should be "visitors_group", "guests_group" and "customers_group"

  Scenario: A default customer group must be associated to the shop it is set for
    Given I create a customer group "shop1_only_group" with the following details:
      | name[en-US]             | Shop1 only |
      | reduction               | 0          |
      | displayPriceTaxExcluded | false      |
      | showPrice               | true       |
      | shopIds                 | shop1      |
    When I set the default customer groups for shop "shop2" to "visitors_group", "guests_group" and "shop1_only_group"
    Then I should get an error that the customer group is not associated to the shop
    And the default customer groups for shop "shop2" should be "visitors_group", "guests_group" and "customers_group"
    When I set the default customer groups for shop "shop1" to "visitors_group", "guests_group" and "shop1_only_group"
    Then the default customer groups for shop "shop1" should be "visitors_group", "guests_group" and "shop1_only_group"
    And the default customer groups for shop "shop2" should be "visitors_group", "guests_group" and "customers_group"

  Scenario: A default customer group must exist
    Given customer group "missing_group" does not exist yet
    When I set the default customer groups for all shops to "missing_group", "guests_group" and "customers_group"
    Then I should get an error that the customer group was not found
    And the default customer groups for all shops should be "visitors_group", "guests_group" and "customers_group"
