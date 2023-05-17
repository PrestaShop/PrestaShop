# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags set-cart-rule-restrictions
@restore-all-tables-before-feature
@set-cart-rule-restrictions
Feature: Set cart rule restrictions in BO
  PrestaShop allows BO users to add and remove restrictions of cart rule combinations,
  which defines which cart rules can or cannot be compatible in the same cart
  As a BO user I must be able to edit cart rules compatibility

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    And currency "usd" is the default one
    And language with iso code "en" is the default one
    And I create cart rule "rule_free_shipping_1" with following properties:
      | name[en-US]       | free shipping 1      |
      | is_active         | true                 |
      | allow_partial_use | false                |
      | priority          | 1                    |
      | valid_from        | 2022-01-01 11:00:00  |
      | valid_to          | 3001-01-01 12:00:00  |
      | total_quantity    | 10                   |
      | quantity_per_user | 10                   |
      | free_shipping     | true                 |
      | code              | rule_free_shipping_1 |
    And I create cart rule "rule_50_percent" with following properties:
      | name[en-US]                            | Half the price         |
      | is_active                              | true                   |
      | allow_partial_use                      | true                   |
      | priority                               | 2                      |
      | valid_from                             | 2022-01-01 11:00:00    |
      | valid_to                               | 3001-01-01 12:00:00    |
      | total_quantity                         | 10                     |
      | quantity_per_user                      | 12                     |
      | free_shipping                          | false                  |
      | code                                   | rule_50_percent        |
      | reduction_percentage                   | 50                     |
      | reduction_apply_to_discounted_products | false                  |
      | discount_application_type              | order_without_shipping |
    And I create cart rule "rule_70_percent" with following properties:
      | name[en-US]                            | Half the price         |
      | is_active                              | true                   |
      | allow_partial_use                      | true                   |
      | priority                               | 3                      |
      | valid_from                             | 2022-01-01 11:00:00    |
      | valid_to                               | 3001-01-01 12:00:00    |
      | total_quantity                         | 10                     |
      | quantity_per_user                      | 12                     |
      | free_shipping                          | false                  |
      | code                                   | rule_70_percent        |
      | reduction_percentage                   | 70                     |
      | reduction_apply_to_discounted_products | false                  |
      | discount_application_type              | order_without_shipping |
    And cart rule "rule_free_shipping_1" should have the following properties:
      | restricted cart rules |  |
    And cart rule "rule_50_percent" should have the following properties:
      | restricted cart rules |  |
    And cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules |  |

  Scenario: Restrict cart rule combinations
    When I restrict following cart rules for cart rule rule_free_shipping_1:
      | rule_50_percent |
    Then cart rule "rule_free_shipping_1" should have the following properties:
      | restricted cart rules | rule_50_percent |
    And cart rule "rule_50_percent" should have the following properties:
      | restricted cart rules | rule_free_shipping_1 |
    And cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules |  |
    When I restrict following cart rules for cart rule rule_free_shipping_1:
      | rule_50_percent |
      | rule_70_percent |
    Then cart rule "rule_free_shipping_1" should have the following properties:
      | restricted cart rules | rule_50_percent,rule_70_percent |
    And cart rule "rule_50_percent" should have the following properties:
      | restricted cart rules | rule_free_shipping_1 |
    And cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules | rule_free_shipping_1 |

  @restore-cart-rules-before-scenario
  Scenario: Provide non-existing ids for cart rule restriction
    When I restrict cart rules for rule_free_shipping_1 providing non-existing cart rules
    Then I should get cart rule error about "non-existing cart rule"
    And cart rule "rule_free_shipping_1" should have the following properties:
      | restricted cart rules |  |

  @restore-cart-rules-before-scenario
  Scenario: Restrict cart rule combinations providing the same cart rule that is being edited in the restricted cart rules list
    Given cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules |  |
    When I restrict following cart rules for cart rule rule_70_percent:
      | rule_50_percent |
      | rule_70_percent |
    Then I should get cart rule error about "invalid cart rule restriction"
    And cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules |  |
