#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags calculation-cartrule-percent-multiple

@reset-database-before-feature
@calculation-cartrule-percent-multiple
Feature: Cart rule (percent) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: Empty cart, 2 cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 10.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" has a discount code "foo3"
    Then I should have 0 different products in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule3"
    Then the current cart should have the following contextual reductions:
      | cartrule2        | 0  |
      | cartrule3        | 0  |
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, 2x % global cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 10.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" has a discount code "foo3"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule3"
    Then the current cart should have the following contextual reductions:
      | cartrule2        | 9.905  |
      | cartrule3        | 0.9905 |
    Then my cart total should be 15.9154 tax included
    #known to fail on previous
    #Then my cart total using previous calculation method should be 15.9154 tax included

  Scenario: one product in cart, quantity 3, one 50% global cartRule
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 10.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" has a discount code "foo3"
    When I add 3 items of product "product1" in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule3"
    Then the current cart should have the following contextual reductions:
      | cartrule2        | 29.72 |
      | cartrule3        | 2.972 |
    Then my cart total should be precisely 33.75 tax included

  Scenario: 3 products in cart, several quantities, 2x % global cartRules
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 10.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" has a discount code "foo3"
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I use the discount "cartrule2"
    When I use the discount "cartrule3"
    Then the current cart should have the following contextual reductions:
      | cartrule2        | 77.705 |
      | cartrule3        | 7.7705 |
    Then my cart total should be 76.93 tax included
    #known to fail on previous
    #Then my cart total using previous calculation method should be 76.93 tax included

  Scenario: one product in cart, one cart rule free shipping, one cart rule 10%
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a cart rule named "freeshipping" that applies no discount with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "freeshipping" offers free shipping
    And there is a cart rule named "10percent" that applies a percent discount of 10.0% with priority 2, quantity of 1000 and quantity per user 1000
    When I add 1 items of product "product1" in my cart
    Then the current cart should have the following contextual reductions:
      | freeshipping     | 7     |
      | 10percent        | 1.981 |
    And my cart total should be precisely 17.83 tax included
    And cart shipping fees should be 0.00 tax included
