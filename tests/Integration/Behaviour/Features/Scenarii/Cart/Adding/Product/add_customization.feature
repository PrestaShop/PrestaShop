Feature: Add product customization in cart
  As a customer
  I must be able to correctly add product customizations in my cart

  Scenario: Add customization in cart
    Given I have an empty default cart
    Given there is a product with name "product8" and price 26.364 and quantity 30
    Given product with name "product8" has a customization field with name "custo1"
    When I add customization named "custo1" of product named "product8" in my cart with quantity 11
    Then Quantity of customization named "custo1" of product named "product8" in my cart should be 11
    Then Remaining quantity of customization named "custo1" for product named "product8" should be 19

  Scenario: Cannot add customization in cart with quantity exceeding availability
    Given I have an empty default cart
    Given there is a product with name "product8" and price 26.364 and quantity 30
    Given product with name "product8" has a customization field with name "custo1"
    Then I am not able to add customization named "custo1" of product named "product8" in my cart with quantity 41
    Then Quantity of customization named "combi1" of product named "product8" in my cart should be 0
    Then Remaining quantity of customization named "custo1" for product named "product8" should be 30

  Scenario: Be able to add out of stock customization if configuration allows it
    Given Shop configuration of "PS_ORDER_OUT_OF_STOCK" is set to 1
    Given I have an empty default cart
    Given there is a product with name "product8" and price 26.364 and quantity 30
    Given product with name "product8" has a customization field with name "custo1"
    When I add customization named "custo1" of product named "product8" in my cart with quantity 41
    Then Quantity of customization named "custo1" of product named "product8" in my cart should be 41
    Then Remaining quantity of customization named "custo1" for product named "product8" should be "-11"
