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

  Scenario: I edit cart rule
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
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | usd                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
    And cart rule "cart_rule_1" should have the following properties:
      | name[en-US]       | cart rule 1         |
      | description       |                     |
      | highlight         | true                |
      | enabled           | true                |
      | allow partial use | true                |
      | priority          | 1                   |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | total quantity    | 11                  |
      | quantity per user | 3                   |
      | free shipping     | true                |
      | minimum amount    | 10                  |
      | currency          | usd                 |
      | tax included      | true                |
      | shipping included | true                |
