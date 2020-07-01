# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule
@reset-database-before-feature
Feature: Add cart rule
  PrestaShop allows BO users to create cart rules
  As a BO user
  I must be able to create cart rules

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given currency "currency1" is the default one

  #  @todo If you need to create cart rule, instead use
  #  "I create cart rule "cart_rule_1" with the following properties" from Delete cart rule scenario.
  Scenario: Create a cart rule with amount discount
    When I want to create a new cart rule
    And I specify its name in default language as "Promotion"
    And I specify its "description" as "Promotion for holidays"
    And I specify that its active from "2019-01-01 11:05:00"
    And I specify that its active until "2019-12-01 00:00:00"
    And I specify that its "quantity" is "10"
    And I specify that its "quantity per user" is "1"
    And I specify that its "priority" is "2"
    And I specify that partial use is disabled for it
    And I specify its status as enabled
    And I specify that it should not be highlighted in cart
    And I specify its "code" as "PROMO_2019"
    And its minimum purchase amount in currency "CHF" is "10"
    And its minimum purchase amount is tax excluded
    And its minimum purchase amount is shipping included
    And it gives free shipping
    And it gives a reduction amount of "15" in currency "USD" which is tax included and applies to order without shipping
    When I save it
    Then its name in default language should be "Promotion"
    And its "description" should be "Promotion for holidays"
    And it should be active from "2019-01-01 11:05:00"
    And it should be active until "2019-12-01 00:00:00"
    And its "quantity" should be "10"
    And its "quantity per user" should be "1"
    And its "priority" should be "2"
    And its "partial use" should be "disabled"
    And its "status" should be "enabled"
    And it should not be highlighted in cart
    And its "code" should be "PROMO_2019"
    And it should have minimum purchase amount of "10" in currency "CHF"
    And its minimum purchase amount should be tax excluded
    And its minimum purchase amount should be shipping included
    And it should give free shipping
    And it should give a reduction of "15" in currency "USD" which is tax included and applies to order without shipping

  #  @todo If you need to create cart rule, instead use
  #  "I create cart rule "cart_rule_1" with the following properties" from Delete cart rule scenario.
  Scenario: Create a cart rule with percentage discount
    When I want to create a new cart rule
    And I specify its name in default language as "50% off promo"
    And I specify its "description" as "Discount for whole catalog for one hour"
    And I specify that its active from "2019-01-01 11:00:00"
    And I specify that its active until "2019-01-01 12:00:00"
    And I specify that its "quantity" is "10"
    And I specify that its "quantity per user" is "2"
    And I specify that its "priority" is "1"
    And I specify that partial use is enabled for it
    And I specify its status as disabled
    And I specify that it should be highlighted in cart
    And I specify its "code" as "HAPPY_HOUR"
    And its minimum purchase amount in currency "USD" is "99.99"
    And its minimum purchase amount is tax included
    And its minimum purchase amount is shipping excluded
    And it gives a percentage reduction of "50" which excludes discounted products and applies to cheapest product
    When I save it
    Then its name in default language should be "50% off promo"
    And its "description" should be "Discount for whole catalog for one hour"
    And it should be active from "2019-01-01 11:00:00"
    And it should be active until "2019-01-01 12:00:00"
    And its "quantity" should be "10"
    And its "quantity per user" should be "2"
    And its "priority" should be "1"
    And its "partial use" should be "enabled"
    And its "status" should be "disabled"
    And it should be highlighted in cart
    And its "code" should be "HAPPY_HOUR"
    And it should have minimum purchase amount of "99.99" in currency "USD"
    And its minimum purchase amount should be tax included
    And its minimum purchase amount should be shipping excluded
    And it should give a percentage reduction of "50" which excludes discounted products and applies to cheapest product

  Scenario: Delete cart rule
    When I create cart rule "cart_rule_1" with following properties:
      | id_cart_rule                              | 1                     |
      | name_in_default_language                  | Cart Rule 1           |
      | highlight                                 | true                  |
      | active                                    | true                  |
      | allow_partial_use                         | true                  |
      | priority                                  | 1                     |
      | is_active                                 | true                  |
      | valid_from                                | 2019-01-01 11:05:00   |
      | valid_to                                  | 2019-12-01 00:00:00   |
      | total_quantity                            | 10                    |
      | quantity_per_user                         | 2                     |
      | free_shipping                             | true                  |
      | minimum_amount                            | 10                    |
      | minimum_amount_currency                   | currency1             |
      | minimum_amount_tax_included               | true                  |
      | minimum_amount_shipping_included          | true                  |
    And I delete Cart rule with reference "cart_rule_1"
    Then Cart rule with reference "cart_rule_1" does not exist

  Scenario: Delete multiple cart rules
    When I create cart rule "cart_rule_1" with following properties:
      | id_cart_rule                              | 1                     |
      | name_in_default_language                  | Cart Rule 1           |
      | highlight                                 | true                  |
      | active                                    | true                  |
      | allow_partial_use                         | true                  |
      | priority                                  | 1                     |
      | is_active                                 | true                  |
      | valid_from                                | 2019-01-01 11:05:00   |
      | valid_to                                  | 2019-12-01 00:00:00   |
      | total_quantity                            | 10                    |
      | quantity_per_user                         | 2                     |
      | free_shipping                             | true                  |
      | minimum_amount                            | 10                    |
      | minimum_amount_currency                   | currency1             |
      | minimum_amount_tax_included               | true                  |
      | minimum_amount_shipping_included          | true                  |
    And I create cart rule "cart_rule_2" with following properties:
      | id_cart_rule                              | 2                     |
      | name_in_default_language                  | Cart Rule 2           |
      | highlight                                 | true                  |
      | active                                    | true                  |
      | allow_partial_use                         | true                  |
      | priority                                  | 1                     |
      | is_active                                 | true                  |
      | valid_from                                | 2019-01-01 11:05:00   |
      | valid_to                                  | 2019-12-01 00:00:00   |
      | total_quantity                            | 10                    |
      | quantity_per_user                         | 2                     |
      | free_shipping                             | true                  |
      | minimum_amount                            | 10                    |
      | minimum_amount_currency                   | currency1             |
      | minimum_amount_tax_included               | true                  |
      | minimum_amount_shipping_included          | true                  |
    And I bulk delete cart rules "cart_rule_1,cart_rule_2"
    Then Cart rule with reference "cart_rule_1" does not exist
    And Cart rule with reference "cart_rule_2" does not exist

  Scenario: Enable and enable cart rule
    When I create cart rule "cart_rule_1" with following properties:
      | id_cart_rule                              | 1                     |
      | name_in_default_language                  | Cart Rule 1           |
      | highlight                                 | true                  |
      | active                                    | true                  |
      | allow_partial_use                         | true                  |
      | priority                                  | 1                     |
      | is_active                                 | false                 |
      | valid_from                                | 2019-01-01 11:05:00   |
      | valid_to                                  | 2019-12-01 00:00:00   |
      | total_quantity                            | 10                    |
      | quantity_per_user                         | 2                     |
      | free_shipping                             | true                  |
      | minimum_amount                            | 10                    |
      | minimum_amount_currency                   | currency1             |
      | minimum_amount_tax_included               | true                  |
      | minimum_amount_shipping_included          | true                  |
    When I enable cart rule with reference "cart_rule_1"
    Then Cart rule with reference "cart_rule_1" is enabled

  Scenario: Disable cart rule
    When I create cart rule "cart_rule_1" with following properties:
      | id_cart_rule                              | 1                     |
      | name_in_default_language                  | Cart Rule 1           |
      | highlight                                 | true                  |
      | active                                    | true                  |
      | allow_partial_use                         | true                  |
      | priority                                  | 1                     |
      | is_active                                 | true                  |
      | valid_from                                | 2019-01-01 11:05:00   |
      | valid_to                                  | 2019-12-01 00:00:00   |
      | total_quantity                            | 10                    |
      | quantity_per_user                         | 2                     |
      | free_shipping                             | true                  |
      | minimum_amount                            | 10                    |
      | minimum_amount_currency                   | currency1             |
      | minimum_amount_tax_included               | true                  |
      | minimum_amount_shipping_included          | true                  |
    And I disable cart rule with reference "cart_rule_1"
    Then Cart rule with reference "cart_rule_1" is disabled

  Scenario: Enable multiple cart rules
    When I create cart rule "cart_rule_1" with following properties:
      | id_cart_rule                              | 1                     |
      | name_in_default_language                  | Cart Rule 1           |
      | highlight                                 | true                  |
      | active                                    | true                  |
      | allow_partial_use                         | true                  |
      | priority                                  | 1                     |
      | is_active                                 | true                  |
      | valid_from                                | 2019-01-01 11:05:00   |
      | valid_to                                  | 2019-12-01 00:00:00   |
      | total_quantity                            | 10                    |
      | quantity_per_user                         | 2                     |
      | free_shipping                             | true                  |
      | minimum_amount                            | 10                    |
      | minimum_amount_currency                   | currency1                   |
      | minimum_amount_tax_included               | true                  |
      | minimum_amount_shipping_included          | true                  |
    And I create cart rule "cart_rule_2" with following properties:
      | id_cart_rule                              | 2                     |
      | name_in_default_language                  | Cart Rule 2           |
      | highlight                                 | true                  |
      | active                                    | true                  |
      | allow_partial_use                         | true                  |
      | priority                                  | 1                     |
      | is_active                                 | true                  |
      | valid_from                                | 2019-01-01 11:05:00   |
      | valid_to                                  | 2019-12-01 00:00:00   |
      | total_quantity                            | 10                    |
      | quantity_per_user                         | 2                     |
      | free_shipping                             | true                  |
      | minimum_amount                            | 10                    |
      | minimum_amount_currency                   | currency1             |
      | minimum_amount_tax_included               | true                  |
      | minimum_amount_shipping_included          | true                  |
    And I bulk disable cart rules "cart_rule_1,cart_rule_2"
    Then Cart rule with reference "cart_rule_1" is disabled
    And Cart rule with reference "cart_rule_2" is disabled

  Scenario: Disable multiple cart rules
    When I create cart rule "cart_rule_1" with following properties:
      | id_cart_rule                              | 1                     |
      | name_in_default_language                  | Cart Rule 1           |
      | highlight                                 | true                  |
      | active                                    | true                  |
      | allow_partial_use                         | true                  |
      | priority                                  | 1                     |
      | is_active                                 | false                 |
      | valid_from                                | 2019-01-01 11:05:00   |
      | valid_to                                  | 2019-12-01 00:00:00   |
      | total_quantity                            | 10                    |
      | quantity_per_user                         | 2                     |
      | free_shipping                             | true                  |
      | minimum_amount                            | 10                    |
      | minimum_amount_currency                   | currency1             |
      | minimum_amount_tax_included               | true                  |
      | minimum_amount_shipping_included          | true                  |
    And I create cart rule "cart_rule_2" with following properties:
      | id_cart_rule                              | 2                     |
      | name_in_default_language                  | Cart Rule 2           |
      | highlight                                 | true                  |
      | active                                    | false                 |
      | allow_partial_use                         | true                  |
      | priority                                  | 1                     |
      | is_active                                 | true                  |
      | valid_from                                | 2019-01-01 11:05:00   |
      | valid_to                                  | 2019-12-01 00:00:00   |
      | total_quantity                            | 10                    |
      | quantity_per_user                         | 2                     |
      | free_shipping                             | true                  |
      | minimum_amount                            | 10                    |
      | minimum_amount_currency                   | currency1             |
      | minimum_amount_tax_included               | true                  |
      | minimum_amount_shipping_included          | true                  |
    When I bulk enable cart rules "cart_rule_1,cart_rule_2"
    Then Cart rule with reference "cart_rule_1" is enabled
    And Cart rule with reference "cart_rule_2" is enabled
