# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags add-cart-rule
@restore-all-tables-before-feature
@add-cart-rule
Feature: Add cart rule
  PrestaShop allows BO users to create cart rules
  As a BO user
  I must be able to create cart rules

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And language with iso code "en" is the default one

  Scenario: Create a cart rule with amount discount
    When I create cart rule "cart_rule_1" with following properties:
      | name[en-US]                      | Promotion              |
      | description                      | Promotion for holidays |
      | highlight                        | false                  |
      | is_active                        | true                   |
      | allow_partial_use                | false                  |
      | priority                         | 2                      |
      | valid_from                       | 2019-01-01 11:05:00    |
      | valid_to                         | 2019-12-01 00:00:00    |
      | total_quantity                   | 10                     |
      | quantity_per_user                | 1                      |
      | free_shipping                    | true                   |
      | minimum_amount                   | 10                     |
      | minimum_amount_currency          | chf                    |
      | minimum_amount_tax_included      | false                  |
      | minimum_amount_shipping_included | true                   |
      | code                             | PROMO_2019             |
      | discount_amount                  | 15                     |
      | discount_currency                | usd                    |
      | discount_includes_tax            | true                   |
      | discount_application_type        | order_without_shipping |
    And cart rule "cart_rule_1" should have the following properties:
      | name[en-US]                      | Promotion              |
      | description                      | Promotion for holidays |
      | highlight                        | false                  |
      | is_active                        | true                   |
      | allow_partial_use                | false                  |
      | priority                         | 2                      |
      | valid_from                       | 2019-01-01 11:05:00    |
      | valid_to                         | 2019-12-01 00:00:00    |
      | total_quantity                   | 10                     |
      | quantity_per_user                | 1                      |
      | free_shipping                    | true                   |
      | minimum_amount                   | 10                     |
      | minimum_amount_currency          | chf                    |
      | minimum_amount_tax_included      | false                  |
      | minimum_amount_shipping_included | true                   |
      | code                             | PROMO_2019             |
      | discount_amount                  | 15                     |
      | discount_currency                | usd                    |
      | discount_includes_tax            | true                   |
      | discount_application_type        | order_without_shipping |

  Scenario: Create a cart rule with percentage discount
    When I create cart rule "cart_rule_2" with following properties:
      | name[en-US]                      | 50% off promo                           |
      | description                      | Discount for whole catalog for one hour |
      | highlight                        | true                                    |
      | is_active                        | false                                   |
      | allow_partial_use                | true                                    |
      | priority                         | 1                                       |
      | valid_from                       | 2019-01-01 11:00:00                     |
      | valid_to                         | 2019-01-01 12:00:00                     |
      | total_quantity                   | 10                                      |
      | quantity_per_user                | 12                                      |
      | free_shipping                    | true                                    |
      | minimum_amount                   | 99.99                                   |
      | minimum_amount_currency          | usd                                     |
      | minimum_amount_tax_included      | true                                    |
      | minimum_amount_shipping_included | false                                   |
      | code                             | HAPPY_HOUR                              |
      | discount_percentage              | 50                                      |
      | apply_to_discounted_products     | false                                   |
      | discount_application_type        | cheapest_product                        |
    And cart rule "cart_rule_2" should have the following properties:
      | name[en-US]                      | 50% off promo                           |
      | description                      | Discount for whole catalog for one hour |
      | highlight                        | true                                    |
      | is_active                        | false                                   |
      | allow_partial_use                | true                                    |
      | priority                         | 1                                       |
      | valid_from                       | 2019-01-01 11:00:00                     |
      | valid_to                         | 2019-01-01 12:00:00                     |
      | total_quantity                   | 10                                      |
      | quantity_per_user                | 12                                      |
      | free_shipping                    | true                                    |
      | minimum_amount                   | 99.99                                   |
      | minimum_amount_currency          | usd                                     |
      | minimum_amount_tax_included      | true                                    |
      | minimum_amount_shipping_included | false                                   |
      | code                             | HAPPY_HOUR                              |
      | discount_percentage              | 50                                      |
      | apply_to_discounted_products     | false                                   |
      | discount_application_type        | cheapest_product                        |

  Scenario: Create and enable cart rule
    When I create cart rule "cart_rule_3" with following properties:
      | name[en-US]                      | Cart Rule 3         |
      | highlight                        | true                |
      | active                           | false               |
      | allow_partial_use                | true                |
      | priority                         | 1                   |
      | is_active                        | false               |
      | valid_from                       | 2019-01-01 11:05:00 |
      | valid_to                         | 2019-12-01 00:00:00 |
      | total_quantity                   | 10                  |
      | quantity_per_user                | 2                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | usd                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
    When I enable cart rule with reference "cart_rule_3"
    Then cart rule with reference "cart_rule_3" is enabled

  Scenario: I should not be able to create cart rule with already existing code
    Given I create cart rule "cart_rule_4" with following properties:
      | name[en-US]       | Cart Rule 4         |
      | highlight         | true                |
      | active            | true                |
      | allow_partial_use | true                |
      | priority          | 1                   |
      | is_active         | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | total_quantity    | 10                  |
      | quantity_per_user | 2                   |
      | free_shipping     | true                |
      | code              | testcode1           |
    And cart rule cart_rule_4 should have the following properties:
      | is_active | testcode1 |
    When I create cart rule "cart_rule_5" with following properties:
      | name[en-US]       | Cart Rule 5         |
      | highlight         | true                |
      | active            | true                |
      | allow_partial_use | true                |
      | priority          | 1                   |
      | is_active         | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | total_quantity    | 10                  |
      | quantity_per_user | 2                   |
      | free_shipping     | true                |
      | code              | testcode1           |
    Then I should get cart rule error about "non unique cart rule code"
