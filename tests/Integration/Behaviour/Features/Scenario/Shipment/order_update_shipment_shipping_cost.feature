# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shipment --tags order_update_shipment_shipping_cost
@restore-all-tables-before-feature
@clear-cache-before-feature
@order_update_shipment_shipping_cost
Feature: Shipping cost recalculation after product quantity update in shipment
  As a BO user
  I want to update the quantity of a product assigned to a shipment
  So that the shipment shipping cost and the order total are recalculated accordingly

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
    # Carrier A: weight-based, 5.00 excl tax for 0-1kg, 10.00 excl tax for 1-50kg (mug=0.3kg, 2=0.6kg→5, 4=1.2kg→10)
    When I create carrier "carrier_a" with specified properties:
      | name             | Carrier A     |
      | active           | true          |
      | shippingMethod   | weight        |
      | zones            | north_america |
      | shippingHandling | false         |
    Then I set ranges for carrier "carrier_a" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 1        | 5           |
      | north_america | 1          | 50       | 10          |
    When I set tax rule "us-fl-tax-rate" for carrier "carrier_a"
    # Carrier B: price-based, 8.00 excl tax
    When I create carrier "carrier_b" with specified properties:
      | name             | Carrier B     |
      | active           | true          |
      | shippingMethod   | price         |
      | zones            | north_america |
      | shippingHandling | false         |
    Then I set ranges for carrier "carrier_b" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 10000    | 8           |
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

  Scenario: Increasing product quantity recalculates shipping cost on the shipment and order total
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 1
    And the order "bo_order1" should have the following shipments:
      | shipment  | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | carrier_a |                 | US      | 5.0                    | 5.3                    |
    And the shipment "shipment1" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 2        |
    # Increase from 2 to 4
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | shipment_mapping | shipment1:4 |
      | price            | 11.9        |
      | amount           | 4           |
    Then order "bo_order1" should contain 4 products "Mug The best is yet to come"
    And the shipment "shipment1" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 4        |
    # 4 × 0.3 kg = 1.2 kg crosses the 1 kg boundary → shifts into 1-50 kg range — verify cost updated on shipment
    And the order "bo_order1" should have the following shipments:
      | shipment  | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | carrier_a |                 | US      | 10.0                   | 10.6                   |
    And order "bo_order1" should have the following shipping totals:
      | total_shipping_tax_excl | 10.0  |
      | total_shipping_tax_incl | 10.6  |

  Scenario: Decreasing product quantity recalculates shipping cost on the shipment and order total
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 1
    And the order "bo_order1" should have the following shipments:
      | shipment  | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | carrier_a |                 | US      | 5.0                    | 5.3                    |
    And I create a shipment for order "bo_order1" with carrier "carrier_b" and product "Mug The best is yet to come" with quantity 1
    And the order "bo_order1" should have "2" shipments:
    And the order "bo_order1" should have the following shipments:
      | shipment     | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1    | carrier_a |                 | US      | 5.0                    | 5.3                    |
      | new_shipment | carrier_b |                 | US      | 8.0                    | 8.48                   |
    # Decrease product quantity in shipment1 from 2 to 1
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | shipment_mapping | shipment1:1 |
      | price            | 11.9        |
      | amount           | 1           |
    Then order "bo_order1" should contain 1 products "Mug The best is yet to come"
    And the shipment "shipment1" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
    And the order "bo_order1" should have the following shipments:
      | shipment     | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1    | carrier_a |                 | US      | 5.0                    | 5.3                    |
      | new_shipment | carrier_b |                 | US      | 8.0                    | 8.48                   |
    # Order total = 5.0 + 8.0 = 13.0 excl, 5.3 + 8.48 = 13.78 incl
    And order "bo_order1" should have the following shipping totals:
      | total_shipping_tax_excl | 13.0  |
      | total_shipping_tax_incl | 13.78 |

  Scenario: Updating quantity when PS_ORDER_RECALCULATE_SHIPPING is disabled does not recalculate shipping cost
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 0
    And the order "bo_order1" should have the following shipments:
      | shipment  | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | carrier_a |                 | US      | 5.0                    | 5.3                    |
    # Increase from 2 to 4 — PS_ORDER_RECALCULATE_SHIPPING=0 means no recalculation at all
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | shipment_mapping | shipment1:4 |
      | price            | 11.9        |
      | amount           | 4           |
    Then order "bo_order1" should contain 4 products "Mug The best is yet to come"
    And the order "bo_order1" should have the following shipments:
      | shipment  | carrier   | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | carrier_a |                 | US      | 5.0                    | 5.3                    |
