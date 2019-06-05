@reset-database-before-feature
Feature: Cart rule (amount) calculation with one cart rule offering free shipping
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: One product in cart, one cartRule offering only free shipping
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule4" that applies no discount with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" offers free shipping
    Given cart rule "cartrule4" has a discount code "foo4"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule4"
    Then my cart total should be 19.812 tax included
    Then my cart total using previous calculation method should be 19.812 tax included

  Scenario: One product in cart, one cartRule offering free shipping AND 5€ discount
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" offers free shipping
    Given cart rule "cartrule4" has a discount code "foo4"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule4"
    Then my cart total should be 14.812 tax included
    Then my cart total using previous calculation method should be 14.812 tax included
