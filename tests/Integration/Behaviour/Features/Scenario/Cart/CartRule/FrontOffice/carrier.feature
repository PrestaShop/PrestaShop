# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-carrier
@restore-all-tables-before-feature
@fo-cart-rule-carrier
Feature: Cart calculation with cart rules and different carriers
  As a customer
  I must see correct cart total price with different carriers when applying cart rules

  # Issue #9540 part one fixed by #12965
  Scenario: free carrier in price range, voucher in percent set the price bellow range
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 151.0 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 150
    Given carrier "carrier1" applies shipping fees of 0.0 in zone "zone1" for price between 150 and 1000
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    When I add 1 item of product "product1" in my cart
    When I use the discount "cartrule2"
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 7.0
    Then my cart total should be 82.5 tax included
    Then my cart total using previous calculation method should be 82.5 tax included

  Scenario: free carrier in price range, voucher in amount set the price bellow range
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 151.0 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 150
    Given carrier "carrier1" applies shipping fees of 0.0 in zone "zone1" for price between 150 and 1000
    Given there is a cart rule named "cartrule2" that applies an amount discount of 2.0 with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    When I add 1 item of product "product1" in my cart
    When I use the discount "cartrule2"
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 7.0
    Then my cart total should be 156.0 tax included
    Then my cart total using previous calculation method should be 156.0 tax included

  # Issue #12976 part two
  Scenario: carrier fees not free, voucher without code set shipping fees free above amount, cart total is below
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 149.0 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 10000
    Given there is a cart rule named "cartrule2" that applies an amount discount of 0.0 with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" offers free shipping
    Given cart rule "cartrule2" applies discount only when cart total is above 150.0
    When I add 1 item of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 7.0
    Then my cart total should be 156.0 tax included
    Then my cart total using previous calculation method should be 156.0 tax included

  Scenario: carrier fees not free, voucher with code set shipping fees free above amount, cart total is below
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 149.0 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 150
    Given carrier "carrier1" applies shipping fees of 0.0 in zone "zone1" for price between 150 and 1000
    Given there is a cart rule named "cartrule2" that applies an amount discount of 0.0 with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given cart rule "cartrule2" offers free shipping
    Given cart rule "cartrule2" applies discount only when cart total is above 150.0
    When I add 1 item of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart rule "cartrule2" cannot be applied to my cart
    Then cart shipping fees should be 7.0
    Then my cart total should be 156.0 tax included
    Then my cart total using previous calculation method should be 156.0 tax included

  Scenario: carrier fees not free, voucher without code set shipping fees free above amount, cart total is below
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 149.0 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 10000
    Given there is a cart rule named "cartrule2" that applies an amount discount of 0.0 with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" offers free shipping
    Given cart rule "cartrule2" applies discount only when cart total is above 150.0
    When I add 1 item of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 7.0
    Then my cart total should be 156.0 tax included
    Then my cart total using previous calculation method should be 156.0 tax included

  Scenario: carrier fees not free, voucher with code set shipping fees free above amount, cart total is above
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 151.0 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 150
    Given carrier "carrier1" applies shipping fees of 0.0 in zone "zone1" for price between 150 and 1000
    Given there is a cart rule named "cartrule2" that applies an amount discount of 0.0 with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given cart rule "cartrule2" offers free shipping
    Given cart rule "cartrule2" applies discount only when cart total is above 150.0
    When I add 1 item of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart rule "cartrule2" can be applied to my cart
    When I use the discount "cartrule2"
    Then cart shipping fees should be 2.0
    Then my cart total should be 151.0 tax included
    Then my cart total using previous calculation method should be 151.0 tax included

  Scenario: carrier fees not free, voucher without code set shipping fees free above amount, cart total is above
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 151.0 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 150
    Given carrier "carrier1" applies shipping fees of 0.0 in zone "zone1" for price between 150 and 1000
    Given there is a cart rule named "cartrule2" that applies an amount discount of 0.0 with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given cart rule "cartrule2" offers free shipping
    Given cart rule "cartrule2" applies discount only when cart total is above 150.0
    When I add 1 item of product "product1" in my cart
    When I use the discount "cartrule2"
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 2.0
    Then my cart total should be 151.0 tax included
    Then my cart total using previous calculation method should be 151.0 tax included

  Scenario: one product in cart, quantity 1, cant apply not corresponding cart rule
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
    Given there is a carrier named "carrier3"
    Given carrier "carrier3" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier3" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo"
    Given cart rule "cartrule1" is restricted to carrier "carrier2"
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "bar"
    Given cart rule "cartrule2" is restricted to carrier "carrier1"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 55.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" is restricted to carrier "carrier3"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart rule "cartrule2" cannot be applied to my cart

  Scenario: one product in cart, quantity 1, can apply corresponding cart rule
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
    Given there is a carrier named "carrier3"
    Given carrier "carrier3" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier3" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo"
    Given cart rule "cartrule1" is restricted to carrier "carrier2"
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "bar"
    Given cart rule "cartrule2" is restricted to carrier "carrier1"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 55.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" is restricted to carrier "carrier3"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart rule "cartrule1" can be applied to my cart
    When I use the discount "cartrule1"
    Then cart rule count in my cart should be 1
    Then cart rule "cartrule1" cannot be applied to my cart
    When I use the discount "cartrule1"
    Then cart rule count in my cart should be 1

  Scenario: one product in cart, quantity 1, can apply corresponding cart rule
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
    Given there is a carrier named "carrier3"
    Given carrier "carrier3" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier3" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo"
    Given cart rule "cartrule1" is restricted to carrier "carrier2"
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "bar"
    Given cart rule "cartrule2" is restricted to carrier "carrier1"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 55.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" is restricted to carrier "carrier3"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart rule "cartrule1" can be applied to my cart
    When I use the discount "cartrule1"
    Then cart rule count in my cart should be 1
    Then cart rule "cartrule1" cannot be applied to my cart
    When I use the discount "cartrule1"
    When I select carrier "carrier1" in my cart
    Then cart rule count in my cart should be 0

  Scenario: one product in cart, quantity 1, cart rule without code correctly (un)applied on corresponding carrier
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
    Given there is a carrier named "carrier3"
    Given carrier "carrier3" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    Given carrier "carrier3" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo"
    Given cart rule "cartrule1" is restricted to carrier "carrier2"
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "bar"
    Given cart rule "cartrule2" is restricted to carrier "carrier1"
    Given there is a cart rule named "cartrule3" that applies a percent discount of 55.0% with priority 3, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule3" is restricted to carrier "carrier3"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart rule count in my cart should be 0
    When I select carrier "carrier3" in my cart
    Then cart rule count in my cart should be 1
    When I select carrier "carrier2" in my cart
    Then cart rule count in my cart should be 0
