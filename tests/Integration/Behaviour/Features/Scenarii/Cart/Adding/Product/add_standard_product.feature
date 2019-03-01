@database-feature
Feature: Add product in cart
  As a customer
  I must be able to correctly add products in my cart

  Scenario: Add product(s) in cart
    Given I have an empty default cart
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Then Remaining quantity of product named product1 should be 1000
    When I add product named product1 in my cart with quantity 11
    Then Quantity of product named product1 in my cart should be 11
    Then Remaining quantity of product named product1 should be 989

  Scenario: Cannot add product in cart with quantity exceeding availability
    Given I have an empty default cart
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Then I am not able to add product named product1 in my cart with quantity 1100
    Then Quantity of product named product1 in my cart should be 0
    Then Remaining quantity of product named product1 should be 1000

  Scenario: Be able to add out of stock product if configuration allows it
    Given Shop configuration of PS_ORDER_OUT_OF_STOCK is set to 1
    Given I have an empty default cart
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1100
    Then Quantity of product named product1 in my cart should be 1100
    Then Remaining quantity of product named product1 should be -100

  Scenario: change product quantity
    Given I have an empty default cart
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I change quantity of product named product1 in my cart with quantity 1 and operator up, result of change is OK
    Then Quantity of product named product1 in my cart should be 1
    When I change quantity of product named product1 in my cart with quantity 1 and operator up, result of change is OK
    Then Quantity of product named product1 in my cart should be 2
    When I change quantity of product named product1 in my cart with quantity 2 and operator up, result of change is OK
    Then Quantity of product named product1 in my cart should be 4
    When I change quantity of product named product1 in my cart with quantity 2 and operator nothing, result of change is KO
    Then Quantity of product named product1 in my cart should be 4
    When I change quantity of product named product1 in my cart with quantity 2 and operator down, result of change is OK
    Then Quantity of product named product1 in my cart should be 2
    When I change quantity of product named product1 in my cart with quantity 1 and operator down, result of change is OK
    Then Quantity of product named product1 in my cart should be 1
    When I change quantity of product named product1 in my cart with quantity 1 and operator down, result of change is OK
    Then Quantity of product named product1 in my cart should be 0
    When I change quantity of product named product1 in my cart with quantity 1 and operator down, result of change is OK
    Then Quantity of product named product1 in my cart should be 0
    When I change quantity of product named product1 in my cart with quantity 1 and operator nothing, result of change is OK
    Then Quantity of product named product1 in my cart should be 0
