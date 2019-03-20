@current
@reset-database-before-feature
Feature: Cart rule (amount) calculation with one cart rule
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: 4 products in cart, one is virtual, several quantities, one 5€ global voucher
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a product in the catalog named "product8" with a price of 12.345 and 1000 items in stock
    Given product "product8" is virtual
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    When I add 3 items of product "product1" in my cart
    When I add 2 items of product "product2" in my cart
    When I add 1 items of product "product3" in my cart
    When I add 2 items of product "product8" in my cart
    Then cart rule "cartrule4" can be applied to my cart
    When I use the discount "cartrule4"
    Then my cart total should be 182.09 tax included
    Then my cart total using previous calculation method should be 182.09 tax included

  Scenario: Only virtual product in my cart, one 5€ global voucher
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product8" with a price of 12.345 and 1000 items in stock
    Given product "product8" is virtual
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    When I add 2 items of product "product8" in my cart
    Then cart rule "cartrule4" can be applied to my cart
    When I use the discount "cartrule4"
    Then my cart total should be 19.69 tax included
    Then my cart total using previous calculation method should be 19.69 tax included
