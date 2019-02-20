Feature: Add cart rule in cart
  As a customer
  I must be able to correctly add cart rules in my cart

  Scenario: No product in cart should give a not valid cart rule insertion
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a cart rule with name "cartrule1" and percent discount of 50% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named "cartrule1" has a code: "foo1"
    Then Product count in my cart should be 0
    Then Cart rule named "cartrule1" cannot be applied to my cart
    When I add cart rule named "cartrule1" to my cart
    When Some cart rules exist today for customer with id 0
    Then Total product count in my cart should be 0

  Scenario: 1 product in cart, cart rule is inserted correctly
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name "product1" and price 19.812 and quantity 1000
    Given There is a cart rule with name "cartrule1" and percent discount of 50% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named "cartrule1" has a code: "foo1"
    When I add product named "product1" in my cart with quantity 1
    Then Product count in my cart should be 1
    Then Cart rule named "cartrule1" can be applied to my cart
    When I add cart rule named "cartrule1" to my cart
    When Some cart rules exist today for customer with id 0
    Then Total product count in my cart should be 1

  Scenario: 1 product in cart, cart rules are inserted correctly
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name "product1" and price 19.812 and quantity 1000
    Given There is a cart rule with name "cartrule1" and percent discount of 50% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named "cartrule1" has a code: "foo1"
    Given There is a cart rule with name "cartrule2" and percent discount of 50% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named "cartrule2" has a code: "foo2"
    When I add product named "product1" in my cart with quantity 1
    Then Product count in my cart should be 1
    Then Cart rule named "cartrule1" can be applied to my cart
    When I add cart rule named "cartrule1" to my cart
    Then Cart rule named "cartrule2" can be applied to my cart
    When I add cart rule named "cartrule2" to my cart
    When Some cart rules exist today for customer with id 0
    Then Total product count in my cart should be 1

  Scenario: 1 product in cart, double cart rule not inserted
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name "product1" and price 19.812 and quantity 1000
    Given There is a cart rule with name "cartrule1" and percent discount of 50% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named "cartrule1" has a code: "foo1"
    When I add product named "product1" in my cart with quantity 1
    Then Product count in my cart should be 1
    Then Cart rule named "cartrule1" can be applied to my cart
    When I add cart rule named "cartrule1" to my cart
    Then Cart rule named "cartrule1" cannot be applied to my cart
    When I add cart rule named "cartrule1" to my cart
    When Some cart rules exist today for customer with id 0
    Then Total product count in my cart should be 1

  Scenario: 1 product in cart, cart rule giving gift, and global cart rule should be inserted without error
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name "product1" and price 19.812 and quantity 1000
    Given there is a product with name "product3" and price 31.188 and quantity 1000
    Given There is a cart rule with name "cartrule1" and percent discount of 50% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named "cartrule1" has a code: "foo1"
    Given There is a cart rule with name "cartrule12" and percent discount of 10% and priority of 12 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named "cartrule12" has a code: "foo12"
    Given Cart rule named "cartrule12" has a gift product named "product3"
    When I add product named "product1" in my cart with quantity 1
    Then Product count in my cart should be 1
    Then Cart rule named "cartrule12" can be applied to my cart
    When I add cart rule named "cartrule12" to my cart
    Then Cart rule named "cartrule1" can be applied to my cart
    When I add cart rule named "cartrule1" to my cart
    When Some cart rules exist today for customer with id 0
    Then Total product count in my cart should be 2

  Scenario: 1 product in cart, cart rule giving gift out of stock, and global cart rule should be inserted without error (test PR #8361)
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given there is a product with name "product1" and price 19.812 and quantity 1000
    Given there is a product with name "product4" and price 35.567 and quantity 1000
    Given product with name "product4" is out of stock
    Given There is a cart rule with name "cartrule1" and percent discount of 50% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named "cartrule1" has a code: "foo1"
    Given There is a cart rule with name "cartrule13" and percent discount of 10% and priority of 13 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named "cartrule13" has a code: "foo13"
    Given Cart rule named "cartrule13" has a gift product named "product4"
    When I add product named "product1" in my cart with quantity 1
    Then Product count in my cart should be 1
    Then Cart rule named "cartrule13" can be applied to my cart
    When I add cart rule named "cartrule13" to my cart
    Then Cart rule named "cartrule1" can be applied to my cart
    When I add cart rule named "cartrule1" to my cart
    When Some cart rules exist today for customer with id 0
    Then Total product count in my cart should be 1
