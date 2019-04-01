@reset-database-before-feature
Feature: Cart rule (amount) calculation with one cart rule restricted to one product
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, one cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a cart rule named "cartrule8" that applies an amount discount of 5.0 with priority 8, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule8" is restricted to product "product2"
    Given cart rule "cartrule8" has a discount code "foo8"
    Then I should have 0 different products in my cart
    When I use the discount "cartrule8"
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, one specific 5€ cartRule on product2
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a cart rule named "cartrule8" that applies an amount discount of 5.0 with priority 8, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule8" is restricted to product "product2"
    Given cart rule "cartrule8" has a discount code "foo8"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule8"
    Then my cart total should be 26.812 tax included
    Then my cart total using previous calculation method should be 26.812 tax included

  Scenario: one product in cart, quantity 3, one specific 5€ cartRule on product #2
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a cart rule named "cartrule8" that applies an amount discount of 5.0 with priority 8, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule8" is restricted to product "product2"
    Given cart rule "cartrule8" has a discount code "foo8"
    When I add 3 items of product "product1" in my cart
    When I use the discount "cartrule8"
    Then my cart total should be 66.436 tax included
    Then my cart total using previous calculation method should be 66.436 tax included

  Scenario: 3 products in cart, several quantities, one specific 5€ cartRule on product #2
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule8" that applies an amount discount of 5.0 with priority 8, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule8" is restricted to product "product2"
    Given cart rule "cartrule8" has a discount code "foo8"
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I use the discount "cartrule8"
    Then my cart total should be 157.4 tax included
    Then my cart total using previous calculation method should be 157.4 tax included

  Scenario: 3 products in cart, several quantities, one specific 500€ cartRule on product #2
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule9" that applies an amount discount of 500.0 with priority 8, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule9" is restricted to product "product2"
    Given cart rule "cartrule9" has a discount code "foo9"
    When I add 3 items of product "product1" in my cart
    When I add 2 items of product "product2" in my cart
    When I add 1 items of product "product3" in my cart
    When I use the discount "cartrule9"
    Then my cart total should be 97.624 tax included
    #known to fail on previous
    #Then my cart total using previous calculation method should be 97.624 tax included
