# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-excluded-discounted-product
@restore-all-tables-before-feature
@fo-cart-rule-excluded-discounted-product
Feature: Cart rule (percent) calculation with one cart rule restricted to not already discounted product
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given product "product1" has a specific price named "specificPrice1" with an amount discount of 3.0

  Scenario: multiple products in cart, several quantities, one 50% cartRule excluding already discounted
    Given there is a cart rule "cartrule10" with following properties:
      | name[en-US]                  | cartrule10             |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 10                     |
      | free_shipping                | false                  |
      | code                         | foo10                  |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | false                  |
      | discount_application_type    | order_without_shipping |
    And I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 153.4 tax included
    When I apply the voucher code "foo10"
    Then my cart total should be 105.418 tax included

  Scenario: multiple products in cart, several quantities, one 50% cartRule on selected product excluding already discounted
    Given there is a cart rule "cartrule11" with following properties:
      | name[en-US]                  | cartrule11             |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 10                     |
      | free_shipping                | false                  |
      | code                         | foo11                  |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | false                  |
      | discount_application_type    | order_without_shipping |
    And cart rule "cartrule11" is restricted to product "product2"
    And I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 153.4 tax included
    When I apply the voucher code "foo11"
    Then my cart total should be 121.000 tax included
