@restore-all-tables-before-feature
Feature: Cart calculation with carriers
  As a customer
  I must be able to have correct cart total when selecting carriers

  Scenario: Empty cart, carrier 1
    Given I have an empty default cart
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
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
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 5.1
    Then my cart total should be 24.912 tax included
    Then my cart total using previous calculation method should be 24.912 tax included

  Scenario: one product in cart, quantity 3, carrier 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    When I add 3 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 5.1
    Then my cart total should be 64.536 tax included
    Then my cart total using previous calculation method should be 64.536 tax included

  Scenario: 3 products in cart, several quantities, carrier 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 5.1
    Then my cart total should be 160.5 tax included
    Then my cart total using previous calculation method should be 160.5 tax included

  Scenario: Empty cart, carrier 2
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 0.0
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, carrier 2
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 7.7
    Then my cart total should be 27.512 tax included
    Then my cart total using previous calculation method should be 27.512 tax included

  Scenario: one product in cart, quantity 3, carrier 2
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    When I add 3 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 7.7
    Then my cart total should be 67.136 tax included
    Then my cart total using previous calculation method should be 67.136 tax included

  Scenario: 3 products in cart, several quantities, carrier 2
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a zone named "zone2"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    Given there is a carrier named "carrier2"
    Given carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 7.7
    Then my cart total should be 163.1 tax included
    Then my cart total using previous calculation method should be 163.1 tax included

  Scenario: free carrier in price range
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 151.0 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 150
    Given carrier "carrier1" applies shipping fees of 0.0 in zone "zone1" for price between 150 and 1000
    When I add 1 item of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 2.0
    Then my cart total should be 153.0 tax included
    Then my cart total using previous calculation method should be 153.0 tax included
