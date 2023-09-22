# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-validation
@restore-all-tables-before-feature
@fo-cart-rule-validation
Feature: Cart rule application is validated before it is applied to cart
  As a customer
  I must not be able to apply invalid cart rules

  Background:
    Given I have an empty default cart
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a cart rule "cart_rule_1" with following properties:
      | name[en-US]                  | cartrule1 |
      | free_shipping                | false     |
      | code                         | foo1      |
      | discount_percentage          | 50        |
      | apply_to_discounted_products | true      |
    And there is a cart rule "cart_rule_2" with following properties:
      | name[en-US]           | cartrule2 |
      | free_shipping         | false     |
      | code                  | foo2      |
      | discount_amount       | 10        |
      | discount_currency     | usd       |
      | discount_includes_tax | false     |
    And there is a cart rule "cart_rule_3" with following properties:
      | name[en-US]       | cartrule3 |
      | total_quantity    | 10        |
      | quantity_per_user | 10        |
      | free_shipping     | true      |
      | code              | foo3      |

  Scenario: Adding a cart rule to an empty cart should not be possible
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And discount code "foo1" is not applied to my cart
    And discount code "foo2" is not applied to my cart
    And discount code "foo3" is not applied to my cart
    And I should have 0 different products in my cart
    And my cart total shipping fees should be 0.0 tax included
    And my cart total should be 0.0 tax included
    # try applying percentage discount
    When I apply the voucher code "foo1"
    Then I should get cart rule validation error saying "Cart is empty"
    And discount code "foo1" is not applied to my cart
    And I should have 0 products in my cart
    And my cart total shipping fees should be 0.0 tax included
    And my cart total should be 0.0 tax included
    # try applying amount discount
    When I apply the voucher code "foo2"
    Then I should get cart rule validation error saying "Cart is empty"
    And discount code "foo2" is not applied to my cart
    And I should have 0 products in my cart
    And my cart total shipping fees should be 0.0 tax included
    And my cart total should be 0.0 tax included
    # try applying free shipping discount
    When I apply the voucher code "foo3"
    Then I should get cart rule validation error saying "Cart is empty"
    And discount code "foo3" is not applied to my cart
    And I should have 0 products in my cart
    And my cart total shipping fees should be 0.0 tax included
    And my cart total should be 0.0 tax included
    # try applying cart rule restricted to carrier
    Given I have an empty default cart
    And there is a cart rule "cart_rule_4" with following properties:
      | name[en-US]         | cart_rule_4   |
      | free_shipping       | false         |
      | free_shipping       | true          |
      | code                | rule_carrier1 |
      | discount_percentage | 50            |
    And there is a carrier named "carrier1"
    And I restrict following carriers for cart rule cart_rule_4:
      | restricted carriers | carrier1 |
    And I save all the restrictions for cart rule cart_rule_4
    And cart rule cart_rule_4 should have the following properties:
      | restricted carriers | carrier1 |
    And I select carrier "carrier1" in my cart
    And I should have 0 products in my cart
    When I apply the voucher code "foo3"
    Then I should get cart rule validation error saying "Cart is empty"

  @restore-cart-rules-after-scenario
  Scenario: Cart rule without code is not applied when cart is empty
    Given I have an empty default cart
    And my cart total shipping fees should be 0.0 tax included
    And my cart total should be 0.0 tax included
    And I should have 0 products in my cart
    When there is a cart rule "cart_rule_5" with following properties:
      | name[en-US]   | cart_rule_5 |
      | free_shipping | false       |
      | free_shipping | false       |
      | gift_product  | product1    |
    Then my cart total shipping fees should be 0.0 tax included
    And my cart total should be 0.0 tax included
    And I should have 0 products in my cart

  Scenario: Cart rule cannot be applied again when it is already in the cart
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And I add 1 items of product "product1" in my cart
    And I should have 1 different products in my cart
    And my cart total shipping fees should be 7.0 tax excluded
    And my cart total should be 26.8 tax included
    And discount code "foo1" is not applied to my cart
    And I apply the voucher code "foo1"
    And discount "foo1" is applied to my cart
    And my cart total should be 16.9 tax included
    When I apply the voucher code "foo1"
    Then I should get cart rule validation error saying "This voucher is already in your cart"
    And my cart total should be 16.9 tax included
    And I should have 1 products in my cart

  Scenario: I cannot use voucher when it is restricted to specific product and that product is not in my cart
    Given I have an empty default cart
    And there is a cart rule "cartrule4" with following properties:
      | name[en-US]           | reduces $5 for product2 |
      | priority              | 8                       |
      | free_shipping         | false                   |
      | code                  | foo4                    |
      | discount_amount       | 5                       |
      | discount_currency     | usd                     |
      | discount_includes_tax | false                   |
    And cart rule "cartrule4" is restricted to product "product2"
    And I add 1 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 26.812 tax included
    When I apply the voucher code "foo4"
    And I should get cart rule validation error saying "You cannot use this voucher with these products"
    When I add 2 items of product "product1" in my cart
    Then my cart total should be 66.436 tax included
    When I apply the voucher code "foo4"
    Then I should get cart rule validation error saying "You cannot use this voucher with these products"
    And my cart total should be 66.436 tax included
    And my cart total using previous calculation method should be 66.436 tax included
    And I add 1 items of product "product2" in my cart
    And my cart total should be 98.82 tax included
    When I apply the voucher code "foo4"
    Then my cart total should be 93.82 tax included
    # same with percentage
    Given I have an empty default cart
    And there is a cart rule "cartrule50" with following properties:
      | name[en-US]         | cartrule50 |
      | priority            | 10         |
      | free_shipping       | false      |
      | code                | foo50      |
      | discount_percentage | 50         |
    And cart rule "cartrule50" is restricted to product "product2"
    And I add 1 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 26.812 tax included
    When I apply the voucher code "foo50"
    Then I should get cart rule validation error saying "You cannot use this voucher with these products"
    And my cart total should be 26.812 tax included
    # same with free shipping (just restricting to another product)
    Given I have an empty default cart
    And there is a cart rule "cartruleFreeShip" with following properties:
      | name[en-US]                  | cartruleFreeShip |
      | priority                     | 11               |
      | free_shipping                | true             |
      | code                         | cartruleFreeShip |
      | discount_percentage          | 50               |
      | apply_to_discounted_products | true             |
    And cart rule "cartruleFreeShip" is restricted to product "product1"
    And I add 1 items of product "product2" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 39.4 tax included
    When I apply the voucher code "cartruleFreeShip"
    Then I should get cart rule validation error saying "You cannot use this voucher with these products"
    And my cart total should be 39.4 tax included
    Given I have an empty default cart
    And product "product1" is out of stock
    And I add 1 items of product "product2" in my cart
    And I am not allowed to add 1 items of product "product1" in my cart
    When I apply the voucher code "cartruleFreeShip"
    Then I should get cart rule validation error saying "You cannot use this voucher with these products"
    When I apply the voucher code "cartrule50"
    Then my cart total should be 23.2 tax included
