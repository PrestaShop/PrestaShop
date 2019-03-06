@database-feature
Feature: Cart calculation with only products and gift wrapping
  As a customer
  I must be able to have correct cart total when adding products, and selecting gift wrapping

  Scenario: Empty cart
    Given Shop configuration of PS_GIFT_WRAPPING is set to 1
    Given Shop configuration of PS_GIFT_WRAPPING_PRICE is set to 5.3
    Given I have an empty default cart
    When I select gift wrapping
    Then Distinct product count in my cart should be 0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1
    Given Shop configuration of PS_GIFT_WRAPPING is set to 1
    Given Shop configuration of PS_GIFT_WRAPPING_PRICE is set to 5.3
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    When I select gift wrapping
    Then Expected total of my cart tax included should be 32.112
    Then Expected total of my cart tax included should be 32.112 with previous calculation method

  Scenario: one product in cart, quantity 3
    Given Shop configuration of PS_GIFT_WRAPPING is set to 1
    Given Shop configuration of PS_GIFT_WRAPPING_PRICE is set to 5.3
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    When I select gift wrapping
    Then Expected total of my cart tax included should be 71.736
    Then Expected total of my cart tax included should be 71.736 with previous calculation method

  Scenario: 3 products in cart, several quantities
    Given Shop configuration of PS_GIFT_WRAPPING is set to 1
    Given Shop configuration of PS_GIFT_WRAPPING_PRICE is set to 5.3
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    When I select gift wrapping
    Then Expected total of my cart tax included should be 167.7
    Then Expected total of my cart tax included should be 167.7 with previous calculation method
