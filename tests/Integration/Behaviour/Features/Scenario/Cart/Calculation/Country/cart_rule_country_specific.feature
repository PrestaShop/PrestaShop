# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags calculation-country-cart-rule-specific

@reset-database-before-feature
@calculation-country-cart-rule-specific
Feature: Cart calculation with country specific cart rules
  As a customer
  I must be able to have correct cart total when selecting country

  Scenario: Cart with a Product And address restricted by cart rule
    Given I have an empty default cart
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
      And there is a cart rule named "cartrule1" that applies an amount discount of 7.8 with priority 1, quantity of 1000 and quantity per user 1000
      And cart rule "cartrule1" is restricted to country "FR"
      And there is a product in the catalog named "Product1" with a price of 90.12 and 100 items in stock
    When I select address "address-fr" in my cart
      And I select carrier "carrier1" in my cart
      And I add 1 items of product "Product1" in my cart
    Then cart rule count in my cart should be 1
      And the current cart should have the following contextual reductions:
        | cartrule1        | 7.8 |
      And cart shipping fees should be 14.3 tax excluded
      And cart shipping fees should be 14.3 tax included
      And my cart total should be 96.6 tax included

  Scenario: Cart with a Product And adress not restricted by cart rule
    Given I have an empty default cart
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
      And there is a cart rule named "cartrule1" that applies an amount discount of 7.8 with priority 1, quantity of 1000 and quantity per user 1000
      And cart rule "cartrule1" is restricted to country "FR"
      And there is a product in the catalog named "Product1" with a price of 90.12 and 100 items in stock
    When I select address "address-us" in my cart
      And I select carrier "carrier1" in my cart
      And I add 1 items of product "Product1" in my cart
    Then cart rule count in my cart should be 0
      And cart shipping fees should be 47.6 tax included
      And my cart total should be 137.7 tax included
