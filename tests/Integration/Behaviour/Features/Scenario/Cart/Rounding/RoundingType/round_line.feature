# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags round-line
@restore-all-tables-before-feature
@round-line
Feature: Cart calculation with rounding type LINE
  As a customer
  I must be able to have correct cart total when configuration is set to different rounding types

  Background:
    Given I have an empty default cart
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And specific shop configuration for "rounding type" is set to round each line
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock

  Scenario: one product in cart, quantity 1
    When I add 1 items of product "product1" in my cart
    Then my cart total should be precisely 26.81 tax included
    And my cart total shipping fees should be 7.0 tax included

  Scenario: one product in cart, quantity 3
    When I add 3 items of product "product1" in my cart
    Then my cart total should be precisely 66.44 tax included
    And my cart total shipping fees should be 7.0 tax included

  Scenario: 3 products in cart, several quantities
    When I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    Then my cart total should be precisely 162.4 tax included
    And my cart total shipping fees should be 7.0 tax included

  @restore-cart-rules-after-scenario
  Scenario: one product in cart, quantity 3 with a percentage cart rule
    Given there is a cart rule "cartrule1" with following properties:
      | name[en-US]         | cartrule1 |
      | priority            | 1         |
      | discount_percentage | 15        |
    When I add 3 items of product "product1" in my cart
    Then my cart total should be precisely 57.52 tax included
    And my cart total shipping fees should be 7.0 tax included

  Scenario: one product in cart, quantity 3 with an amount cart rule
    Given there is a cart rule "cartrule1" with following properties:
      | name[en-US]           | cartrule1 |
      | priority              | 8         |
      | discount_amount       | 5         |
      | discount_currency     | usd       |
      | discount_includes_tax | false     |
    When I add 3 items of product "product1" in my cart
    Then my cart total should be precisely 61.44 tax included
    And my cart total shipping fees should be 7.0 tax included
