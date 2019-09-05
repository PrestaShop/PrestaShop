@reset-database-before-feature
Feature: Cart rule (percent) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, 2 cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 10.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" has a discount code "foo3"
    Then I should have 0 different products in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule3"
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, 2x % global cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 10.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" has a discount code "foo3"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule3"
    Then my cart total should be 15.9154 tax included
    #known to fail on previous
    #Then my cart total using previous calculation method should be 15.9154 tax included

  Scenario: one product in cart, quantity 3, one 50% global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 10.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" has a discount code "foo3"
    When I add 3 items of product "product1" in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule3"
    Then my cart total should be 33.75 tax included
    #known to fail on previous
    #Then my cart total using previous calculation method should be 33.75 tax included

  Scenario: 3 products in cart, several quantities, 2x % global cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 10.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" has a discount code "foo3"
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule3"
    Then my cart total should be 76.93 tax included
    #known to fail on previous
    #Then my cart total using previous calculation method should be 76.93 tax included
