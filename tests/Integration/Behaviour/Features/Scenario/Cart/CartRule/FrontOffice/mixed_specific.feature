# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-mixed-specific
@restore-all-tables-before-feature
@fo-cart-rule-mixed-specific
Feature: Cart rule (percent) calculation with multiple cart rules restricted to one product
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given I have an empty default cart
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a cart rule "cartrule10" with following properties:
      | name[en-US]         | cartrule10 |
      | priority            | 10         |
      | free_shipping       | false      |
      | code                | foo10      |
      | discount_percentage | 50         |
    And cart rule "cartrule10" is restricted to product "product2"
    And there is a cart rule "cartrule8" with following properties:
      | name[en-US]           | cartrule8 |
      | priority              | 8         |
      | free_shipping         | false     |
      | code                  | foo8      |
      | discount_amount       | 5         |
      | discount_currency     | usd       |
      | discount_includes_tax | false     |
    And cart rule "cartrule8" is restricted to product "product2"

  Scenario: one product #2 in cart, quantity 3, specific 5€ cartRule on product #2, specific 50% cartRule on product #2
    Given I add 3 items of product "product2" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 104.2 tax included
    When I apply the voucher code "foo10"
    And I apply the voucher code "foo8"
    And my cart total should be 53.082 tax included

  Scenario: 3 products in cart, several quantities, specific 5€ cartRule on product #2, specific 50% cartRule on product #2
    Given I add 3 items of product "product1" in my cart
    And I add 2 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 162.4 tax included
    When I apply the voucher code "foo10"
    And I apply the voucher code "foo8"
    Then my cart total should be 127.512 tax included
