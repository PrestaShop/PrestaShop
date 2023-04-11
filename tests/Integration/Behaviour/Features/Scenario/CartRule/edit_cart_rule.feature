# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags edit-cart-rule
@restore-all-tables-before-feature
@edit-cart-rule
Feature: Add cart rule
  PrestaShop allows BO users to create cart rules
  As a BO user
  I must be able to edit cart rules

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    And currency "usd" is the default one
    Given I create cart rule "cart_rule_1" with following properties:
      | name[en-US]                      | cart rule 1         |
      | highlight                        | true                |
      | is_active                        | true                |
      | allow_partial_use                | true                |
      | priority                         | 1                   |
      | valid_from                       | 2019-01-01 11:05:00 |
      | valid_to                         | 2019-12-01 00:00:00 |
      | total_quantity                   | 11                  |
      | quantity_per_user                | 3                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 0                   |
      | minimum_amount_currency          | usd                 |
      | minimum_amount_tax_included      | false               |
      | minimum_amount_shipping_included | false               |
    And cart rule "cart_rule_1" should have the following properties:
      | name[en-US]                      | cart rule 1         |
      | highlight                        | true                |
      | is_active                        | true                |
      | allow_partial_use                | true                |
      | priority                         | 1                   |
      | valid_from                       | 2019-01-01 11:05:00 |
      | valid_to                         | 2019-12-01 00:00:00 |
      | total_quantity                   | 11                  |
      | quantity_per_user                | 3                   |
      | free_shipping                    | true                |
      # @todo: update the test without providing minimum amount because it should not be mandatory (after PR https://github.com/PrestaShop/PrestaShop/pull/31904)
      | minimum_amount                   | 0                   |
      | minimum_amount_currency          | usd                 |
      # when currency is not provided the default one is used
      | reduction_currency               | usd                 |
      | minimum_amount_tax_included      | false               |
      | minimum_amount_shipping_included | false               |

  Scenario: I edit cart rule and change various properties
    When I edit cart rule cart_rule_1 with following properties:
      | name[en-US]                      | cart rule 1 edited                                 |
      | highlight                        | false                                              |
      | is_active                        | false                                              |
      | allow_partial_use                | false                                              |
      | priority                         | 2                                                  |
      | date_range                       | from: 2019-01-01 11:05:01, to: 2020-12-01 00:00:00 |
      | total_quantity                   | 100                                                |
      | quantity_per_user                | 1                                                  |
      | free_shipping                    | true                                               |
      | minimum_amount                   | 10                                                 |
      | minimum_amount_currency          | chf                                                |
      | minimum_amount_tax_included      | true                                               |
      | minimum_amount_shipping_included | true                                               |
    Then cart rule "cart_rule_1" should have the following properties:
      | name[en-US]                      | cart rule 1 edited  |
      | highlight                        | false               |
      | is_active                        | false               |
      | allow_partial_use                | false               |
      | priority                         | 2                   |
      | valid_from                       | 2019-01-01 11:05:01 |
      | valid_to                         | 2020-12-01 00:00:00 |
      | total_quantity                   | 100                 |
      | quantity_per_user                | 1                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | chf                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
      | reduction_amount                 | 0                   |
      | reduction_tax                    | false               |

  Scenario: I edit cart rule and remove free shipping when it is the only action.
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping | false |
    Then I should get cart rule error about "missing action"
    And cart rule "cart_rule_1" should have the following properties:
      | free_shipping | true |

  Scenario: I edit cart rule by adding amount discount action.
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping             | true                   |
      | reduction_amount          | 10.5                   |
      | reduction_tax             | true                   |
      | reduction_currency        | chf                    |
      | discount_application_type | order_without_shipping |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping             | true                   |
      | reduction_amount          | 10.5                   |
      | reduction_tax             | true                   |
      | reduction_currency        | chf                    |
      | discount_application_type | order_without_shipping |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping             | false                  |
      | reduction_amount          | 11                     |
      | reduction_tax             | false                  |
      | reduction_currency        | usd                    |
      | discount_application_type | order_without_shipping |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping             | false                  |
      | reduction_amount          | 11                     |
      | reduction_tax             | false                  |
      | reduction_currency        | usd                    |
      | discount_application_type | order_without_shipping |

  Scenario: I edit cart rule by adding percentage discount action.
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping                          | true                   |
      | reduction_percentage                   | 85.5                   |
      | discount_application_type              | order_without_shipping |
      | reduction_apply_to_discounted_products | true                   |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping                          | true                   |
      | reduction_percentage                   | 85.5                   |
      | discount_application_type              | order_without_shipping |
      | reduction_apply_to_discounted_products | true                   |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping                          | false            |
      | reduction_percentage                   | 10               |
      | discount_application_type              | cheapest_product |
      | reduction_apply_to_discounted_products | false            |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping                          | false            |
      | reduction_percentage                   | 10               |
      | discount_application_type              | cheapest_product |
      | reduction_apply_to_discounted_products | false            |
