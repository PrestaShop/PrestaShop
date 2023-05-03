# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags cart-rule-restriction
@restore-all-tables-before-feature
@restore-cart-rules-before-scenario
@cart-rule-restriction
Feature: Cart rule combinations restrictions are applied correctly in cart
  PrestaShop allows BO users to restrict usages of certain cart rule combinations
  As a BO user
  I must be able to restrict cart rules to avoid applying them together in cart

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    And currency "usd" is the default one
    And language with iso code "en" is the default one
    And there is a zone named "zone1"
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is an address named "address1" with postcode "1" in state "state1"
    And there is a carrier named "carrier1"
    And carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 150
    And I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And I update product "product1" with following values:
      | price | 100 |
    And I update product "product1" stock with following information:
      | delta_quantity | 100 |
    And product product1 should have following prices information:
      | price              | 100 |
    And I create cart rule "rule_free_shipping_1" with following properties:
      | name[en-US]       | free shipping 1     |
      | is_active         | true                |
      | allow_partial_use | false               |
      | priority          | 1                   |
      | valid_from        | 2022-01-01 11:00:00 |
      | valid_to          | 3001-01-01 12:00:00 |
      | total_quantity    | 10                  |
      | quantity_per_user | 10                  |
      | free_shipping     | true                |
      | code              | TEST_RESTRICTION_1  |
    And I create cart rule "rule_50_percent" with following properties:
      | name[en-US]                            | Half the price      |
      | is_active                              | true                |
      | allow_partial_use                      | true                |
      | priority                               | 2                   |
      | valid_from                             | 2022-01-01 11:00:00 |
      | valid_to                               | 3001-01-01 12:00:00 |
      | total_quantity                         | 10                  |
      | quantity_per_user                      | 12                  |
      | free_shipping                          | false               |
      | code                                   | TEST_RESTRICTION_2  |
      | reduction_percentage                   | 50                  |
      | reduction_apply_to_discounted_products | false               |
    And cart rule "rule_free_shipping_1" should have the following properties:
      | is_active             | true                |
      | free_shipping         | true                |
      | priority              | 1                   |
      | valid_from            | 2022-01-01 11:00:00 |
      | valid_to              | 3001-01-01 12:00:00 |
      | code                  | TEST_RESTRICTION_1  |
      | restricted cart rules |                     |
    And cart rule "rule_50_percent" should have the following properties:
      | is_active                              | true                   |
      | priority                               | 2                      |
      | valid_from                             | 2022-01-01 11:00:00    |
      | valid_to                               | 3001-01-01 12:00:00    |
      | free_shipping                          | false                  |
      | code                                   | TEST_RESTRICTION_2     |
      | reduction_percentage                   | 50                     |
      | reduction_apply_to_discounted_products | false                  |
      | discount_application_type              | order_without_shipping |
      | restricted cart rules                  |                        |

  Scenario: 1 product in cart, cart rules are inserted correctly
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And I select address "address1" in my cart
    And I add the following products to my cart:
      | id reference | quantity |
      | product1     | 1        |
    And I should have 1 different products in my cart
    #@todo: need to assert taxes
    And my cart total should be 100.0 tax excluded
    And cart shipping fees should be 0.0
    And cart rule referenced as "rule_free_shipping_1" can be applied to my cart
    And cart rule referenced as "rule_50_percent" can be applied to my cart
    When I apply the discount code "TEST_RESTRICTION_1" to my cart
    Then my cart total should be 100.0 tax excluded
    And cart rule referenced as "rule_50_percent" can be applied to my cart
#    When I edit cart rule rule_free_shipping_1 with following properties:

#    When I use the discount "cartrule1"
#    Then cart rule "cartrule2" can be applied to my cart
#    When I use the discount "cartrule2"
#    When at least one cart rule applies today for customer with id 0
