# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-country-restriction
@restore-all-tables-before-feature
@fo-cart-rule-country-restriction
Feature: Cart calculation with country specific cart rules
  As a customer
  I must be able to have correct cart total when selecting country

  Background:
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And I have an empty default cart
    And shipping handling fees are set to 2.0
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And I add new zone "zone1" with following properties:
      | name    | Europe |
      | enabled | true   |
    And I add new zone "zone2" with following properties:
      | name    | North America |
      | enabled | true          |
    And there is a country named "France" and iso code "FR" in zone "zone1"
    And there is a country named "United States of America" and iso code "US" in zone "zone2"
    And there is a state named "state-fr" with iso code "TEST-FR" in country "France" and zone "zone1"
    And there is a state named "state-us" with iso code "TEST-US" in country "United States of America" and zone "zone2"
    And there is an address named "address-fr" with postcode "1" in state "state-fr"
    And there is an address named "address-us" with postcode "1" in state "state-us"
    And I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | zones            | zone1, zone2                           |
    Then I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 10000    | 12.3        |
      | zone2   | 0          | 10000    | 45.6        |
    And there is a cart rule "cartrule1" with following properties:
      | name[en-US]           | cartrule1 |
      | priority              | 1         |
      | free_shipping         | false     |
      | discount_amount       | 7.8       |
      | discount_currency     | usd       |
      | discount_includes_tax | false     |
    And I restrict following countries for cart rule cartrule1:
      | restricted countries | France |
    And I save all the restrictions for cart rule cartrule1
    And cart rule cartrule1 should have the following properties:
      | restricted countries | France |
    And there is a product in the catalog named "Product1" with a price of 90.12 and 100 items in stock

  Scenario: Cart with a Product And address restricted by cart rule
    When I select address "address-fr" in my cart
    And I select carrier "carrier1" in my cart
    And I add 1 items of product "Product1" in my cart
    Then cart rule count in my cart should be 1
    And the current cart should have the following contextual reductions:
      | reference | reduction |
      | cartrule1 | 7.8       |
    And cart shipping fees should be 14.3 tax excluded
    And cart shipping fees should be 14.3 tax included
    And my cart total should be 96.6 tax included

  @restore-cart-rules-before-scenario
  Scenario: Cart with a Product And adress not restricted by cart rule
    When I select address "address-us" in my cart
    And I select carrier "carrier1" in my cart
    And I add 1 items of product "Product1" in my cart
    Then cart rule count in my cart should be 0
    And cart shipping fees should be 47.6 tax included
    And my cart total should be 137.7 tax included
