# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags set-country-restrictions
@restore-all-tables-before-feature
@set-country-restrictions
Feature: Set cart rule country restrictions in BO
  PrestaShop allows BO users to add and remove country restrictions of cart rule,
  which defines whether or not the cart rule should be applicable in a specific country

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    And currency "usd" is the default one
    And language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    When I add new country "France" with following properties:
      | name[en-US]                | France          |
      | iso_code                   | FR              |
      | call_prefix                | 123             |
      | default_currency           | 1               |
      | zone                       | 1               |
      | need_zip_code              | true            |
      | zip_code_format            | 1 NL            |
      | address_format             | not implemented |
      | is_enabled                 | true            |
      | contains_states            | false           |
      | need_identification_number | false           |
      | display_tax_label          | true            |
      | shop_association           | 1               |
    When I add new country "United_states" with following properties:
      | name[en-US]                | United States   |
      | iso_code                   | US              |
      | call_prefix                | 1234            |
      | default_currency           | 1               |
      | zone                       | 2               |
      | need_zip_code              | true            |
      | zip_code_format            | 1 NL            |
      | address_format             | not implemented |
      | is_enabled                 | true            |
      | contains_states            | false           |
      | need_identification_number | false           |
      | display_tax_label          | true            |
      | shop_association           | 1               |
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
      | name[en-US]          | free shipping 1      |
      | is_active            | true                 |
      | allow_partial_use    | false                |
      | priority             | 1                    |
      | valid_from           | 2022-01-01 11:00:00  |
      | valid_to             | 3001-01-01 12:00:00  |
      | total_quantity       | 10                   |
      | quantity_per_user    | 10                   |
      | free_shipping        | true                 |
      | code                 | rule_free_shipping_1 |
      | restricted countries |                      |
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
      | restricted countries         |                        |
    And I clear all country restrictions for cart rule rule_free_shipping_1
    And I clear all country restrictions for cart rule rule_50_percent
    And cart rule "rule_free_shipping_1" should have the following properties:
      | restricted countries |  |
    And cart rule "rule_50_percent" should have the following properties:
      | restricted countries |  |

  Scenario: Restrict cart rule countries and clear the restrictions
    When I restrict following countries for cart rule rule_free_shipping_1:
      | restricted countries | France,United_states |
    And I save all the restrictions for cart rule rule_free_shipping_1
    And I restrict following countries for cart rule rule_50_percent:
      | restricted countries | France |
    And I save all the restrictions for cart rule rule_50_percent
    Then cart rule rule_free_shipping_1 should have the following properties:
      | restricted countries | France,United_states |
    And cart rule rule_50_percent should have the following properties:
      | restricted countries | France |
    When I restrict following countries for cart rule rule_free_shipping_1:
      | restricted countries | United_states |
    And I save all the restrictions for cart rule rule_free_shipping_1
    And I clear all country restrictions for cart rule rule_50_percent
    And I save all the restrictions for cart rule rule_50_percent
    Then cart rule rule_free_shipping_1 should have the following properties:
      | restricted countries | United_states |
    Then cart rule rule_50_percent should have the following properties:
      | restricted countries |  |
    And I clear all country restrictions for cart rule rule_free_shipping_1
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule rule_50_percent should have the following properties:
      | restricted countries |  |
    Then cart rule rule_50_percent should have the following properties:
      | restricted countries |  |
