@database-feature
Feature: Cart rule (percent) calculation with multiple cart rules restricted to one product
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, 2 vouchers
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given There is a cart rule with name cartrule10 and percent discount of 50.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule10 is restricted to product named product2
    Given Cart rule named cartrule10 has a code: foo10
    Given There is a cart rule with name cartrule11 and percent discount of 10.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule11 is restricted to product named product2
    Given Cart rule named cartrule11 has a code: foo11
    Then Distinct product count in my cart should be 0
    When I add cart rule named cartrule10 to my cart
    When I add cart rule named cartrule11 to my cart
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1, specific 50% voucher on product #2, specific 10% voucher on product #2
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given There is a cart rule with name cartrule10 and percent discount of 50.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule10 is restricted to product named product2
    Given Cart rule named cartrule10 has a code: foo10
    Given There is a cart rule with name cartrule11 and percent discount of 10.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule11 is restricted to product named product2
    Given Cart rule named cartrule11 has a code: foo11
    When I add product named product1 in my cart with quantity 1
    When I add cart rule named cartrule10 to my cart
    When I add cart rule named cartrule11 to my cart
    Then Expected total of my cart tax included should be 26.812
    Then Expected total of my cart tax included should be 26.812 with previous calculation method

  Scenario: one product in cart, quantity 3, specific 50% voucher on product #2, specific 10% voucher on product #2
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given There is a cart rule with name cartrule10 and percent discount of 50.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule10 is restricted to product named product2
    Given Cart rule named cartrule10 has a code: foo10
    Given There is a cart rule with name cartrule11 and percent discount of 10.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule11 is restricted to product named product2
    Given Cart rule named cartrule11 has a code: foo11
    When I add product named product1 in my cart with quantity 3
    When I add cart rule named cartrule10 to my cart
    When I add cart rule named cartrule11 to my cart
    Then Expected total of my cart tax included should be 66.436
    Then Expected total of my cart tax included should be 66.436 with previous calculation method

  Scenario: one product #2 in cart, quantity 3, specific 50% voucher on product #2, specific 10% voucher on product #2
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given There is a cart rule with name cartrule10 and percent discount of 50.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule10 is restricted to product named product2
    Given Cart rule named cartrule10 has a code: foo10
    Given There is a cart rule with name cartrule11 and percent discount of 10.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule11 is restricted to product named product2
    Given Cart rule named cartrule11 has a code: foo11
    When I add product named product2 in my cart with quantity 3
    When I add cart rule named cartrule10 to my cart
    When I add cart rule named cartrule11 to my cart
    Then Expected total of my cart tax included should be 50.7238
    #known to fail on previous
    #Then Expected total of my cart tax included should be 50.7238 with previous calculation method

  Scenario: 3 products in cart, several quantities, specific 50% voucher on product #2, specific 10% voucher on product #2
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    Given There is a cart rule with name cartrule10 and percent discount of 50.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule10 is restricted to product named product2
    Given Cart rule named cartrule10 has a code: foo10
    Given There is a cart rule with name cartrule11 and percent discount of 10.0% and priority of 10 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule11 is restricted to product named product2
    Given Cart rule named cartrule11 has a code: foo11
    When I add product named product1 in my cart with quantity 3
    When I add product named product2 in my cart with quantity 2
    When I add product named product3 in my cart with quantity 1
    When I add cart rule named cartrule10 to my cart
    When I add cart rule named cartrule11 to my cart
    Then Expected total of my cart tax included should be 126.7732
    #known to fail on previous
    #Then Expected total of my cart tax included should be 126.7732 with previous calculation method
