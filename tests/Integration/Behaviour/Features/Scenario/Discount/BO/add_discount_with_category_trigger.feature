# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-with-category-trigger
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-with-category-trigger
Feature: Add discount with category trigger
  PrestaShop allows BO users to update discounts conditions
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given language with iso code "en" is the default one
    And category "home" in default language named "Home" exists

  Scenario: Create discount with category trigger
    When I create a "free_shipping" discount "discount_with_category_trigger" with following properties:
      | name[en-US] | Promotion |
    Then discount "discount_with_category_trigger" should have the following properties:
      | name[en-US] | Promotion     |
      | type        | free_shipping |
    When I update discount "discount_with_category_trigger" with following conditions matching at least 42 products:
      | condition_type | items  |
      | categories     | home  |
    Then discount "discount_with_category_trigger" should have the following properties:
      | name[en-US]              | Promotion     |
      | type                     | free_shipping |
      | minimum_product_quantity | 0             |
    Then discount "discount_with_category_trigger" should have the following product conditions matching at least 42 products:
      | condition_type | items  |
      | categories     | home  |
