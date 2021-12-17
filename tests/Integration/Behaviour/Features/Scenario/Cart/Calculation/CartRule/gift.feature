@restore-all-tables-before-feature
Feature: Cart calculation with cart rules giving gift
  As a customer
  I must be able to have correct cart total when adding products, and adding cart rule with gift

  Scenario: 1 product in cart (out of stock), 1 cart rule give it as a gift, offering a gift (out of stock) and a global 10% discount
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    Given product "product4" is out of stock
    Given there is a cart rule named "cartrule13" that applies a percent discount of 10.0% with priority 13, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule13" has a discount code "foo13"
    Given cart rule "cartrule13" offers a gift product "product4"
    When I am not allowed to add 1 items of product "product4" in my cart
    When I use the discount "cartrule13"
    Then I should have 0 products in my cart
    Then my cart total should be 0.0 tax included
    # Test known not to be reliable on previous
    # Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: 2 products in cart, one cart rule offering a gift (out of stock) and a global 10% discount
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    Given product "product4" is out of stock
    Given there is a cart rule named "cartrule13" that applies a percent discount of 10.0% with priority 13, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule13" has a discount code "foo13"
    Given cart rule "cartrule13" offers a gift product "product4"
    When I add 3 items of product "product1" in my cart
    When I am not allowed to add 1 items of product "product4" in my cart
    When I use the discount "cartrule13"
    Then I should have 3 products in my cart
    Then my cart total should be 60.4924 tax included
    # Test known not to be reliable on previous
    # Then my cart total using previous calculation method should be 60.4924 tax included

  Scenario: 3 products in cart, one cart rule offering a gift and a global 10% discount
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    Given there is a cart rule named "cartrule13" that applies a percent discount of 10.0% with priority 13, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule13" has a discount code "foo13"
    Given cart rule "cartrule13" offers a gift product "product4"
    When I add 3 items of product "product1" in my cart
    When I use the discount "cartrule13"
    Then I should have 4 products in my cart
    Then my cart total should be 60.487 tax included
    Then shipping handling fees are set to 7.0
    Then the current cart should have the following contextual reductions:
      | cartrule13        |  41.514  |

  Scenario: 2 products in cart including one with specific price, one cart rule offering a gift and a global 10% discount
    but does not apply to already discounted products

    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And product "product1" has a specific price named "discount" with a discount of 20.00 percent
    Given there is a product in the catalog named "product2" with a price of 11.00 and 1000 items in stock
    Given there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    Given there is a cart rule named "cartrule13" that applies a percent discount of 10.0% with priority 13, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule13" has a discount code "foo13"
    Given cart rule "cartrule13" offers a gift product "product4"
    Given cart rule "cartrule13" does not apply to already discounted products
    When I add 1 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    When I use the discount "cartrule13"
    Then I should have 3 products in my cart
    Then my cart total should be 32.8 tax included
    Then shipping handling fees are set to 7.0
    Then the current cart should have the following contextual reductions:
      | cartrule13        |  36.67  |

  Scenario: 1 product in my cart, one cart rule offering the same product and a global 50% discount
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product_1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product_2" with a price of 35.567 and 1000 items in stock
    Given there is a cart rule named "cartrule14" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule14" has a discount code "foo14"
    Given there is a cart rule named "cartrule15" that applies no discount with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule15" offers a gift product "product_1"
    Given cart rule "cartrule15" has a discount code "foo15"
    When I add 1 items of product "product_1" in my cart
    And I use the discount "cartrule14"
    Then I should have 1 products in my cart
    And the current cart should have the following contextual reductions:
      | cartrule14        | 9.905 |
    And my cart total should be precisely 16.9 tax included
    When I use the discount "cartrule15"
    Then I should have 2 products in my cart
    And the current cart should have the following contextual reductions:
      | cartrule14        | 9.905  |
      | cartrule15        | 19.81 |
    And my cart total should be precisely 16.9 tax included

  Scenario: 1 product in my cart, 2 same cart rules offering the same product as gift
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product_1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product_2" with a price of 35.567 and 1000 items in stock
    Given there is a cart rule named "cartrule16" that applies no discount with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule16" offers a gift product "product_1"
    Given cart rule "cartrule16" has a discount code "foo16"
    Given there is a cart rule named "cartrule17" that applies no discount with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule17" offers a gift product "product_1"
    Given cart rule "cartrule17" has a discount code "foo17"
    When I add 1 items of product "product_1" in my cart
    And I use the discount "cartrule16"
    Then I should have 2 products in my cart
    And the current cart should have the following contextual reductions:
      | cartrule16        | 19.81 |
    And my cart total should be precisely 26.81 tax included
    When I use the discount "cartrule17"
    Then I should have 3 products in my cart
    And the current cart should have the following contextual reductions:
      | cartrule16        | 19.81 |
      | cartrule17        | 19.81 |
    And my cart total should be precisely 26.82 tax included
    # 3*19.812 = 59.436 rounded to 59.44 - 19.81*2 = 19.82 + 7 (shipping) = 26.82

  Scenario: 2 products in cart, one cart rule offering a gift (in stock) and a global 10% discount
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    Given product "product4" is out of stock
    Given there is a cart rule named "cartrule12" that applies a percent discount of 10.0% with priority 12, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule12" has a discount code "foo12"
    Given cart rule "cartrule12" offers a gift product "product3"
    When I add 2 items of product "product1" in my cart
    When I add 3 items of product "product3" in my cart
    When I am not allowed to add 1 items of product "product4" in my cart
    When I use the discount "cartrule12"
    Then I should have 6 products in my cart
    Then my cart total should be 126.8692 tax included
    # Test known not to be reliable on previous
    # Then my cart total using previous calculation method should be 126.8692 tax included
