# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shipment --tags delivery-option-naming
@restore-all-tables-before-feature
@clear-cache-before-feature
@delivery-option-naming
Feature: Naming of a delivery option that covers several carriers
  As a customer
  I want the carrier named beside a shipping price to be the carrier that price pays for
  So that I can tell what I am choosing when my cart ships in several packages

  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And there is a zone "north_america" named "North America"
    And I identify tax rules group named "US-FL Rate (6%)" as "us-fl-tax-rate"
    When I create carrier "beer_carrier" with specified properties:
      | name           | Beer carrier  |
      | active         | true          |
      | shippingMethod | price         |
      | zones          | north_america |
      | shippingHandling | false       |
    Then I set ranges for carrier "beer_carrier" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 1000     | 5           |
    When I set tax rule "us-fl-tax-rate" for carrier "beer_carrier"
    When I create carrier "saucisson_carrier" with specified properties:
      | name           | Saucisson carrier |
      | active         | true              |
      | shippingMethod | price             |
      | zones          | north_america     |
      | shippingHandling | false           |
    Then I set ranges for carrier "saucisson_carrier" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 1000     | 10          |
    When I set tax rule "us-fl-tax-rate" for carrier "saucisson_carrier"
    When I add product "bottle_of_beer" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    When I update product "bottle_of_beer" stock with following information:
      | delta_quantity | 51  |
      | location       | dtc |
    And I assign product bottle_of_beer with following carriers:
      | beer_carrier |
    And I enable product "bottle_of_beer"
    When I add product "saucisson" with following information:
      | name[en-US] | saucisson |
      | type        | standard  |
    When I update product "saucisson" stock with following information:
      | delta_quantity | 42  |
      | location       | dtc |
    And I assign product saucisson with following carriers:
      | saucisson_carrier |
    And I enable product "saucisson"

  Scenario: An option covering two carriers is named after both
    Given I create an empty cart "naming_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "naming_cart"
    And I add 1 products "bottle of beer" to the cart "naming_cart"
    And I add 1 products "saucisson" to the cart "naming_cart"
    # The two products ship with different carriers, so this option covers two packages and is priced for
    # both. Before the fix only the last carrier of the loop survived, so the checkout showed one carrier's
    # name against the price of two.
    Then the delivery options for cart "naming_cart" should be named:
      | name                            |
      | Beer carrier, Saucisson carrier |

  Scenario: An option covering a single carrier keeps that carrier's name
    Given I create an empty cart "single_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "single_cart"
    And I add 1 products "bottle of beer" to the cart "single_cart"
    Then the delivery options for cart "single_cart" should be named:
      | name         |
      | Beer carrier |
