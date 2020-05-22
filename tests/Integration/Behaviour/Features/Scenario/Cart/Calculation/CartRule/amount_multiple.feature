@reset-database-before-feature
Feature: Cart rule (amount) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, 2 cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    Given there is a cart rule named "cartrule6" that applies an amount discount of 10.0 with priority 6, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule6" has a discount code "foo6"
    Then I should have 0 different products in my cart
    When I use the discount "cartrule4"
    When I use the discount "cartrule6"
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, one 5€ global cartRule, one 10€ global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    Given there is a cart rule named "cartrule6" that applies an amount discount of 10.0 with priority 6, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule6" has a discount code "foo6"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule4"
    When I use the discount "cartrule6"
    Then my cart total should be 11.812 tax included
    Then my cart total using previous calculation method should be 11.812 tax included

  Scenario: one product in cart, quantity 3, one 5€ global cartRule, one 10€ global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    Given there is a cart rule named "cartrule6" that applies an amount discount of 10.0 with priority 6, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule6" has a discount code "foo6"
    When I add 3 items of product "product1" in my cart
    When I use the discount "cartrule4"
    When I use the discount "cartrule6"
    Then my cart total should be 51.436 tax included
    Then my cart total using previous calculation method should be 51.436 tax included

  Scenario: 3 products in cart, several quantities, one 5€ global cartRule (reduced product at first place)
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    Given there is a cart rule named "cartrule6" that applies an amount discount of 10.0 with priority 6, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule6" has a discount code "foo6"
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I use the discount "cartrule4"
    When I use the discount "cartrule6"
    Then my cart total should be 147.4 tax included
    Then my cart total using previous calculation method should be 147.4 tax included

  Scenario: One product in my cart, one cart rule for free shipping and one for free gift
    Given I have an empty default cart
    Given there is a category named "Awesome"
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product8" with a price of 12.345 and 1000 items in stock
    Given product "product8" is in category "Awesome"
    Given there is a cart rule named "cartrule-free-shipping" that applies an amount discount of 0.0 with priority 1, quantity of 1 and quantity per user 1
    Given cart rule "cartrule-free-shipping" offers free shipping
    Given there is a cart rule named "cartrule-free-gift" that applies an amount discount of 10.0 with priority 1, quantity of 1 and quantity per user 1
    Given cart rule "cartrule-free-gift" is restricted to the category "Awesome" with a quantity of 1
    Given cart rule "cartrule-free-gift" is restricted to product "product8"
    When I add 1 item of product "product8" in my cart
    Then my cart total should be precisely 2.35 tax included
    Then my cart total should be 2.4 tax included
    Then my cart total using previous calculation method should be 2.4 tax included

  Scenario: One product in my cart, one 10€ global cartRule, 2 free gifts global cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule-10" that applies an amount discount of 10.0 with priority 1, quantity of 1 and quantity per user 1
    Given there is a cart rule named "cartrule-free-gift-1" that applies no discount with priority 1, quantity of 1 and quantity per user 1
    Given cart rule "cartrule-free-gift-1" offers a gift product "product2"
    Given there is a cart rule named "cartrule-free-gift-2" that applies no discount with priority 1, quantity of 1 and quantity per user 1
    Given cart rule "cartrule-free-gift-2" offers a gift product "product3"
    When I add 1 item of product "product1" in my cart
    Then I should have 3 products in my cart
    Then my cart total should be precisely 16.81 tax included
    Then my cart total should be 16.8 tax included
    Then my cart total using previous calculation method should be 16.8 tax included

  Scenario: One product in my cart, one 30€ global cartRule (which is superior to the product bought), 2 free gifts global cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule-30" that applies an amount discount of 30.0 with priority 1, quantity of 1 and quantity per user 1
    Given there is a cart rule named "cartrule-free-gift-1" that applies no discount with priority 1, quantity of 1 and quantity per user 1
    Given cart rule "cartrule-free-gift-1" offers a gift product "product2"
    Given there is a cart rule named "cartrule-free-gift-2" that applies no discount with priority 1, quantity of 1 and quantity per user 1
    Given cart rule "cartrule-free-gift-2" offers a gift product "product3"
    When I add 1 item of product "product1" in my cart
    Then I should have 3 products in my cart
    Then my cart total should be precisely 7.0 tax included
    Then my cart total should be 7.0 tax included
    Then my cart total using previous calculation method should be 7.0 tax included

  Scenario: One product in my cart, one 30€ global cartRule (which is superior to the product bought) with free shipping, 2 free gifts global cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule-30" that applies an amount discount of 30.0 with priority 1, quantity of 1 and quantity per user 1
    Given cart rule "cartrule-30" offers free shipping
    Given there is a cart rule named "cartrule-free-gift-1" that applies no discount with priority 1, quantity of 1 and quantity per user 1
    Given cart rule "cartrule-free-gift-1" offers a gift product "product2"
    Given there is a cart rule named "cartrule-free-gift-2" that applies no discount with priority 1, quantity of 1 and quantity per user 1
    Given cart rule "cartrule-free-gift-2" offers a gift product "product3"
    When I add 1 item of product "product1" in my cart
    Then I should have 3 products in my cart
    Then my cart total should be precisely 0.0 tax included
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included
