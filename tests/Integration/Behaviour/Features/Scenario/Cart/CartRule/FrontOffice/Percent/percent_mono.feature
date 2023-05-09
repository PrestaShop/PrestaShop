# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-percent-mono
@restore-all-tables-before-feature
@fo-cart-rule-percent-mono
Feature: Cart rule (percent) calculation with one cart rule
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, one cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Then I should have 0 different products in my cart
    When I use the discount "cartrule2"
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, one 50% global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule2"
    Then my cart total should be 16.906 tax included
    Then my cart total using previous calculation method should be 16.906 tax included

  Scenario: one product in cart, quantity 3, one 50% global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    When I add 3 items of product "product1" in my cart
    When I use the discount "cartrule2"
    Then my cart total should be 36.718 tax included
    Then my cart total using previous calculation method should be 36.718 tax included

  Scenario: 3 products in cart, several quantities, one 5€ global cartRule (reduced product at first place)
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I use the discount "cartrule2"
    Then my cart total should be 84.7 tax included
    Then my cart total using previous calculation method should be 84.7 tax included

  Scenario: 1 product in cart, percentage reduction cart rule is applied correctly
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    And total cart shipping fees should be 7.0 tax excluded
    And my cart total should be 26.8 tax excluded
    When I apply the discount code "foo1"
    Then my cart total should be 16.9 tax excluded
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart
