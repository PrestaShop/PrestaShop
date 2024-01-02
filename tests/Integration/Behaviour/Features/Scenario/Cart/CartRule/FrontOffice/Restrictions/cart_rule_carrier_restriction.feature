# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-carrier-restriction
@restore-all-tables-before-feature
@fo-cart-rule-carrier-restriction
@clear-cache-before-feature
Feature: Cart calculation with carrier specific cart rules
  As a customer
  I must be able to have correct cart total when selecting carriers

  Background:
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And I have an empty default cart
    And shipping handling fees are set to 2.0
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a zone named "zone1"
    And there is a zone named "zone2"
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a country named "country2" and iso code "US" in zone "zone2"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    And there is an address named "address1" with postcode "1" in state "state1"
    And there is an address named "address2" with postcode "1" in state "state2"
    And there is a carrier named "carrier1"
    And carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    And carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    And there is a carrier named "carrier2"
    And carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    And carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    And there is a carrier named "carrier3"
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a cart rule "cartrule1" with following properties:
      | name[en-US]         | cartrule1 |
      | total_quantity      | 1000      |
      | quantity_per_user   | 1000      |
      | priority            | 1         |
      | free_shipping       | false     |
      | code                | foo       |
      | discount_percentage | 50        |
    And I restrict following carriers for cart rule cartrule1:
      | restricted carriers | carrier2 |
    And I save all the restrictions for cart rule cartrule1
    And cart rule cartrule1 should have the following properties:
      | restricted carriers | carrier2 |
    And there is a cart rule "cartrule2" with following properties:
      | name[en-US]         | cartrule2 |
      | total_quantity      | 1000      |
      | quantity_per_user   | 1000      |
      | priority            | 2         |
      | free_shipping       | false     |
      | code                | bar       |
      | discount_percentage | 50        |
    And I restrict following carriers for cart rule cartrule2:
      | restricted carriers | carrier1 |
    And I save all the restrictions for cart rule cartrule2
    And cart rule cartrule2 should have the following properties:
      | restricted carriers | carrier1 |

  Scenario: I cannot use voucher when it is restricted to specific carrier and that carrier is not selected
    When I add 1 items of product "product1" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    And cart shipping fees should be 5.1
    And my cart total shipping fees should be 5.1 tax included
    And my cart total should be 24.912 tax included
    When I apply the voucher code "foo"
    Then I should get cart rule validation error saying "You cannot use this voucher with this carrier"
    And my cart total should be 24.912 tax included
    And cart shipping fees should be 5.1

  @restore-cart-rules-after-scenario
  Scenario: Free shipping cart rule without code is applied to cart automatically when restricted carrier is selected
    Given there is a cart rule "cartrule10" with following properties:
      | name[en-US]       | cartrule10 |
      | total_quantity    | 1000       |
      | quantity_per_user | 1000       |
      | priority          | 1          |
      | free_shipping     | true       |
    And I restrict following carriers for cart rule cartrule10:
      | restricted carriers | carrier2 |
    And I save all the restrictions for cart rule cartrule10
    And cart rule cartrule10 should have the following properties:
      | restricted carriers | carrier2 |
    When I add 1 items of product "product1" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    Then cart shipping fees should be 5.1
    And my cart total should be 24.912 tax included
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 7.7
    And my cart total should be 19.812 tax included

  @restore-cart-rules-after-scenario
  Scenario: Percentage cart rule without code is applied to cart automatically when restricted carrier is selected
    Given there is a cart rule "cartrule11" with following properties:
      | name[en-US]         | cartrule11 |
      | total_quantity      | 1000       |
      | quantity_per_user   | 1000       |
      | priority            | 1          |
      | free_shipping       | false      |
      | discount_percentage | 55         |
    And I restrict following carriers for cart rule cartrule11:
      | restricted carriers | carrier2 |
    And I save all the restrictions for cart rule cartrule11
    And cart rule cartrule11 should have the following properties:
      | restricted carriers | carrier2 |
    When I add 1 items of product "product1" in my cart
    And I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 5.1
    And my cart total should be 24.912 tax included
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 7.7
    Then my cart total should be 16.6154 tax included

  @restore-cart-rules-after-scenario
  Scenario: Amount cart rule without code is applied to cart automatically when restricted carrier is selected
    Given there is a cart rule "cartrule12" with following properties:
      | name[en-US]           | cartrule12 |
      | total_quantity        | 1000       |
      | quantity_per_user     | 1000       |
      | priority              | 1          |
      | free_shipping         | false      |
      | discount_amount       | 10         |
      | discount_currency     | usd        |
      | discount_includes_tax | false      |
    And I restrict following carriers for cart rule cartrule12:
      | restricted carriers | carrier2 |
    And I save all the restrictions for cart rule cartrule12
    And cart rule cartrule12 should have the following properties:
      | restricted carriers | carrier2 |
    When I add 1 items of product "product1" in my cart
    And I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 5.1
    And my cart total should be 24.912 tax included
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 7.7
    And my cart total should be 17.5 tax included

  Scenario: one product in cart, quantity 1, can apply corresponding cart rule
    Given carrier "carrier3" applies shipping fees of 6.7 in zone "zone1" for price between 0 and 10000
    And carrier "carrier3" applies shipping fees of 7.2 in zone "zone2" for price between 0 and 10000
    And there is a cart rule "cartrule3" with following properties:
      | name[en-US]                  | cartrule3              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 3                      |
      | free_shipping                | false                  |
      | discount_percentage          | 55                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And I restrict following carriers for cart rule cartrule3:
      | restricted carriers | carrier3 |
    And I save all the restrictions for cart rule cartrule3
    And cart rule cartrule3 should have the following properties:
      | restricted carriers | carrier3 |
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    When I apply the voucher code "foo"
    Then cart shipping fees should be 7.7
    And my cart total should be 17.6 tax included
    When I select carrier "carrier1" in my cart
    Then cart rule count in my cart should be 0
    And my cart total should be 24.912 tax included
    When I select carrier "carrier3" in my cart
    Then cart shipping fees should be 8.7
    And cart rule count in my cart should be 1
    And my cart total should be 17.6 tax included
    When I select carrier "carrier2" in my cart
    Then cart rule count in my cart should be 0
    And my cart total should be 27.5 tax included
