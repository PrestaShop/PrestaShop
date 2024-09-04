# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-carrier
@restore-all-tables-before-feature
@fo-cart-carrier
Feature: Cart calculation with carriers
  As a customer
  I must be able to have correct cart total when selecting carriers

  Background:
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    Given I add new zone "zone2" with following properties:
      | name    | zone2 |
      | enabled | true  |
    Given I create carrier "carrier1" with specified properties:
      | name             | carrier 1                          |
      | zones            | zone1, zone2                       |
    Given I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 10000    | 3.1         |
      | zone2   | 0          | 10000    | 4.3         |
    Given I create carrier "carrier2" with specified properties:
      | name | carrier 2 |
      | zones            | zone1, zone2                       |
    Given I set ranges for carrier "carrier2" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 10000    | 5.7         |
      | zone2   | 0          | 10000    | 6.2         |
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a country named "country2" and iso code "US" in zone "zone2"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is an address named "address2" with postcode "1" in state "state2"

  Scenario: Empty cart, carrier 1
    Given I have an empty default cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 0.0
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, carrier 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    Then cart shipping fees should be 5.1
    Then my cart total should be 24.912 tax included
    Then my cart total using previous calculation method should be 24.912 tax included

  Scenario: one product in cart, quantity 3, carrier 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
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
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 0.0
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1, carrier 2
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier2" in my cart
    Then cart shipping fees should be 7.7
    Then my cart total should be 27.512 tax included
    Then my cart total using previous calculation method should be 27.512 tax included

  Scenario: one product in cart, quantity 3, carrier 2
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
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
    Given I create carrier "carrier3" with specified properties:
      | name | carrier 3 |
      | zones            | zone1, zone2                       |
    Given I set ranges for carrier "carrier3" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 150      | 5.0         |
      | zone1   | 150        | 1000     | 0.0         |
    When I add 1 item of product "product1" in my cart
    When I select address "address1" in my cart
    When I select carrier "carrier3" in my cart
    Then cart shipping fees should be 2.0
    Then my cart total should be 153.0 tax included
    Then my cart total using previous calculation method should be 153.0 tax included
