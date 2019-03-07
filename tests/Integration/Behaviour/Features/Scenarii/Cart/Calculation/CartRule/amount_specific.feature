@reset-database-before-feature
Feature: Cart rule (amount) calculation with one cart rule restricted to one product
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, one voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a cart rule with name cartrule8 and amount discount of 5 and priority of 8 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule8 is restricted to product named product2
    Given Cart rule named cartrule8 has a code: foo8
    Then Distinct product count in my cart should be 0
    When I add cart rule named cartrule8 to my cart
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1, one specific 5€ voucher on product2
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a cart rule with name cartrule8 and amount discount of 5 and priority of 8 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule8 is restricted to product named product2
    Given Cart rule named cartrule8 has a code: foo8
    When I add product named product1 in my cart with quantity 1
    When I add cart rule named cartrule8 to my cart
    Then Expected total of my cart tax included should be 26.812
    Then Expected total of my cart tax included should be 26.812 with previous calculation method

  Scenario: one product in cart, quantity 3, one specific 5€ voucher on product #2
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a cart rule with name cartrule8 and amount discount of 5 and priority of 8 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule8 is restricted to product named product2
    Given Cart rule named cartrule8 has a code: foo8
    When I add product named product1 in my cart with quantity 3
    When I add cart rule named cartrule8 to my cart
    Then Expected total of my cart tax included should be 66.436
    Then Expected total of my cart tax included should be 66.436 with previous calculation method

  Scenario: 3 products in cart, several quantities, one specific 5€ voucher on product #2
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a cart rule with name cartrule8 and amount discount of 5 and priority of 8 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule8 is restricted to product named product2
    Given Cart rule named cartrule8 has a code: foo8
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    When I add cart rule named cartrule8 to my cart
    Then Expected total of my cart tax included should be 157.4
    Then Expected total of my cart tax included should be 157.4 with previous calculation method

  Scenario: 3 products in cart, several quantities, one specific 500€ voucher on product #2
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a cart rule with name cartrule9 and amount discount of 500 and priority of 9 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule9 is restricted to product named product2
    Given Cart rule named cartrule9 has a code: foo9
    When I add product named product1 in my cart with quantity 3
    When I add product named product2 in my cart with quantity 2
    When I add product named product3 in my cart with quantity 1
    When I add cart rule named cartrule9 to my cart
    Then Expected total of my cart tax included should be 97.624
    #known to fail on previous
    #Then Expected total of my cart tax included should be 97.624 with previous calculation method
