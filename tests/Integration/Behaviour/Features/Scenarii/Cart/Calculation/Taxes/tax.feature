# here we need to implement correct fixtures for taxrulegroup/taxrate/address

@database-feature
Feature: Cart calculation with tax
  As a customer
  I must be able to have correct cart total when using taxes

  Scenario: empty cart
    Given I have an empty default cart
    When I set delivery address id to 2
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method
    Then Expected total of my cart tax excluded should be 0.0
    Then Expected total of my cart tax excluded should be 0.0 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method
    Then Expected total of my cart tax excluded should be 0.0
    Then Expected total of my cart tax excluded should be 0.0 with previous calculation method

  Scenario: tax #1: one product in cart, quantity 1
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given Product with name product1 has following tax rule group id: 32
    When I set delivery address id to 2
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 27.60448
    Then Expected total of my cart tax included should be 27.60448 with previous calculation method
    Then Expected total of my cart tax excluded should be 26.812
    Then Expected total of my cart tax excluded should be 26.812 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 26.812
    Then Expected total of my cart tax included should be 26.812 with previous calculation method
    Then Expected total of my cart tax excluded should be 26.812
    Then Expected total of my cart tax excluded should be 26.812 with previous calculation method

  Scenario: tax #2: one product in cart, quantity 1
    Given I have an empty default cart
    Given There is a product with name product5 and price 19.812 and quantity 1000
    Given Product with name product5 has following tax rule group id: 9
    When I set delivery address id to 4
    When I add product named product5 in my cart with quantity 1
    Then Expected total of my cart tax included should be 28.00072
    Then Expected total of my cart tax included should be 28.00072 with previous calculation method
    Then Expected total of my cart tax excluded should be 26.812
    Then Expected total of my cart tax excluded should be 26.812 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 26.812
    Then Expected total of my cart tax included should be 26.812 with previous calculation method
    Then Expected total of my cart tax excluded should be 26.812
    Then Expected total of my cart tax excluded should be 26.812 with previous calculation method

  Scenario: tax #3: one product in cart, quantity 1
    Given I have an empty default cart
    Given There is a product with name product5 and price 19.812 and quantity 1000
    Given Product with name product5 has following tax rule group id: 9
    When I set delivery address id to 1
    When I add product named product5 in my cart with quantity 1
    Then Expected total of my cart tax included should be 19.812
    Then Expected total of my cart tax included should be 19.812 with previous calculation method
    Then Expected total of my cart tax excluded should be 19.812
    Then Expected total of my cart tax excluded should be 19.812 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 19.812
    Then Expected total of my cart tax included should be 19.812 with previous calculation method
    Then Expected total of my cart tax excluded should be 19.812
    Then Expected total of my cart tax excluded should be 19.812 with previous calculation method

  Scenario: tax #1: one product in cart, quantity 3
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given Product with name product1 has following tax rule group id: 32
    When I set delivery address id to 2
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 68.81344
    Then Expected total of my cart tax included should be 68.81344 with previous calculation method
    Then Expected total of my cart tax excluded should be 66.436
    Then Expected total of my cart tax excluded should be 66.436 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 66.436
    Then Expected total of my cart tax included should be 66.436 with previous calculation method
    Then Expected total of my cart tax excluded should be 66.436
    Then Expected total of my cart tax excluded should be 66.436 with previous calculation method

  Scenario: tax #2: one product in cart, quantity 3
    Given I have an empty default cart
    Given There is a product with name product5 and price 19.812 and quantity 1000
    Given Product with name product5 has following tax rule group id: 9
    When I set delivery address id to 4
    When I add product named product5 in my cart with quantity 3
    Then Expected total of my cart tax included should be 70.00216
    Then Expected total of my cart tax included should be 70.00216 with previous calculation method
    Then Expected total of my cart tax excluded should be 66.436
    Then Expected total of my cart tax excluded should be 66.436 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 66.436
    Then Expected total of my cart tax included should be 66.436 with previous calculation method
    Then Expected total of my cart tax excluded should be 66.436
    Then Expected total of my cart tax excluded should be 66.436 with previous calculation method

  Scenario: tax #3: one product in cart, quantity 3
    Given I have an empty default cart
    Given There is a product with name product5 and price 19.812 and quantity 1000
    Given Product with name product5 has following tax rule group id: 9
    When I set delivery address id to 1
    When I add product named product5 in my cart with quantity 3
    Then Expected total of my cart tax included should be 59.436
    Then Expected total of my cart tax included should be 59.436 with previous calculation method
    Then Expected total of my cart tax excluded should be 59.436
    Then Expected total of my cart tax excluded should be 59.436 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 59.436
    Then Expected total of my cart tax included should be 59.436 with previous calculation method
    Then Expected total of my cart tax excluded should be 59.436
    Then Expected total of my cart tax excluded should be 59.436 with previous calculation method

  Scenario: tax #1: 3 products in cart, several quantities
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given Product with name product1 has following tax rule group id: 32
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given Product with name product2 has following tax rule group id: 32
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given Product with name product3 has following tax rule group id: 32
    When I set delivery address id to 2
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 168.616
    Then Expected total of my cart tax included should be 168.616 with previous calculation method
    Then Expected total of my cart tax excluded should be 162.4
    Then Expected total of my cart tax excluded should be 162.4 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 162.4
    Then Expected total of my cart tax included should be 162.4 with previous calculation method
    Then Expected total of my cart tax excluded should be 162.4
    Then Expected total of my cart tax excluded should be 162.4 with previous calculation method

  Scenario: tax #2: 3 products in cart, several quantities
    Given I have an empty default cart
    Given There is a product with name product5 and price 19.812 and quantity 1000
    Given Product with name product5 has following tax rule group id: 9
    Given There is a product with name product6 and price 32.388 and quantity 1000
    Given Product with name product6 has following tax rule group id: 9
    Given There is a product with name product7 and price 31.188 and quantity 1000
    Given Product with name product7 has following tax rule group id: 9
    When I set delivery address id to 4
    When I add product named product5 in my cart with quantity 3
    When I add product named product6 in my cart with quantity 2
    When I add product named product7 in my cart with quantity 1
    Then Expected total of my cart tax included should be 171.724
    Then Expected total of my cart tax included should be 171.724 with previous calculation method
    Then Expected total of my cart tax excluded should be 162.4
    Then Expected total of my cart tax excluded should be 162.4 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 162.4
    Then Expected total of my cart tax included should be 162.4 with previous calculation method
    Then Expected total of my cart tax excluded should be 162.4
    Then Expected total of my cart tax excluded should be 162.4 with previous calculation method

  Scenario: tax #3: 3 products in cart, several quantities
    Given I have an empty default cart
    Given There is a product with name product5 and price 19.812 and quantity 1000
    Given Product with name product5 has following tax rule group id: 9
    Given There is a product with name product6 and price 32.388 and quantity 1000
    Given Product with name product6 has following tax rule group id: 9
    Given There is a product with name product7 and price 31.188 and quantity 1000
    Given Product with name product7 has following tax rule group id: 9
    When I set delivery address id to 1
    When I add product named product5 in my cart with quantity 3
    When I add product named product6 in my cart with quantity 2
    When I add product named product7 in my cart with quantity 1
    Then Expected total of my cart tax included should be 155.4
    Then Expected total of my cart tax included should be 155.4 with previous calculation method
    Then Expected total of my cart tax excluded should be 155.4
    Then Expected total of my cart tax excluded should be 155.4 with previous calculation method
    Given Shop configuration of PS_TAX is set to 0
    Then Expected total of my cart tax included should be 155.4
    Then Expected total of my cart tax included should be 155.4 with previous calculation method
    Then Expected total of my cart tax excluded should be 155.4
    Then Expected total of my cart tax excluded should be 155.4 with previous calculation method
