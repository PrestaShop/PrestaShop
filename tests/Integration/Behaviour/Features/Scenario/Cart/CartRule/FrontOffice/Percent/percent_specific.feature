# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-percent-specific
@restore-all-tables-before-feature
@fo-cart-rule-percent-specific
@clear-cache-before-feature
Feature: Cart rule (percent) calculation with one cart rule restricted to one product
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a cart rule "cartrule10" with following properties:
      | name[en-US]                  | cartrule10             |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 10                     |
      | free_shipping                | false                  |
      | code                         | foo10                  |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And cart rule "cartrule10" is restricted to product "product2"
    And there is a cart rule "cartrule11" with following properties:
      | name[en-US]                  | cartrule11             |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 11                     |
      | free_shipping                | false                  |
      | code                         | foo11                  |
      | discount_percentage          | 10                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And cart rule "cartrule11" is restricted to product "product2"

  Scenario: one product #2 in cart, quantity 3, one specific 50% cartRule on product #2
    Given I add 3 items of product "product2" in my cart
    And my cart total shipping fees should be 7.0 tax excluded
    And my cart total should be precisely 104.16 tax included
    When I apply the voucher code "foo10"
    Then my cart total should be 55.582 tax included

  Scenario: 3 products in cart, several quantities, one specific 50% cartRule on product #2
    Given I add 2 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And I add 3 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 162.4 tax included
    When I apply the voucher code "foo10"
    # because cart rule provides 50% discount for product2 only
    Then my cart total should be 130.012 tax included

  Scenario: one product #2 in cart, quantity 3, one specific 50% cartRule on product #2
    Given I add 3 items of product "product2" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 104.164 tax included
    When I apply the voucher code "foo10"
    Then my cart total should be 55.582 tax included

  Scenario: one product #2 in cart, quantity 3, specific 50% cartRule on product #2, specific 10% cartRule on product #2
    Given I add 3 items of product "product2" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 104.164 tax included
    When I apply the voucher code "foo10"
    Then my cart total should be 55.58 tax included
    When I apply the voucher code "foo11"
    Then my cart total should be 50.73 tax included

  Scenario: 3 products in cart, several quantities, specific 50% cartRule on product #2, specific 10% cartRule on product #2
    Given I add 3 items of product "product1" in my cart
    And I add 2 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 162.4 tax included
    When I apply the voucher code "foo10"
    When I apply the voucher code "foo11"
    Then my cart total should be 126.7732 tax included
