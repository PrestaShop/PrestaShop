@database-feature
Feature: Cart calculation with specific price rule (mixed)
  As a customer
  I must be able to have correct cart total when adding specific price rule

  Scenario: 3 products in cart, several quantities, one rule percent with price set from quantity 1
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule1 and reduction in percentage and reduction value of 10 and minimal quantity of 1
    Given specific price rule with name priceRule1 change product price to 2.0
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 17.8
    Then Expected total of my cart tax included should be 17.8 with previous calculation method

  Scenario: 3 products in cart, several quantities, one rule amount with price set from quantity 1
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule2 and reduction in amount and reduction value of 3 and minimal quantity of 1
    Given specific price rule with name priceRule2 change product price to 2.0
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 7.0
    Then Expected total of my cart tax included should be 7.0 with previous calculation method

  Scenario: 3 products in cart, several quantities, one rule percent with price set from quantity 2
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule3 and reduction in percentage and reduction value of 10 and minimal quantity of 2
    Given specific price rule with name priceRule3 change product price to 2.0
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 47.188
    Then Expected total of my cart tax included should be 47.188 with previous calculation method

  Scenario: 3 products in cart, several quantities, one rule amount with price set from quantity 2
    Given I have an empty default cart
    Given There is a specific price rule with name priceRule4 and reduction in amount and reduction value of 3 and minimal quantity of 2
    Given specific price rule with name priceRule4 change product price to 2.0
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 38.188
    Then Expected total of my cart tax included should be 38.188 with previous calculation method
