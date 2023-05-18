# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-percent-mono
@restore-all-tables-before-feature
@fo-cart-rule-percent-mono
Feature: Cart rule (percent) calculation with one cart rule
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a cart rule "cartrule2" with following properties:
      | name[en-US]                  | cartrule2              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 2                      |
      | free_shipping                | false                  |
      | code                         | foo2                   |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |

  Scenario: one product in cart, quantity 1, one 50% global cartRule
    Given I have an empty default cart
    And I add 1 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 26.812 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be 16.906 tax included
    And my cart total using previous calculation method should be 16.906 tax included

  Scenario: one product in cart, quantity 3, one 50% global cartRule
    Given I have an empty default cart
    And I add 3 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 66.436 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be 36.718 tax included
    And my cart total using previous calculation method should be 36.718 tax included

  Scenario: 3 products in cart, several quantities, one 5€ global cartRule (reduced product at first place)
    Given I have an empty default cart
    And I add 3 items of product "product1" in my cart
    And I add 2 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 162.4 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be 84.7 tax included
    Then my cart total using previous calculation method should be 84.7 tax included

  Scenario: 1 product in cart, percentage reduction cart rule is applied correctly
    Given I have an empty default cart
    And there is a cart rule "cartrule1" with following properties:
      | name[en-US]                  | cartrule1              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 1                      |
      | free_shipping                | false                  |
      | code                         | foo1                   |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And I add 1 items of product "product1" in my cart
    And I should have 1 different products in my cart
    And my cart total shipping fees should be 7.0 tax excluded
    And my cart total should be 26.812 tax included
    When I apply the voucher code "foo1"
    Then my cart total should be 16.906 tax included
    And my cart total using previous calculation method should be 16.906 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be 11.95 tax included
    And I should have 1 products in my cart
