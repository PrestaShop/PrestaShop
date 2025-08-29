# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-with-brand-trigger
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-with-brand-trigger
Feature: Add discount with brand trigger
  PrestaShop allows BO users to update discounts conditions
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  Scenario: Create discount with brand trigger
    Given I add new manufacturer "rocket" with following properties:
      | name                     | Rocket                             |
      | short_description[en-US] | Cigarettes manufacturer            |
      | description[en-US]       | Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.|
      | meta_title[en-US]        | You smoke - you die!               |
      | meta_description[en-US]  | The sun is shining and the weather is sweet|
      | enabled                  | true                               |
    When I create a "free_shipping" discount "discount_with_brand_trigger" with following properties:
      | name[en-US] | Promotion |
    Then discount "discount_with_brand_trigger" should have the following properties:
      | name[en-US] | Promotion     |
      | type        | free_shipping |
    When I update discount "discount_with_brand_trigger" with following conditions matching at least 42 products:
      | condition_type | items  |
      | manufacturers  | rocket |
    Then discount "discount_with_brand_trigger" should have the following properties:
      | name[en-US]              | Promotion     |
      | type                     | free_shipping |
      | minimum_product_quantity | 0             |
    Then discount "discount_with_brand_trigger" should have the following product conditions matching at least 42 products:
      | condition_type | items  |
      | manufacturers  | rocket |
