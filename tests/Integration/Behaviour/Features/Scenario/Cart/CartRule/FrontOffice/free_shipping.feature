# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-free-shipping
@restore-all-tables-before-feature
@fo-cart-rule-free-shipping
Feature: Cart rule (amount) calculation with one cart rule offering free shipping
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given I have an empty default cart
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock

  Scenario: One product in cart, one cartRule offering only free shipping
    Given there is a cart rule "cartrule1" with following properties:
      | name[en-US]       | cartrule1 |
      | priority          | 4         |
      | free_shipping     | true      |
      | code              | foo1      |
    And I add 1 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 26.812 tax included
    When I apply the voucher code "foo1"
    Then my cart total should be 19.812 tax included

  Scenario: One product in cart, one cartRule offering free shipping AND 5€ discount
    Given there is a cart rule "cartrule2" with following properties:
      | name[en-US]           | cartrule2 |
      | priority              | 4         |
      | free_shipping         | true      |
      | code                  | foo2      |
      | discount_amount       | 5         |
      | discount_currency     | usd       |
      | discount_includes_tax | false     |
    And I add 1 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 26.812 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be 14.812 tax included

  Scenario: One product in cart, four different cartRules offering free shipping
    Given there is a cart rule "cartrule5" with following properties:
      | name[en-US]           | cartrule5 |
      | priority              | 4         |
      | free_shipping         | true      |
      | code                  | foo5      |
      | discount_amount       | 5         |
      | discount_currency     | usd       |
      | discount_includes_tax | false     |
    And there is a cart rule "cartrule6" with following properties:
      | name[en-US]       | cartrule6 |
      | total_quantity    | 100       |
      | quantity_per_user | 10        |
      | priority          | 5         |
      | free_shipping     | true      |
      | code              | foo6      |
    And there is a cart rule "cartrule7" with following properties:
      | name[en-US]       | cartrule7 |
      | priority          | 6         |
      | free_shipping     | true      |
      | code              | foo7      |
    And there is a cart rule "cartrule8" with following properties:
      | name[en-US]           | cartrule8 |
      | priority              | 4         |
      | free_shipping         | true      |
      | code                  | foo8      |
      | discount_amount       | 5         |
      | discount_currency     | usd       |
      | discount_includes_tax | false     |
    And I add 1 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 26.812 tax included
    When I apply the voucher code "foo5"
    Then my cart total should be 14.812 tax included
    When I apply the voucher code "foo6"
    Then my cart total should be 14.812 tax included
    When I apply the voucher code "foo7"
    Then my cart total should be 14.812 tax included
    When I apply the voucher code "foo8"
    Then my cart total should be 9.8 tax included
