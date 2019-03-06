@database-feature
Feature: Cart calculation with only products
  As a customer
  I must be able to have correct cart total when adding products

  Scenario: Empty cart
    Given I have an empty default cart
    Then Distinct product count in my cart should be 0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 26.812
    Then Expected total of my cart tax included should be 26.812 with previous calculation method

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 66.436
    Then Expected total of my cart tax included should be 66.436 with previous calculation method

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 162.4
    Then Expected total of my cart tax included should be 162.4 with previous calculation method
