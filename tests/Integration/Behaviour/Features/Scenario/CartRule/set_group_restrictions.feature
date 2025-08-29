# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags set-group-restrictions
@restore-all-tables-before-feature
@set-group-restrictions
Feature: Set cart rule customer group restrictions in BO
  PrestaShop allows BO users to add and remove group restrictions of cart rule,
  which defines whether or not the cart rule should be applicable to a specific customer group

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    And currency "usd" is the default one
    And language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And I create a Customer Group "group1" with the following details:
      | name[en-US]             | Name EN |
      | reduction               | 1.23    |
      | displayPriceTaxExcluded | true    |
      | showPrice               | true    |
      | shopIds                 | shop1   |
    And I create a Customer Group "group2" with the following details:
      | name[en-US]             | Name EN |
      | reduction               | 1.25    |
      | displayPriceTaxExcluded | true    |
      | showPrice               | true    |
      | shopIds                 | shop1   |
    And I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | virtual        |
    And I add product "product2" with following information:
      | name[en-US] | T-shirt nr1 |
      | type        | standard    |
    And I add product "product3" with following information:
      | name[en-US] | Shirt - Dom & Jquery |
      | type        | standard             |
    And there is a cart rule "rule_free_shipping_1" with following properties:
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
      | restricted groups |                      |
    And there is a cart rule "rule_50_percent" with following properties:
      | name[en-US]                  | Half the price         |
      | is_active                    | true                   |
      | allow_partial_use            | true                   |
      | priority                     | 2                      |
      | valid_from                   | 2022-01-01 11:00:00    |
      | valid_to                     | 3001-01-01 12:00:00    |
      | total_quantity               | 10                     |
      | quantity_per_user            | 12                     |
      | free_shipping                | false                  |
      | code                         | rule_50_percent        |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | false                  |
      | discount_application_type    | order_without_shipping |
      | restricted groups            |                        |
    And I clear all group restrictions for cart rule rule_free_shipping_1
    And I clear all group restrictions for cart rule rule_50_percent
    And cart rule "rule_free_shipping_1" should have the following properties:
      | restricted groups |  |
    And cart rule "rule_50_percent" should have the following properties:
      | restricted groups |  |

  Scenario: Restrict cart rule groups and clear the restrictions
    When I restrict following groups for cart rule rule_free_shipping_1:
      | restricted groups | group1,group2 |
    And I save all the restrictions for cart rule rule_free_shipping_1
    And I restrict following groups for cart rule rule_50_percent:
      | restricted groups | group1 |
    And I save all the restrictions for cart rule rule_50_percent
    Then cart rule rule_free_shipping_1 should have the following properties:
      | restricted groups | group1,group2 |
    And cart rule rule_50_percent should have the following properties:
      | restricted groups | group1 |
    When I restrict following groups for cart rule rule_free_shipping_1:
      | restricted groups | group2 |
    And I save all the restrictions for cart rule rule_free_shipping_1
    And I clear all group restrictions for cart rule rule_50_percent
    And I save all the restrictions for cart rule rule_50_percent
    Then cart rule rule_free_shipping_1 should have the following properties:
      | restricted groups | group2 |
    Then cart rule rule_50_percent should have the following properties:
      | restricted groups |  |
    And I clear all group restrictions for cart rule rule_free_shipping_1
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule rule_50_percent should have the following properties:
      | restricted groups |  |
    Then cart rule rule_50_percent should have the following properties:
      | restricted groups |  |
