# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shipment --tags products-with-different-carriers
@restore-all-tables-before-feature
@clear-cache-before-feature
@products-with-different-carriers
Feature: Product associated with different carriers
  As a BO users
  I want to order products associated with different carriers
  I order them multiple order shipments are created

  Background:
    Given I enable feature flag "improved_shipment"
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And there is a zone "north_america" named "North America"
    # Create two carriers different with prices
    And I identify tax rules group named "US-FL Rate (6%)" as "us-fl-tax-rate"
    When I create carrier "beer_carrier" with specified properties:
      | name           | Beer carrier  |
      | active         | true          |
      | shippingMethod | price         |
      | zones          | north_america |
      | shippingHandling          | false |
    Then I set ranges for carrier "beer_carrier" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 1000     | 5           |
    When I set tax rule "us-fl-tax-rate" for carrier "beer_carrier"
    Then carrier "beer_carrier" should have the following properties:
      | name         | Beer carrier    |
      | taxRuleGroup | US-FL Rate (6%) |
    When I create carrier "saucisson_carrier" with specified properties:
      | name           | Saucisson carrier  |
      | active         | true          |
      | shippingMethod | price         |
      | zones          | north_america |
      | shippingHandling          | false |
    Then I set ranges for carrier "saucisson_carrier" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 1000     | 10           |
    When I set tax rule "us-fl-tax-rate" for carrier "saucisson_carrier"
    Then carrier "saucisson_carrier" should have the following properties:
      | name         | Saucisson carrier    |
      | taxRuleGroup | US-FL Rate (6%) |
    When I add product "bottle_of_beer" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    When I update product "bottle_of_beer" stock with following information:
      | delta_quantity | 51  |
      | location       | dtc |
    And product "bottle_of_beer" should have following stock information:
      | quantity | 51  |
      | location | dtc |
    And I assign product bottle_of_beer with following carriers:
      | beer_carrier |
    Then product bottle_of_beer should have following shipping information:
      | carriers | [beer_carrier] |
    And I enable product "bottle_of_beer"
    When I add product "bottle_of_sparking_water" with following information:
      | name[en-US] | bottle of sparking water |
      | type        | standard       |
    When I update product "bottle_of_sparking_water" stock with following information:
      | delta_quantity | 51  |
      | location       | dtc |
    And product "bottle_of_sparking_water" should have following stock information:
      | quantity | 51  |
      | location | dtc |
    And I assign product bottle_of_sparking_water with following carriers:
      | beer_carrier |
    Then product bottle_of_sparking_water should have following shipping information:
      | carriers | [beer_carrier] |
    And I enable product "bottle_of_sparking_water"

    When I add product "saucisson" with following information:
      | name[en-US] | saucisson |
      | type        | standard  |
    When I update product "saucisson" stock with following information:
      | delta_quantity | 42  |
      | location       | dtc |
    And product "saucisson" should have following stock information:
      | quantity | 42  |
      | location | dtc |
    And I assign product saucisson with following carriers:
      | saucisson_carrier |
    Then product saucisson should have following shipping information:
      | carriers | [saucisson_carrier] |
    And I enable product "saucisson"

  Scenario: Retrieve shipments for existing order
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 1 products "bottle of beer" to the cart "dummy_cart"
    And I add 2 products "bottle_of_sparking_water" to the cart "dummy_cart"
    And I add 2 products "saucisson" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I reference order "bo_order1" delivery address as "US"
    Given the order "bo_order1" should have the following shipments:
      | shipment  | carrier           | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | beer_carrier      |                 | US      | 5.0                    | 5.3                   |
      | shipment2 | saucisson_carrier |                 | US      | 10.0                   | 10.6                  |
    Then the shipment "shipment1" should have the following products:
      | product_name   | quantity |
      | bottle of beer | 1        |
      | bottle of sparking water | 2        |
    Then the shipment "shipment2" should have the following products:
      | product_name | quantity |
      | saucisson    | 2        |

  Scenario: Retrieve available shipments for merge action
    Then order "bo_order1" should get available shipments for product "bottle of beer":
      | shipment_name                  | can_handle_merge |
      | Shipment 1 Beer carrier        | 1             |
      | Shipment 2 Saucisson carrier   | 0             |
    Then order "bo_order1" should get available shipments for product "saucisson":
      | shipment_name                  | can_handle_merge |
      | Shipment 1 Beer carrier        | 0             |
      | Shipment 2 Saucisson carrier   | 1             |

  Scenario: Splitting a shipment
    Given I create carrier "pickup_carrier" with specified properties:
      | name | Pickup |
    And I assign product "saucisson" with following carriers:
      | pickup_carrier |
    And I split the shipment "shipment2" to create a new shipment with "pickup_carrier" with following products:
      | product_name | quantity |
      | saucisson    | 1        |
    And the shipment "shipment2" should have the following products:
      | product_name | quantity |
      | saucisson    | 1        |
    And the order "bo_order1" should have "3" shipments:
    Then the shipment "shipment3" should have the following products:
      | product_name | quantity |
      | saucisson    | 1        |

  Scenario: Merge product into a shipment with specified quantity
    Given I merge product from "shipment2" into "shipment1" with following information:
      | product_name | quantity |
      | saucisson    | 1        |
    Then the shipment "shipment1" should have the following products:
      | product_name   | quantity |
      | bottle of beer | 1        |
      | bottle of sparking water | 2        |
      | saucisson      | 1        |
    And the shipment "shipment2" should have the following products:
      | product_name   | quantity |
      | saucisson      | 1        |
    And the shipment "shipment2" should be deleted

  Scenario: Merge product into a shipment with full quantity
    Given I merge product from "shipment3" into "shipment1" with following information:
      | product_name | quantity |
      | saucisson    | 1        |
    Then the shipment "shipment1" should have the following products:
      | product_name   | quantity |
      | bottle of beer | 1        |
      | bottle of sparking water | 2        |
      | saucisson      | 2        |
    And the shipment "shipment2" should be deleted

  Scenario: Retrieve available shipments for merge action
    Then order "bo_order1" should get available shipments for product "bottle of beer":
      | shipment_name                  | can_handle_merge |
      | Shipment 1 Beer carrier        | 1             |
      | Shipment 2 Saucisson carrier   | 0             |
    Then order "bo_order1" should get available shipments for product "saucisson":
      | shipment_name                  | can_handle_merge |
      | Shipment 1 Beer carrier        | 0             |
      | Shipment 2 Saucisson carrier   | 1             |

  Scenario: Delete product from a existing shipment:
    When I remove product from the shipment "shipment1" with following properties:
    | product_name |
    | bottle of beer |
    Then the shipment "shipment1" should have the following products:
    | product_name | quantity |
    |  bottle of sparking water   |     2    |
    |  saucisson   |     2    |
