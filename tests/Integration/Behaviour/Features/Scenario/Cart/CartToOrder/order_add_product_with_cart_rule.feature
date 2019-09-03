@reset-database-before-feature
Feature: Check cart to order data copy
  As a BO user
  I must be able to add products in existing order, calculation must be done correctly with cart rule

  Scenario: 1 product in cart, add new product from order with restricted amount discount exceeding product price
    Given I have an empty default cart
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1"in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given product "product2" belongs to tax group "taxrule1"
    Given there is a customer named "customer1" whose email is "fake@prestashop.com"
    Given address "address1" is associated to customer "customer1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" ships to all groups
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 10000
    When I am logged in as "customer1"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    When I validate my cart using payment module fake
    Given there is a cart rule named "cartrule5" that applies an amount discount of 500.0 with priority 5, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule5" is restricted to product "product2"
    When 2 items of product "product2" are added in my cart order, with prices 32.388 tax excluded and 32.388 tax included
    Then current cart order total for products should be 87.97 tax included
    Then current cart order total for products should be 84.59 tax excluded
    Then current cart order total discount should be 67.37 tax included
    Then current cart order total discount should be 64.78 tax excluded
    Then current cart order shipping fees should be 7.0 tax included
    Then current cart order shipping fees should be 7.0 tax excluded

  Scenario: 1 product in cart, add new product from order with restricted amount discount
    Given I have an empty default cart
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1"in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given product "product2" belongs to tax group "taxrule1"
    Given there is a customer named "customer1" whose email is "fake@prestashop.com"
    Given address "address1" is associated to customer "customer1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" ships to all groups
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 10000
    When I am logged in as "customer1"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    When I validate my cart using payment module fake
    Given there is a cart rule named "cartrule5" that applies an amount discount of 5.0 with priority 5, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule5" is restricted to product "product2"
    When 2 items of product "product2" are added in my cart order, with prices 32.388 tax excluded and 32.388 tax included
    Then current cart order total for products should be 87.97 tax included
    Then current cart order total for products should be 84.59 tax excluded
    Then current cart order total discount should be 5.2 tax included
    Then current cart order total discount should be 5.0 tax excluded
    Then current cart order shipping fees should be 7.0 tax included
    Then current cart order shipping fees should be 7.0 tax excluded

  Scenario: 1 product in cart, add new product from order with restricted percent discount
    Given I have an empty default cart
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1"in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given product "product2" belongs to tax group "taxrule1"
    Given there is a customer named "customer1" whose email is "fake@prestashop.com"
    Given address "address1" is associated to customer "customer1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" ships to all groups
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 10000
    When I am logged in as "customer1"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    When I validate my cart using payment module fake
    Given there is a cart rule named "cartrule5" that applies a percent discount of 15.0% with priority 5, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule5" is restricted to product "product2"
    When 2 items of product "product2" are added in my cart order, with prices 32.388 tax excluded and 32.388 tax included
    Then current cart order total for products should be 87.97 tax included
    Then current cart order total for products should be 84.59 tax excluded
    Then current cart order total discount should be 10.11 tax included
    Then current cart order total discount should be 9.72 tax excluded
    Then current cart order shipping fees should be 7.0 tax included
    Then current cart order shipping fees should be 7.0 tax excluded
