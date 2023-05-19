# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-percent-multiple
@restore-all-tables-before-feature
@fo-cart-rule-percent-multiple
@clear-cache-before-feature
Feature: Cart rule (percent) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
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
    And my cart total should be 26.81 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be 16.9 tax included
    When I apply the voucher code "foo3"
    Then my cart total should be 15.92 tax included
    And the current cart should have the following contextual reductions:
      | reference | reduction |
      | cartrule2 | 9.905     |
      | cartrule3 | 0.9905    |

  Scenario: one product in cart, quantity 3, one 50% global cartRule
    Given I have an empty default cart
    And I add 3 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 66.44 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be 36.72 tax included
    When I apply the voucher code "foo3"
    Then my cart total should be 33.75 tax included
    And the current cart should have the following contextual reductions:
      | reference | reduction |
      | cartrule2 | 29.72     |
      | cartrule3 | 2.972     |

  Scenario: 3 products in cart, several quantities, 2x % global cartRules
    Given I have an empty default cart
    And I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 162.4 tax included
    When I apply the voucher code "foo2"
    Then my cart total should be 84.7 tax included
    When I apply the voucher code "foo3"
    Then my cart total should be 76.93 tax included
    And the current cart should have the following contextual reductions:
      | reference | reduction |
      | cartrule2 | 77.705    |
      | cartrule3 | 7.7705    |

  @restore-cart-rules-after-scenario
  Scenario: one product in cart, one cart rule free shipping, one cart rule 10%
    Given I have an empty default cart
    When I add 1 items of product "product1" in my cart
    # checking cart total and shipping before cart rules exists, to assert that shipping price will be reduced later
    # because when ONLY_SHIPPING is calculated it does not count cart rules
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be precisely 26.81 tax included
    Given I have an empty default cart
    And there is a cart rule "freeshipping" with following properties:
      | name[en-US]       | freeshipping |
      | total_quantity    | 1000         |
      | quantity_per_user | 1000         |
      | priority          | 1            |
      | free_shipping     | true         |
    And there is a cart rule "10percent" with following properties:
      | name[en-US]                  | 10percent              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 2                      |
      | discount_percentage          | 10                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    # now adding same products just with cart rules and checking the order total is correct (shipping and percentage applied)
    When I add 1 items of product "product1" in my cart
    And my cart total should be precisely 17.83 tax included
    And the current cart should have the following contextual reductions:
      | reference    | reduction |
      | freeshipping | 7         |
      | 10percent    | 1.981     |

  Scenario: 2 combinable cart rules are applied correctly
    Given I have an empty default cart
    And there is a cart rule "cartrule50" with following properties:
      | name[en-US]                  | cartrule50             |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 1                      |
      | code                         | foo50                  |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And there is a cart rule "cartrule30" with following properties:
      | name[en-US]                  | cartrule30             |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 2                      |
      | code                         | foo30                  |
      | discount_percentage          | 30                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And I add 1 items of product "product1" in my cart
    And I should have 1 different products in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be precisely 26.81 tax included
    When I apply the voucher code "foo50"
    Then my cart total should be 16.9 tax included
    When I apply the voucher code "foo30"
    Then my cart total should be precisely 13.93 tax included

  Scenario: 3 products in cart, several quantities, 2x % global cartRules without codes
    Given I have an empty default cart
    When I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    Then my cart total should be 162.4 tax included
    And my cart total shipping fees should be 7.0 tax included
    Given I have an empty default cart
    And there is a cart rule "cartrule14" with following properties:
      | name[en-US]                  | cartrule14             |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 14                     |
      | free_shipping                | false                  |
      | code                         |                        |
      | discount_percentage          | 10                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And there is a cart rule "cartrule15" with following properties:
      | name[en-US]                  | cartrule15             |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 15                     |
      | free_shipping                | false                  |
      | code                         |                        |
      | discount_percentage          | 10                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    When I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    Then my cart total should be 132.874 tax included
