# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shipment --tags shipment
@restore-all-tables-before-feature
@shipment
Feature: Retrieving shipment for orders
  As a BO users
  I want to retrieve the list of shipments associated with a specific order
  In order to be able to track the shipment of this order

  Background:
    Given I enable feature flag "improved_shipment"
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 1 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add 2 products "Mug Today is a good day" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I reference order "bo_order1" delivery address as "US"

  Scenario: Retrieve shipmets for existing order
    Given the order "bo_order1" should have the following shipments:
      | shipment  | carrier         | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | default_carrier |                 | US      |                    7.0 |                   7.42 |
    Then the shipment "shipment1" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come |        1 |
      | Mug Today is a good day     |        2 |

  Scenario: Switch shipment carrier
    Given the order "bo_order1" should have the following shipments:
      | shipment  | carrier         | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | default_carrier |                 | US      |                    7.0 |                   7.42 |
    Given group "visitor" named "Visitor" exists
    Given group "guest" named "Guest" exists
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    When I create carrier "new_carrier" with specified properties:
      | name             | Blyet Carrier                      |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | shippingHandling | false                              |
      | zones            | zone1                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I switch the carrier for shipment "shipment1" to "new_carrier"
    Then the order "bo_order1" should have the following shipments:
      | shipment  | carrier         | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | new_carrier     |                 | US      |                    7.0 |                   7.42 |

  Scenario: Retrieve shipment for viewing
    Given the order "bo_order1" should have the following shipments:
      | shipment  | carrier         | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | default_carrier |                 | US      |                    7.0 |                   7.42 |
    Then the shipment view "shipment1" should contain:
      | tracking_number |                 |
      | carrier_name    | My carrier      |
      | firstname       | John            |
      | lastname        | DOE             |
      | address1        | 16, Main street |
      | postcode        | 33133           |
      | city            | Miami           |
      | state           | Florida         |
      | country         | United States   |
