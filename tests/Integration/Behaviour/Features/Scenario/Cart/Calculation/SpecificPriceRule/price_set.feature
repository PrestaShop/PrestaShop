@reset-database-before-feature
Feature: Cart calculation with specific price rule (price set)
  As a customer
  I must be able to have correct cart total when adding specific price rule

  Scenario: 1 product in cart, quantity 1, one rule percent from quantity 1
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule1" with a percent discount of 0% and minimum quantity of 1
    Given specific price rule "priceRule1" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 9.0 tax included
    Then my cart total using previous calculation method should be 9.0 tax included

  Scenario: 1 product in cart, quantity 1, one rule percent from quantity 2
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule2" with a percent discount of 0% and minimum quantity of 2
    Given specific price rule "priceRule2" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 26.812 tax included
    Then my cart total using previous calculation method should be 26.812 tax included

  Scenario: 1 product in cart, quantity 3, one rule percent from quantity 1
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule1" with a percent discount of 0% and minimum quantity of 1
    Given specific price rule "priceRule1" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 3 items of product "product1" in my cart
    Then my cart total should be 13.0 tax included
    Then my cart total using previous calculation method should be 13.0 tax included

  Scenario: 1 product in cart, quantity 3, one rule percent from quantity 2
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule2" with a percent discount of 0% and minimum quantity of 2
    Given specific price rule "priceRule2" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 3 items of product "product1" in my cart
    Then my cart total should be 13.0 tax included
    Then my cart total using previous calculation method should be 13.0 tax included

  Scenario: 3 products in cart, several quantities, one rule percent from quantity 1
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule1" with a percent discount of 0% and minimum quantity of 1
    Given specific price rule "priceRule1" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 19.0 tax included
    Then my cart total using previous calculation method should be 19.0 tax included

  Scenario: 3 products in cart, several quantities, one rule percent from quantity 2
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule2" with a percent discount of 0% and minimum quantity of 2
    Given specific price rule "priceRule2" changes product price to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 48.188 tax included
    Then my cart total using previous calculation method should be 48.188 tax included
