@reset-database-before-feature
Feature: Cart calculation with rounding mode HALF_ODD
  As a customer
  I must be able to have correct cart total when configuration is set to different rounding modes

  Scenario: Empty cart
    Given I have an empty default cart
    Given specific shop configuration for "rounding mode" is set to round half odd
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given specific shop configuration for "rounding mode" is set to round half odd
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 26.81 tax included
    Then my cart total using previous calculation method should be 26.81 tax included

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given specific shop configuration for "rounding mode" is set to round half odd
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 3 items of product "product1" in my cart
    Then my cart total should be 66.44 tax included
    Then my cart total using previous calculation method should be 66.44 tax included

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given specific shop configuration for "rounding mode" is set to round half odd
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 162.41 tax included
    Then my cart total using previous calculation method should be 162.41 tax included
