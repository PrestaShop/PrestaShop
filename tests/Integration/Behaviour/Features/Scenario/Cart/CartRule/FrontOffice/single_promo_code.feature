@restore-all-tables-before-feature
@fo-cart-rule-single-promo-code
Feature: Only one promo code can be used in a cart
  As a customer
  I should not be able to combine multiple promo codes by default

  Background:
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a cart rule "first_promo" with following properties:
      | name[en-US]         | first_promo |
      | total_quantity      | 1000        |
      | quantity_per_user   | 1000        |
      | priority            | 1           |
      | free_shipping       | false       |
      | code                | FIRST10     |
      | discount_percentage | 10          |
    And there is a cart rule "second_promo" with following properties:
      | name[en-US]         | second_promo |
      | total_quantity      | 1000         |
      | quantity_per_user   | 1000         |
      | priority            | 2            |
      | free_shipping       | false        |
      | code                | SECOND10     |
      | discount_percentage | 10           |
    And I have an empty default cart

  Scenario: Applying a second promo code is rejected by default
    Given I add 1 items of product "product1" in my cart
    When I apply the voucher code "FIRST10"
    And I apply the voucher code "SECOND10"
    Then I should get cart rule validation error saying "Only one promo code can be used per cart"
    And discount code "FIRST10" is applied to my cart
    And discount code "SECOND10" is not applied to my cart

  Scenario: Adding a cart rule directly cannot bypass the promo code limit
    Given shop configuration for "PS_CART_RULE_ALLOW_MULTIPLE_CODES" is set to 0
    And I add 1 items of product "product1" in my cart
    When I use the discount "first_promo"
    And I use the discount "second_promo"
    Then cart rule count in my cart should be 1
    And discount code "FIRST10" is applied to my cart
    And discount code "SECOND10" is not applied to my cart

  Scenario: Merchants can explicitly allow multiple promo codes
    Given shop configuration for "PS_CART_RULE_ALLOW_MULTIPLE_CODES" is set to 1
    And I add 1 items of product "product1" in my cart
    When I apply the voucher code "FIRST10"
    And I apply the voucher code "SECOND10"
    Then cart rule count in my cart should be 2
    And discount code "FIRST10" is applied to my cart
    And discount code "SECOND10" is applied to my cart

  Scenario: Existing carts keep the highest priority promo code after stacking is disabled
    Given shop configuration for "PS_CART_RULE_ALLOW_MULTIPLE_CODES" is set to 1
    And I add 1 items of product "product1" in my cart
    When I apply the voucher code "FIRST10"
    And I apply the voucher code "SECOND10"
    And shop configuration for "PS_CART_RULE_ALLOW_MULTIPLE_CODES" is set to 0
    And I add 1 items of product "product1" in my cart
    Then cart rule count in my cart should be 1
    And discount code "FIRST10" is applied to my cart
    And discount code "SECOND10" is not applied to my cart

  Scenario: Code-less automatic discounts remain combinable with one promo code
    Given shop configuration for "PS_CART_RULE_ALLOW_MULTIPLE_CODES" is set to 0
    And there is a cart rule "automatic_discount" with following properties:
      | name[en-US]         | automatic_discount |
      | total_quantity      | 1000               |
      | quantity_per_user   | 1000               |
      | priority            | 3                  |
      | free_shipping       | false              |
      | code                |                    |
      | discount_percentage | 5                  |
    And I add 1 items of product "product1" in my cart
    When I apply the voucher code "FIRST10"
    Then cart rule count in my cart should be 2
    And discount code "FIRST10" is applied to my cart
    And cart rule "automatic_discount" is applied to my cart
