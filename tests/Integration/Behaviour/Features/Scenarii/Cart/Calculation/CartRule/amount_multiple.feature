@database-feature
Feature: Cart rule (amount) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, 2 vouchers
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a cart rule with name cartrule4 and amount discount of 5 and priority of 4 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule4 has a code: foo4
    Given There is a cart rule with name cartrule6 and amount discount of 10 and priority of 6 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule6 has a code: foo6
    Then Distinct product count in my cart should be 0
    When I add cart rule named cartrule4 to my cart
    When I add cart rule named cartrule6 to my cart
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1, one 5€ global voucher, one 10€ global voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule4 and amount discount of 5 and priority of 4 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule4 has a code: foo4
    Given There is a cart rule with name cartrule6 and amount discount of 10 and priority of 6 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule6 has a code: foo6
    When I add product named product1 in my cart with quantity 1
    When I add cart rule named cartrule4 to my cart
    When I add cart rule named cartrule6 to my cart
    Then Expected total of my cart tax included should be 11.812
    Then Expected total of my cart tax included should be 11.812 with previous calculation method

  Scenario: one product in cart, quantity 3, one 5€ global voucher, one 10€ global voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule4 and amount discount of 5 and priority of 4 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule4 has a code: foo4
    Given There is a cart rule with name cartrule6 and amount discount of 10 and priority of 6 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule6 has a code: foo6
    When I add product named product1 in my cart with quantity 3
    When I add cart rule named cartrule4 to my cart
    When I add cart rule named cartrule6 to my cart
    Then Expected total of my cart tax included should be 51.436
    Then Expected total of my cart tax included should be 51.436 with previous calculation method

  Scenario: 3 products in cart, several quantities, one 5€ global voucher (reduced product at first place)
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    Given There is a cart rule with name cartrule4 and amount discount of 5 and priority of 4 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule4 has a code: foo4
    Given There is a cart rule with name cartrule6 and amount discount of 10 and priority of 6 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule6 has a code: foo6
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    When I add cart rule named cartrule4 to my cart
    When I add cart rule named cartrule6 to my cart
    Then Expected total of my cart tax included should be 147.4
    Then Expected total of my cart tax included should be 147.4 with previous calculation method
