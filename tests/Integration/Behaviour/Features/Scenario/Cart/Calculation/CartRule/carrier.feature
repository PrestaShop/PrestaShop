@reset-database-before-feature
Feature: Cart calculation with cart rules giving gift
  As a customer
  I must be able to have correct cart total when adding products, and adding cart rule with gift

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
