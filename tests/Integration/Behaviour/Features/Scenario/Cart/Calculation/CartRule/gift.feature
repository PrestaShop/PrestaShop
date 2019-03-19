@reset-database-before-feature
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
    Then my cart total using previous calculation method should be 0.0 tax included

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
    Then my cart total using previous calculation method should be 126.8692 tax included
