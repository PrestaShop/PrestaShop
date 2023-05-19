# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-amount-specific
@restore-all-tables-before-feature
@fo-cart-rule-amount-specific
Feature: Cart rule (amount) calculation with one cart rule restricted to one product
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a cart rule "cartrule8" with following properties:
      | name[en-US]                  | reduces $5 for product2 |
      | total_quantity               | 1000                    |
      | quantity_per_user            | 1000                    |
      | priority                     | 8                       |
      | free_shipping                | false                   |
      | code                         | foo8                    |
      | discount_amount              | 5                       |
      | discount_currency            | usd                     |
      | discount_includes_tax        | false                   |
      | apply_to_discounted_products | true                    |
      | discount_application_type    | order_without_shipping  |
    And cart rule "cartrule8" is restricted to product "product2"
    And I have an empty default cart

  Scenario: 3 products in cart, several quantities, one specific 5€ cartRule on product #2
    When I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax excluded
    And my cart total should be 162.4 tax excluded
    When I apply the voucher code "foo8"
    Then my cart total should be 157.4 tax included
    And my cart total using previous calculation method should be 157.4 tax included

  Scenario: 3 products in cart, several quantities, one specific 500€ cartRule on product #2
    Given there is a cart rule "cartrule9" with following properties:
      | name[en-US]                  | reduces $500 for product2 |
      | total_quantity               | 1000                      |
      | quantity_per_user            | 1000                      |
      | priority                     | 8                         |
      | free_shipping                | false                     |
      | code                         | foo9                      |
      | discount_amount              | 500                       |
      | discount_currency            | usd                       |
      | discount_includes_tax        | false                     |
      | apply_to_discounted_products | true                      |
      | discount_application_type    | order_without_shipping    |
    And cart rule "cartrule9" is restricted to product "product2"
    When I add 3 items of product "product1" in my cart
    When I add 2 items of product "product2" in my cart
    When I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax excluded
    And my cart total should be 162.364 tax included
    When I apply the voucher code "foo9"
    # discount is $500, but it only applies for product 2 so in the end it provides a discount equal to value of product2 (so 32.388 * 2qty)
    Then my cart total should be 97.624 tax included
    And my cart total using previous calculation method should be 97.624 tax included
