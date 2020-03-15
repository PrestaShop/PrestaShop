@reset-database-before-feature
Feature: Cart rule (mixed) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, 2 mixed cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    Then I should have 0 different products in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule4"
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, one 50% global cartRule, one 5€ global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule4"
    Then my cart total should be 11.906 tax included
    Then my cart total using previous calculation method should be 11.906 tax included

  Scenario: one product in cart, quantity 1, one 50% global cartRule, one 500€ global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule5" that applies an amount discount of 500.0 with priority 5, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule5" has a discount code "foo5"
    When I add 3 items of product "product1" in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule5"
    Then my cart total should be 7.0 tax included
    Then my cart total using previous calculation method should be 7.0 tax included

  Scenario: one product in cart, quantity 3, one 5€ global cartRule, one 50% global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    Given there is a cart rule named "cartrule7" that applies a percent discount of 50.0% with priority 7, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule7" has a discount code "foo7"
    When I add 3 items of product "product1" in my cart
    When I use the discount "cartrule4"
    When I use the discount "cartrule7"
    Then my cart total should be 34.218 tax included
    #known to fail on previous
    #Then my cart total using previous calculation method should be 34.218 tax included

  Scenario: one product in cart, quantity 3, one 500€ global cartRule, one 50% global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule5" that applies an amount discount of 500.0 with priority 5, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule5" has a discount code "foo5"
    Given there is a cart rule named "cartrule7" that applies a percent discount of 50.0% with priority 7, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule7" has a discount code "foo7"
    When I add 3 items of product "product1" in my cart
    When I use the discount "cartrule5"
    When I use the discount "cartrule7"
    Then my cart total should be 7.0 tax included
    Then my cart total using previous calculation method should be 7.0 tax included

  Scenario: 3 products with several quantities in cart, one 5€ global cartRule, one 50% global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule4" that applies an amount discount of 5.0 with priority 4, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule4" has a discount code "foo4"
    Given there is a cart rule named "cartrule7" that applies a percent discount of 50.0% with priority 7, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule7" has a discount code "foo7"
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I use the discount "cartrule4"
    When I use the discount "cartrule7"
    Then my cart total should be 82.205 tax included
    #known to fail on previous
    #Then my cart total using previous calculation method should be 82.205 tax included
