# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-with-feature-trigger
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-with-feature-trigger
Feature: Add discount with supplier feature
  PrestaShop allows BO users to update discounts conditions
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given language with iso code "en" is the default one

  Scenario: Create discount with feature trigger
    When I create product feature "element" with specified properties:
      | name[en-US]      | Nature Element |
      | associated shops | shop1          |
    Then product feature "element" should have following details:
      | name[en-US] | Nature Element |
    When I create feature value "fire" for feature "element" with following properties:
      | value[en-US] | Fire |
    When I create a "free_shipping" discount "discount_with_feature_trigger" with following properties:
      | name[en-US]                | Promotion |
      | productConditionQuantity   | 42        |
      | productCondition[features] | fire      |
    Then discount "discount_with_feature_trigger" should have the following properties:
      | name[en-US]                | Promotion     |
      | type                       | free_shipping |
      | minimum_product_quantity   | 0             |
      | productConditionQuantity   | 42            |
      | productCondition[features] | fire          |
