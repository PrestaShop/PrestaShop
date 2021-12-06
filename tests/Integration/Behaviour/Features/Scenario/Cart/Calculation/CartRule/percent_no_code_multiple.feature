@restore-all-tables-before-feature
Feature: Cart rule (percent) calculation with multiple cart rules without code
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: 3 products in cart, several quantities, 2x % global cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule14" that applies a percent discount of 10.0% with priority 14, quantity of 1000 and quantity per user 1000
    Given there is a cart rule named "cartrule15" that applies a percent discount of 10.0% with priority 15, quantity of 1000 and quantity per user 1000
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I use the discount "cartrule14"
    When I use the discount "cartrule15"
    Then my cart total should be 132.874 tax included
    #known to fail on previous
    #Then my cart total using previous calculation method should be 132.874 tax included
