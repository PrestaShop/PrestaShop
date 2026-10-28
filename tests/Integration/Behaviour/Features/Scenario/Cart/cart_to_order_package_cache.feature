# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags cart-to-order-package-cache
@restore-all-tables-before-feature
@cart-to-order-package-cache
Feature: Order contains every cart product when packages were computed before the last product was added
  As a merchant
  I must get every cart product in the order, even when the cart and the order are built in the same process

  Background:
    Given email sending is disabled
    And shipping handling fees are set to 2.0
    And there is a product in the catalog named "product1" with a price of 10.00 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 10.00 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 10.00 and 1000 items in stock
    And I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is an address named "address1" with postcode "1" in state "state1"
    And there is a customer named "customer1" whose email is "fake@prestashop.com"
    And address "address1" is associated to customer "customer1"
    And I create carrier "carrier1" with specified properties:
      | name  | carrier 1 |
      | zones | zone1     |
    And I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 10000    | 5.0         |

  # The free shipping cart rule makes the cart compute its package list while it only contains the first product
  @restore-cart-rules-after-scenario
  Scenario: 3 products added one by one with a free shipping cart rule
    Given I have an empty default cart
    And there is a cart rule "freeshipping" with following properties:
      | name[en-US]   | freeshipping |
      | priority      | 1            |
      | free_shipping | true         |
    When I am logged in as "customer1"
    And I select address "address1" in my cart
    And I add 1 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And I select carrier "carrier1" in my cart
    Then my cart total should be precisely 30.0 tax included
    When I validate my cart using payment module fake
    Then current cart order total for products should be 30.0 tax included
    And current cart order total discount should be 7.0 tax included
    And current cart order shipping fees should be 7.0 tax included
