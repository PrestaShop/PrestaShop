# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shipment --tags order_create_shipment
@restore-all-tables-before-feature
@clear-cache-before-feature
@order_create_shipment
Feature: Create shipment with shipping cost calculation
  As a BO user
  I want to create a shipment for an existing order
  So that shipping costs are calculated and reflected on both the shipment and the order

  Background:
    Given I enable feature flag "improved_shipment"
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And there is a zone "north_america" named "North America"
    And I identify tax rules group named "US-FL Rate (6%)" as "us-fl-tax-rate"
    # Carrier A: price-based, 5.00 excl tax
    When I create carrier "carrier_a" with specified properties:
      | name             | Carrier A     |
      | active           | true          |
      | shippingMethod   | price         |
      | zones            | north_america |
      | shippingHandling | false         |
    Then I set ranges for carrier "carrier_a" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 10000    | 5           |
    When I set tax rule "us-fl-tax-rate" for carrier "carrier_a"
    # Carrier B: price-based, 12.00 excl tax
    When I create carrier "carrier_b" with specified properties:
      | name             | Carrier B     |
      | active           | true          |
      | shippingMethod   | price         |
      | zones            | north_america |
      | shippingHandling | false         |
    Then I set ranges for carrier "carrier_b" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 10000    | 12          |
    When I set tax rule "us-fl-tax-rate" for carrier "carrier_b"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I reference order "bo_order1" delivery address as "US"

  Scenario: Creating a shipment calculates its shipping cost based on the carrier and updates the order total
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 1
    And the order "bo_order1" should have the following shipments:
      | shipment  | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | carrier_a |                 | US      | 5.0                    | 5.3                    |
    When I create a shipment for order "bo_order1" with carrier "carrier_b" and product "Mug The best is yet to come" with quantity 1
    Then the order "bo_order1" should have the following shipments:
      | shipment     | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1    | carrier_a |                 | US      | 5.0                    | 5.3                    |
      | new_shipment | carrier_b |                 | US      | 12.0                   | 12.72                  |
    # Order total = sum of all shipments: 5.0 + 12.0 = 17.0 excl, 5.3 + 12.72 = 18.02 incl
    And order "bo_order1" should have the following shipping totals:
      | total_shipping_tax_excl | 17.0  |
      | total_shipping_tax_incl | 18.02 |

  Scenario: When PS_ORDER_RECALCULATE_SHIPPING is disabled, new shipment costs are set to 0 and order total is not recalculated
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 0
    And the order "bo_order1" should have the following shipments:
      | shipment  | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | carrier_a |                 | US      | 5.0                    | 5.3                    |
    When I create a shipment for order "bo_order1" with carrier "carrier_b" and product "Mug The best is yet to come" with quantity 1
    Then the order "bo_order1" should have the following shipments:
      | shipment     | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1    | carrier_a |                 | US      | 5.0                    | 5.3                    |
      | new_shipment | carrier_b |                 | US      | 0.0                    | 0.0                    |
    # Order total unchanged since recalculation is disabled
    And order "bo_order1" should have the following shipping totals:
      | total_shipping_tax_excl | 5.0 |
      | total_shipping_tax_incl | 5.3 |

  Scenario: Creating multiple shipments accumulates shipping costs on the order
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 1
    And the order "bo_order1" should have the following shipments:
      | shipment  | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | carrier_a |                 | US      | 5.0                    | 5.3                    |
    When I create a shipment for order "bo_order1" with carrier "carrier_b" and product "Mug The best is yet to come" with quantity 1
    And the order "bo_order1" should have "2" shipments:
    And I create a shipment for order "bo_order1" with carrier "carrier_a" and product "Mug The best is yet to come" with quantity 1
    And the order "bo_order1" should have "3" shipments:
    # Order total = 5.0 + 12.0 + 5.0 = 22.0 excl, 5.3 + 12.72 + 5.3 = 23.32 incl
    Then order "bo_order1" should have the following shipping totals:
      | total_shipping_tax_excl | 22.0  |
      | total_shipping_tax_incl | 23.32 |
