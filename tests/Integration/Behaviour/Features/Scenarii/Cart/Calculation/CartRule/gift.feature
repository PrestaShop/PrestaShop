@database-feature
Feature: Cart calculation with cart rules giving gift
  As a customer
  I must be able to have correct cart total when adding products, and adding cart rule with gift

  Scenario: 1 product in cart (out of stock), 1 cart rule give it as a gift, offering a gift (out of stock) and a global 10% discount
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product4 and price 35.567 and quantity 1000
    Given Product with name product4 is out of stock
    Given There is a cart rule with name cartrule13 and percent discount of 10.0% and priority of 13 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule13 has a code: foo13
    Given Cart rule named cartrule13 has a gift product named product4
    When I am not able to add product named product4 in my cart with quantity 1
    When I add cart rule named cartrule13 to my cart
    Then Total product count in my cart should be 0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: 2 products in cart, one cart rule offering a gift (out of stock) and a global 10% discount
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product4 and price 35.567 and quantity 1000
    Given Product with name product4 is out of stock
    Given There is a cart rule with name cartrule13 and percent discount of 10.0% and priority of 13 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule13 has a code: foo13
    Given Cart rule named cartrule13 has a gift product named product4
    When I add product named product1 in my cart with quantity 3
    When I am not able to add product named product4 in my cart with quantity 1
    When I add cart rule named cartrule13 to my cart
    Then Total product count in my cart should be 3
    Then Expected total of my cart tax included should be 60.4924

  Scenario: 2 products in cart, one cart rule offering a gift (in stock) and a global 10% discount
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a product with name product4 and price 35.567 and quantity 1000
    Given Product with name product4 is out of stock
    Given There is a cart rule with name cartrule12 and percent discount of 10.0% and priority of 13 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule12 has a code: foo12
    Given Cart rule named cartrule12 has a gift product named product3
    When I add product named product1 in my cart with quantity 2
    When I add product named product3 in my cart with quantity 3
    When I am not able to add product named product4 in my cart with quantity 1
    When I add cart rule named cartrule12 to my cart
    Then Total product count in my cart should be 6
    Then Expected total of my cart tax included should be 126.8692
    Then Expected total of my cart tax included should be 126.8692 with previous calculation method
