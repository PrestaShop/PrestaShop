@reset-database-before-feature
Feature: Cart calculation with rounding mode UP
  As a customer
  I must be able to have correct cart total when configuration is set to different rounding modes

  Scenario: Empty cart
    Given I have an empty default cart
    Given Specific shop configuration of "rounding mode" is set to PS_ROUND_UP
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given Specific shop configuration of "rounding mode" is set to PS_ROUND_UP
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 26.82
    Then Expected total of my cart tax included should be 26.82 with previous calculation method

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given Specific shop configuration of "rounding mode" is set to PS_ROUND_UP
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 66.44
    Then Expected total of my cart tax included should be 66.44 with previous calculation method

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given Specific shop configuration of "rounding mode" is set to PS_ROUND_UP
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 162.41
    Then Expected total of my cart tax included should be 162.41 with previous calculation method
