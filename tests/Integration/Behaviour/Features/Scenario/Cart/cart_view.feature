# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags cart-view
@restore-all-tables-before-feature
@cart-view
Feature: View a cart in the back office
  As an employee
  I must be able to view a cart without changing what it contains

  Background:
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
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

  # An automatic cart rule has no code and is valid from before the order was placed. Viewing an
  # already ordered cart used to attach it, so the cart then showed a total the customer never paid.
  Scenario: Viewing an ordered cart does not attach an automatic cart rule created afterwards
    Given I have an empty default cart
    And email sending is disabled
    And I am logged in as "customer1"
    And I add 1 items of product "product1" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    And I validate my cart using payment module fake
    And there is a cart rule "autorule1" with following properties:
      | name[en-US]         | autorule1 |
      | discount_percentage | 10        |
    When I view the current cart in the back office
    Then the current cart should have 0 cart rules
