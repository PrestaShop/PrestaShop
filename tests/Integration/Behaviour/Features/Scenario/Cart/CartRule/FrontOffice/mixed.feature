# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-mixed
@restore-all-tables-before-feature
@fo-cart-rule-mixed
Feature: Cart rule (mixed) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given I have an empty default cart
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    And there is a cart rule "cartrule2" with following properties:
      | name[en-US]         | cartrule2 |
      | priority            | 2         |
      | free_shipping       | false     |
      | code                | foo2      |
      | discount_percentage | 50        |
    And there is a cart rule "cartrule4" with following properties:
      | name[en-US]           | cartrule4 |
      | priority              | 4         |
      | free_shipping         | false     |
      | code                  | foo4      |
      | discount_amount       | 5         |
      | discount_currency     | usd       |
      | discount_includes_tax | false     |
    And there is a cart rule "cartrule5" with following properties:
      | name[en-US]           | cartrule5 |
      | priority              | 5         |
      | free_shipping         | false     |
      | code                  | foo5      |
      | discount_amount       | 500       |
      | discount_currency     | usd       |
      | discount_includes_tax | false     |
    And there is a cart rule "cartrule7" with following properties:
      | name[en-US]         | cartrule7 |
      | priority            | 7         |
      | free_shipping       | false     |
      | code                | foo7      |
      | discount_percentage | 50        |

  Scenario: one product in cart, quantity 1, one 50% global cartRule, one 5€ global cartRule
    Given I add 1 items of product "product1" in my cart
    And my cart total should be 26.812 tax included
    And my cart total shipping fees should be 7.0 tax included
    When I apply the voucher code "foo2"
    And I apply the voucher code "foo4"
    Then my cart total should be 11.906 tax included

  Scenario: one product in cart, quantity 1, one 50% global cartRule, one 500€ global cartRule
    Given I add 3 items of product "product1" in my cart
    And my cart total should be 66.436 tax included
    And my cart total shipping fees should be 7.0 tax included
    When I apply the voucher code "foo2"
    When I apply the voucher code "foo5"
    Then my cart total should be 7.0 tax included

  Scenario: one product in cart, quantity 3, one 5€ global cartRule, one 50% global cartRule
    Given I add 3 items of product "product1" in my cart
    And my cart total should be 66.436 tax included
    And my cart total shipping fees should be 7.0 tax included
    When I apply the voucher code "foo4"
    When I apply the voucher code "foo7"
    Then my cart total should be 34.218 tax included

  Scenario: one product in cart, quantity 3, one 500€ global cartRule, one 50% global cartRule
    Given I add 3 items of product "product1" in my cart
    And my cart total should be 66.436 tax included
    And my cart total shipping fees should be 7.0 tax included
    When I apply the voucher code "foo5"
    Then my cart total should be 7.0 tax included

  Scenario: 3 products with several quantities in cart, one 5€ global cartRule, one 50% global cartRule
    Given I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total should be 162.4 tax included
    And my cart total shipping fees should be 7.0 tax included
    When I apply the voucher code "foo4"
    And I apply the voucher code "foo7"
    Then my cart total should be 82.205 tax included

  Scenario: Percentage reduction cart rule and gift product cart rule applied to the same cart
    Given there is a cart rule "cartrule12" with following properties:
      | name[en-US]         | cartrule12 |
      | priority            | 1          |
      | free_shipping       | false      |
      | code                | foo12      |
      | discount_percentage | 10         |
      | gift_product        | product3   |
    And I add 1 items of product "product1" in my cart
    And I should have 1 different products in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 26.8 tax included
    When I apply the voucher code "foo12"
    Then my cart total should be 24.8 tax included
    And I should have 2 products in my cart
    When I apply the voucher code "foo2"
    Then my cart total should be 15.9 tax included
    And I should have 2 products in my cart

  Scenario: 1 product in cart, cart rule giving gift out of stock, and global cart rule should be inserted without error (test PR #8361)
    Given product "product4" is out of stock
    And there is a cart rule "cartrule13" with following properties:
      | name[en-US]         | cartrule13 |
      | priority            | 1          |
      | free_shipping       | false      |
      | code                | foo13      |
      | discount_percentage | 10         |
      | gift_product        | product4   |
    And I add 1 items of product "product1" in my cart
    And I should have 1 different products in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 26.8 tax included
    When I apply the voucher code "foo13"
    Then my cart total should be 24.8 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be 15.9 tax included
    And I should have 1 products in my cart
