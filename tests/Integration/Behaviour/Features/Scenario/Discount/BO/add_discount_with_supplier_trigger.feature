# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-with-supplier-trigger
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-with-supplier-trigger
Feature: Add discount with supplier trigger
  PrestaShop allows BO users to update discounts conditions
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given language with iso code "en" is the default one

  Scenario: Create discount with supplier trigger
    When I add new supplier supplier1 with the following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | shops                   | [shop1]            |
    Then supplier supplier1 should have following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | shops                   | [shop1]            |
    When I create a "free_shipping" discount "discount_with_supplier_trigger" with following properties:
      | name[en-US] | Promotion |
    Then discount "discount_with_supplier_trigger" should have the following properties:
      | name[en-US] | Promotion     |
      | type        | free_shipping |
    When I update discount "discount_with_supplier_trigger" with following conditions matching at least 42 products:
      | condition_type | items     |
      | suppliers      | supplier1 |
    Then discount "discount_with_supplier_trigger" should have the following properties:
      | name[en-US]              | Promotion     |
      | type                     | free_shipping |
      | minimum_product_quantity | 0             |
    Then discount "discount_with_supplier_trigger" should have the following product conditions matching at least 42 products:
      | condition_type | items     |
      | suppliers      | supplier1 |
