@reset-database-before-feature
Feature: Cart calculation with specific price rule (mixed)
  As a customer
  I must be able to have correct cart total when adding specific price rule

  Scenario: 3 products in cart, several quantities, one rule percent with price set from quantity 1
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule1" with a percent discount of 10% and minimum quantity of 1
    Given specific price rule "priceRule1" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 17.8 tax included
    Then my cart total using previous calculation method should be 17.8 tax included

  Scenario: 3 products in cart, several quantities, one rule amount with price set from quantity 1
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule2" with an amount discount of 3 and minimum quantity of 1
    Given specific price rule "priceRule2" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 7.0 tax included
    Then my cart total using previous calculation method should be 7.0 tax included

  Scenario: 3 products in cart, several quantities, one rule percent with price set from quantity 2
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule3" with a percent discount of 10% and minimum quantity of 2
    Given specific price rule "priceRule3" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 47.188 tax included
    Then my cart total using previous calculation method should be 47.188 tax included

  Scenario: 3 products in cart, several quantities, one rule amount with price set from quantity 2
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule4" with an amount discount of 3 and minimum quantity of 2
    Given specific price rule "priceRule4" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 38.188 tax included
    Then my cart total using previous calculation method should be 38.188 tax included
