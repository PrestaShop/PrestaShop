# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-gift
@restore-all-tables-before-feature
@fo-cart-rule-gift
Feature: Cart calculation with cart rules giving gift
  As a customer
  I must be able to have correct cart total when adding products, and adding cart rule with gift

  Background:
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    # cart rules must be reset after each scenario, because new product is created everytime, but old cart rule remains (with previous id of gift product)
    # will need to be improved later when cleaning up product steps
    And there is a cart rule "cartrule13" with following properties:
      | name[en-US]         | cartrule13 |
      | priority            | 13         |
      | free_shipping       | false      |
      | code                | foo13      |
      | discount_percentage | 10         |
      | gift_product        | product4   |

  @restore-cart-rules-after-scenario
  Scenario: 1 product in cart (out of stock), 1 cart rule give it as a gift, offering a gift (out of stock) and a global 10% discount
    And there is a cart rule "cartrule10" with following properties:
      | name[en-US]                  | cartrule10 |
      | priority                     | 10         |
      | free_shipping                | false      |
      | code                         | foo10      |
      | discount_percentage          | 10         |
      | gift_product                 | product4   |
      | apply_to_discounted_products | true       |
    Given product "product4" is out of stock
    And I am not allowed to add 1 items of product "product4" in my cart
    When I apply the voucher code "foo10"
    # @todo: when apply_to_discounted_products = false then it throws "You cannot use this voucher on products on sale", but it is misleading (though out of scope now to fix)
    Then I should get cart rule validation error saying "Cart is empty"
    And I should have 0 products in my cart
    And my cart total should be 0.0 tax included

  @restore-cart-rules-after-scenario
  Scenario: 2 products in cart, one cart rule offering a gift (out of stock) and a global 10% discount
    Given product "product4" is out of stock
    And I add 3 items of product "product1" in my cart
    And my cart total should be 66.436 tax included
    And I am not allowed to add 1 items of product "product4" in my cart
    When I apply the voucher code "foo13"
    And I should have 3 products in my cart
    And my cart total should be 60.4924 tax included

  @restore-cart-rules-after-scenario
  Scenario: 3 products in cart, one cart rule offering a gift and a global 10% discount
    Given I add 3 items of product "product1" in my cart
    When I apply the voucher code "foo13"
    Then I should have 4 products in my cart
    And my cart total should be 60.487 tax included
    And my cart total shipping fees should be 7.0 tax included
    And the current cart should have the following contextual reductions:
      | reference  | reduction |
      | cartrule13 | 41.514    |

  @restore-cart-rules-after-scenario
  Scenario: 2 products in cart including one with specific price, one cart rule offering a gift and a global 10% discount
  but does not apply to already discounted products
    Given there is a product in the catalog named "product2" with a price of 11.00 and 1000 items in stock
    And product "product1" has a specific price named "discount" with a discount of 20.00 percent
    And I add 1 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    When I apply the voucher code "foo13"
    Then I should have 3 products in my cart
    And my cart total should be 32.8 tax included
    And my cart total shipping fees should be 7.0 tax included
    And the current cart should have the following contextual reductions:
      | reference  | reduction |
      | cartrule13 | 36.67     |

  @restore-cart-rules-after-scenario
  Scenario: 1 product in my cart, one cart rule offering the same product and a global 50% discount
    Given there is a cart rule "cartrule14" with following properties:
      | name[en-US]         | cartrule14 |
      | priority            | 1          |
      | free_shipping       | false      |
      | code                | foo14      |
      | discount_percentage | 50         |
    And there is a cart rule "cartrule15" with following properties:
      | name[en-US]   | cartrule15 |
      | priority      | 1          |
      | free_shipping | false      |
      | code          | foo15      |
      | gift_product  | product1   |
    And I add 1 items of product "product1" in my cart
    When I apply the voucher code "foo14"
    Then I should have 1 products in my cart
    And the current cart should have the following contextual reductions:
      | reference  | reduction |
      | cartrule14 | 9.905     |
    And my cart total should be precisely 16.9 tax included
    When I apply the voucher code "foo15"
    Then I should have 2 products in my cart
    And the current cart should have the following contextual reductions:
      | reference  | reduction |
      | cartrule15 | 19.81     |
      | cartrule14 | 9.905     |
    And my cart total should be precisely 16.9 tax included

  @restore-cart-rules-after-scenario
  Scenario: 1 product in my cart, 2 same cart rules offering the same product as gift
    Given there is a cart rule "cartrule16" with following properties:
      | name[en-US]   | cartrule16 |
      | priority      | 1          |
      | free_shipping | false      |
      | code          | foo16      |
      | gift_product  | product1   |
    And there is a cart rule "cartrule17" with following properties:
      | name[en-US]   | cartrule17 |
      | priority      | 1          |
      | free_shipping | false      |
      | code          | foo17      |
      | gift_product  | product1   |
    And I add 1 items of product "product1" in my cart
    When I apply the voucher code "foo16"
    Then I should have 2 products in my cart
    And the current cart should have the following contextual reductions:
      | reference  | reduction |
      | cartrule16 | 19.81     |
    And my cart total should be precisely 26.81 tax included
    When I apply the voucher code "foo17"
    Then I should have 3 products in my cart
    And the current cart should have the following contextual reductions:
      | reference  | reduction |
      | cartrule16 | 19.81     |
      | cartrule17 | 19.81     |
    And my cart total should be precisely 26.82 tax included
    # 3*19.812 = 59.436 rounded to 59.44 - 19.81*2 = 19.82 + 7 (shipping) = 26.82

  @restore-cart-rules-after-scenario
  Scenario: 2 products in cart, one cart rule offering a gift (in stock) and a global 10% discount
    Given product "product4" is out of stock
    And there is a cart rule "cartrule17" with following properties:
      | name[en-US]         | cartrule12 |
      | priority            | 12         |
      | free_shipping       | false      |
      | code                | foo12      |
      | gift_product        | product3   |
      | discount_percentage | 10         |
    And I add 2 items of product "product1" in my cart
    And I add 3 items of product "product3" in my cart
    And I am not allowed to add 1 items of product "product4" in my cart
    When I apply the voucher code "foo12"
    Then I should have 6 products in my cart
    And my cart total should be 126.8692 tax included
