@reset-database-before-feature
Feature: Cart calculation with carrier specific cart rules
  As a customer
  I must be able to have correct cart total when selecting carriers

  Scenario: Empty cart, carrier 1
    Given I have an empty default cart
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country"country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for weight between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for weight between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for weight between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for weight between 0 and 10000
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" is restricted to carrier "carrier2"
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 0.0
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, carrier 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country"country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for weight between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for weight between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for weight between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for weight between 0 and 10000
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" is restricted to carrier "carrier2"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 5.1
    Then my cart total should be 24.912 tax included
    Then my cart total using previous calculation method should be 24.912 tax included

  Scenario: Empty cart, carrier 2
    Given I have an empty default cart
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country"country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for weight between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for weight between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for weight between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for weight between 0 and 10000
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" is restricted to carrier "carrier2"
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 0.0
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  # following is testing the bug http://forge.prestashop.com/browse/BOOM-3307
  Scenario: one product in cart, quantity 1, carrier 2
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country"country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for weight between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for weight between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for weight between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for weight between 0 and 10000
    Given there is a cart rule named "cartrule1" that applies a percent discount of 55.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" is restricted to carrier "carrier2"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 7.7
    Then my cart total should be 16.6154 tax included
    Then my cart total using previous calculation method should be 16.6154 tax included
