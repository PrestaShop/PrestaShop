# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-percent-multiple
@restore-all-tables-before-feature
@fo-cart-rule-percent-multiple
@clear-cache-before-feature
Feature: Cart rule (percent) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a cart rule "cartrule2" with following properties:
      | name[en-US]                  | cartrule2              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 2                      |
      | free_shipping                | false                  |
      | code                         | foo2                   |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And there is a cart rule "cartrule3" with following properties:
      | name[en-US]                  | cartrule3              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 3                      |
      | free_shipping                | false                  |
      | code                         | foo3                   |
      | discount_percentage          | 10                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |

  Scenario: one product in cart, quantity 1, 2x % global cartRules
    Given I have an empty default cart
    And I add 1 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be precisely 26.812 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be precisely 16.906 tax included
    When I apply the voucher code "foo3"
    Then my cart total should be precisely 15.9154 tax included
    And my cart total using previous calculation method should be precisely 15.9154 tax included
    And the current cart should have the following contextual reductions:
      | reference | reduction |
      | cartrule2 | 9.905     |
      | cartrule3 | 0.9905    |

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
      | reference | reduction |
      | cartrule2 | 29.72     |
      | cartrule3 | 2.972     |
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
      | reference | reduction |
      | cartrule2 | 77.705    |
      | cartrule3 | 7.7705    |
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
      | reference    | reduction |
      | freeshipping | 7         |
      | 10percent    | 1.981     |
    And my cart total should be precisely 17.83 tax included
    And cart shipping fees should be 0.00 tax included

  Scenario: 2 combinable cart rules are applied correctly
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Given there is a cart rule named "cartrule2" that applies a percent discount of 30.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    And my cart total shipping fees should be 7.0 tax excluded
    And my cart total should be 26.8 tax excluded
    When I apply the voucher code "foo1"
    Then my cart total should be 16.9 tax excluded
    When I apply the voucher code "foo2"
    # (19.812*0.5)*0.7 + 7 shipping
    Then my cart total should be 13.9 tax excluded
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart
