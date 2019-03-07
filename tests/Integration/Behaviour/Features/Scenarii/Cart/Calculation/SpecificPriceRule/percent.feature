@reset-database-before-feature
Feature: Cart calculation with specific price rule (percent)
  As a customer
  I must be able to have correct cart total when adding specific price rule

  Scenario: 1 product in cart, quantity 1, one rule percent from quantity 1
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule1 and reduction in percentage and reduction value of 23 and minimal quantity of 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 22.25524
    Then Expected total of my cart tax included should be 22.25524 with previous calculation method

  Scenario: 1 product in cart, quantity 1, one rule percent from quantity 2
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule2 and reduction in percentage and reduction value of 15 and minimal quantity of 2
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 26.812
    Then Expected total of my cart tax included should be 26.812 with previous calculation method

  Scenario: 1 product in cart, quantity 3, one rule percent from quantity 1
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule1 and reduction in percentage and reduction value of 23 and minimal quantity of 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 52.76572
    Then Expected total of my cart tax included should be 52.76572 with previous calculation method

  Scenario: 1 product in cart, quantity 3, one rule percent from quantity 2
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule2 and reduction in percentage and reduction value of 15 and minimal quantity of 2
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 57.5206
    Then Expected total of my cart tax included should be 57.5206 with previous calculation method

  Scenario: 3 products in cart, several quantities, one rule percent from quantity 1
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule1 and reduction in percentage and reduction value of 23 and minimal quantity of 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 126.658
    Then Expected total of my cart tax included should be 126.658 with previous calculation method

  Scenario: 3 products in cart, several quantities, one rule percent from quantity 2
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule2 and reduction in percentage and reduction value of 15 and minimal quantity of 2
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 143.7682
    Then Expected total of my cart tax included should be 143.7682 with previous calculation method
