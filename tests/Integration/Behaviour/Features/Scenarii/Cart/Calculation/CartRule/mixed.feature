@database-feature
Feature: Cart rule (mixed) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, 2 mixed voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a cart rule with name cartrule2 and percent discount of 50.0% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule2 has a code: foo2
    Given There is a cart rule with name cartrule4 and amount discount of 5 and priority of 4 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule4 has a code: foo4
    Then Distinct product count in my cart should be 0
    When I add cart rule named cartrule2 to my cart
    When I add cart rule named cartrule4 to my cart
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1, one 50% global voucher, one 5€ global voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule2 and percent discount of 50.0% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule2 has a code: foo2
    Given There is a cart rule with name cartrule4 and amount discount of 5 and priority of 4 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule4 has a code: foo4
    When I add product named product1 in my cart with quantity 1
    When I add cart rule named cartrule2 to my cart
    When I add cart rule named cartrule4 to my cart
    Then Expected total of my cart tax included should be 11.906
    Then Expected total of my cart tax included should be 11.906 with previous calculation method

  Scenario: one product in cart, quantity 1, one 50% global voucher, one 500€ global voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule2 and percent discount of 50.0% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule2 has a code: foo2
    Given There is a cart rule with name cartrule5 and amount discount of 500 and priority of 5 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule5 has a code: foo5
    When I add product named product1 in my cart with quantity 3
    When I add cart rule named cartrule2 to my cart
    When I add cart rule named cartrule5 to my cart
    Then Expected total of my cart tax included should be 7.0
    Then Expected total of my cart tax included should be 7.0 with previous calculation method

  Scenario: one product in cart, quantity 3, one 5€ global voucher, one 50% global voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule4 and amount discount of 5 and priority of 4 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule4 has a code: foo4
    Given There is a cart rule with name cartrule7 and percent discount of 50.0% and priority of 7 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule7 has a code: foo7
    When I add product named product1 in my cart with quantity 3
    When I add cart rule named cartrule4 to my cart
    When I add cart rule named cartrule7 to my cart
    Then Expected total of my cart tax included should be 34.218
    #known to fail on previous
    #Then Expected total of my cart tax included should be 34.218 with previous calculation method

  Scenario: one product in cart, quantity 3, one 500€ global voucher, one 50% global voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule5 and amount discount of 500 and priority of 5 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule5 has a code: foo5
    Given There is a cart rule with name cartrule7 and percent discount of 50.0% and priority of 7 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule7 has a code: foo7
    When I add product named product1 in my cart with quantity 3
    When I add cart rule named cartrule5 to my cart
    When I add cart rule named cartrule7 to my cart
    Then Expected total of my cart tax included should be 7.0
    Then Expected total of my cart tax included should be 7.0 with previous calculation method

  Scenario: 3 products with several quantities in cart, one 5€ global voucher, one 50% global voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a cart rule with name cartrule4 and amount discount of 5 and priority of 4 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule4 has a code: foo4
    Given There is a cart rule with name cartrule7 and percent discount of 50.0% and priority of 7 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule7 has a code: foo7
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    When I add cart rule named cartrule4 to my cart
    When I add cart rule named cartrule7 to my cart
    Then Expected total of my cart tax included should be 82.205
    #known to fail on previous
    #Then Expected total of my cart tax included should be 82.205 with previous calculation method
