@reset-database-before-feature
Feature: Cart rule (percent) calculation with one cart rule
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, one voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a cart rule with name cartrule2 and percent discount of 50.0% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule2 has a code: foo2
    Then Distinct product count in my cart should be 0
    When I add cart rule named cartrule2 to my cart
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1, one 50% global voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule2 and percent discount of 50.0% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule2 has a code: foo2
    When I add product named product1 in my cart with quantity 1
    When I add cart rule named cartrule2 to my cart
    Then Expected total of my cart tax included should be 16.906
    Then Expected total of my cart tax included should be 16.906 with previous calculation method

  Scenario: one product in cart, quantity 3, one 50% global voucher
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule2 and percent discount of 50.0% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule2 has a code: foo2
    When I add product named product1 in my cart with quantity 3
    When I add cart rule named cartrule2 to my cart
    Then Expected total of my cart tax included should be 36.718
    Then Expected total of my cart tax included should be 36.718 with previous calculation method

  Scenario: 3 products in cart, several quantities, one 5€ global voucher (reduced product at first place)
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a cart rule with name cartrule2 and percent discount of 50.0% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule2 has a code: foo2
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    When I add cart rule named cartrule2 to my cart
    Then Expected total of my cart tax included should be 84.7
    Then Expected total of my cart tax included should be 84.7 with previous calculation method
