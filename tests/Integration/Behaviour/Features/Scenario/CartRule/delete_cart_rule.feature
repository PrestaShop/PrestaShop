# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags delete-cart-rule
@restore-all-tables-before-feature
@delete-cart-rule
Feature: Add cart rule
  PrestaShop allows BO users to delete cart rules
  As a BO user
  I must be able to delte cart rules

  Background:
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And language with iso code "en" is the default one

  Scenario: Delete cart rule
    When I create cart rule "cart_rule_1" with following properties:
      | name[en-US]                      | Cart Rule 1         |
      | highlight                        | true                |
      | active                           | true                |
      | allow_partial_use                | true                |
      | priority                         | 1                   |
      | is_active                        | true                |
      | valid_from                       | 2019-01-01 11:05:00 |
      | valid_to                         | 2019-12-01 00:00:00 |
      | total_quantity                   | 10                  |
      | quantity_per_user                | 2                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | usd                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
    And I delete Cart rule with reference "cart_rule_1"
    Then Cart rule with reference "cart_rule_1" does not exist

  Scenario: Delete multiple cart rules
    When I create cart rule "cart_rule_2" with following properties:
      | name[en-US]                      | Cart Rule 2         |
      | highlight                        | true                |
      | active                           | true                |
      | allow_partial_use                | true                |
      | priority                         | 1                   |
      | is_active                        | true                |
      | valid_from                       | 2019-01-01 11:05:00 |
      | valid_to                         | 2019-12-01 00:00:00 |
      | total_quantity                   | 10                  |
      | quantity_per_user                | 2                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | usd                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
    And I create cart rule "cart_rule_3" with following properties:
      | name[en-US]                      | Cart Rule 3         |
      | highlight                        | true                |
      | active                           | true                |
      | allow_partial_use                | true                |
      | priority                         | 1                   |
      | is_active                        | true                |
      | valid_from                       | 2019-01-01 11:05:00 |
      | valid_to                         | 2019-12-01 00:00:00 |
      | total_quantity                   | 10                  |
      | quantity_per_user                | 2                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | usd                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
    And I bulk delete cart rules "cart_rule_2,cart_rule_3"
    Then Cart rule with reference "cart_rule_2" does not exist
    And Cart rule with reference "cart_rule_3" does not exist
