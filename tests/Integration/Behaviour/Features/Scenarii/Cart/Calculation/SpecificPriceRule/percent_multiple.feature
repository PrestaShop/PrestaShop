@reset-database-before-feature
Feature: Cart calculation with specific price rule (percent multiple)
  As a customer
  I must be able to have correct cart total when adding specific price rule

  Scenario: 1 product in cart, quantity 1, 2 rule percent from quantity 1, first is used
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule1 and reduction in percentage and reduction value of 23 and minimal quantity of 1
    Given There is a specific price rule with name priceRule2 and reduction in percentage and reduction value of 15 and minimal quantity of 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 22.25524
    Then Expected total of my cart tax included should be 22.25524 with previous calculation method

  Scenario: 1 product in cart, quantity 1, 2 rule percent from quantity 1, reversed, first is used
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule2 and reduction in percentage and reduction value of 15 and minimal quantity of 1
    Given There is a specific price rule with name priceRule1 and reduction in percentage and reduction value of 23 and minimal quantity of 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 23.8402
    Then Expected total of my cart tax included should be 23.8402 with previous calculation method
