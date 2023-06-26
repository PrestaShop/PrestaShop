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
    And there is a zone named "Europe"
    And there is a zone named "North America"
    And there is a country named "France" and iso code "FR" in zone "Europe"
    And there is a country named "United States of America" and iso code "US" in zone "North America"
    And there is a state named "state-fr" with iso code "TEST-FR" in country "France" and zone "Europe"
    And there is a state named "state-us" with iso code "TEST-US" in country "United States of America" and zone "North America"
    And there is an address named "address-fr" with postcode "1" in state "state-fr"
    And there is an address named "address-us" with postcode "1" in state "state-us"
    And there is a carrier named "carrier1"
    And carrier "carrier1" applies shipping fees of 12.3 in zone "Europe" for price between 0 and 10000
    And carrier "carrier1" applies shipping fees of 45.6 in zone "North America" for price between 0 and 10000
    And there is a cart rule "cartrule1" with following properties:
      | name[en-US]                  | cartrule1              |
      | priority                     | 1                      |
      | free_shipping                | false                  |
      | discount_amount              | 7.8                    |
      | discount_currency            | usd                    |
      | discount_includes_tax        | false                  |
    And cart rule "cartrule1" is restricted to country "FR"
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
