# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-with-country-trigger
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-with-country-trigger
Feature: Add discount with country trigger
  PrestaShop allows BO users to update discounts conditions
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given language with iso code "en" is the default one
    And country "france" with iso code "FR" exists
    And country "spain" with iso code "ES" exists

  Scenario: Create discount with country trigger
    When I create a "free_shipping" discount "discount_with_country_trigger" with following properties:
      | name[en-US] | Promotion |
    Then discount "discount_with_country_trigger" should have the following properties:
      | name[en-US] | Promotion     |
      | type        | free_shipping |
    When I update discount "discount_with_country_trigger" with conditions based on countries "france,spain"
    Then discount "discount_with_country_trigger" should have the following properties:
      | name[en-US]              | Promotion     |
      | type                     | free_shipping |
      | minimum_product_quantity | 0             |
    Then discount "discount_with_country_trigger" should have the following properties:
      | condition_type | items        |
      | countries      | spain,france |
