@restore-all-tables-before-feature
Feature: Cart calculation with specific price rule (percent multiple)
  As a customer
  I must be able to have correct cart total when adding specific price rule

  Scenario: 1 product in cart, quantity 1, 2 rule percent from quantity 1, first is used
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule1" with a percent discount of 23% and minimum quantity of 1
    Given there is a specific price rule named "priceRule2" with a percent discount of 15% and minimum quantity of 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 22.25524 tax included
    Then my cart total using previous calculation method should be 22.25524 tax included

  Scenario: 1 product in cart, quantity 1, 2 rule percent from quantity 1, reversed, first is used
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule2" with a percent discount of 15% and minimum quantity of 1
    Given there is a specific price rule named "priceRule1" with a percent discount of 23% and minimum quantity of 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 23.8402 tax included
    Then my cart total using previous calculation method should be 23.8402 tax included
