Feature: Cart Calculation
  As a customer
  I must be able to get correct price for my cart

  Scenario: Add combination in cart
    Given I have an empty default cart
    Given there is a product with name "product7" and price 24.324 and quantity 1000
    Given product with name "product7" has a combination with name "combi1" and quantity 500
    Then Remaining quantity of combination named "combi1" for product named "product7" should be 500
    When I add combination named "combi1" of product named "product7" in my cart with quantity 11
    Then Quantity of combination named "combi1" of product named "product7" in my cart should be 11
    Then Remaining quantity of combination named "combi1" for product named "product7" should be 489

  Scenario: Cannot add combination in cart with quantity exceeding availability
    Given I have an empty default cart
    Given there is a product with name "product7" and price 19.812 and quantity 1000
    Given product with name "product7" has a combination with name "combi1" and quantity 500
    Then I am not able to add combination named "combi1" of product named "product7" in my cart with quantity 600
    Then Quantity of combination named "combi1" of product named "product7" in my cart should be 0
    Then Remaining quantity of combination named "combi1" for product named "product7" should be 500

  Scenario: Be able to add out of stock combination if configuration allows it
    Given Shop configuration of "PS_ORDER_OUT_OF_STOCK" is set to 1
    Given I have an empty default cart
    Given there is a product with name "product7" and price 19.812 and quantity 1000
    Given product with name "product7" has a combination with name "combi1" and quantity 500
    Given product with name "product7" is out of stock
    When I add combination named "combi1" of product named "product7" in my cart with quantity 600
    Then Quantity of combination named "combi1" of product named "product7" in my cart should be 600
    Then Remaining quantity of combination named "combi1" for product named "product7" should be "-100"
