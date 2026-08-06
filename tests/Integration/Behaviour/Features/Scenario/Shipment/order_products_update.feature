# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shipment --tags order_products_update
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-products-update-with-shipments
Feature: Update order products with shipments
  As a BO users
  I want to update order products with shipments

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

  Scenario: Update product in order with shipments
    Given I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I reference order "bo_order1" delivery address as "US"
    And the order "bo_order1" should have the following shipments:
      | shipment  | carrier         | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment1 | default_carrier |                 | US      |                    7.0 |                   7.42 |
    And the shipment "shipment1" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come |        1 |
      | Mug Today is a good day     |        2 |
    And I create carrier "pickup_carrier" with specified properties:
      | name | Pickup |
    And I split the shipment "shipment1" to create a new shipment with "pickup_carrier" with following products:
      | product_name             | quantity |
      | Mug Today is a good day  | 1        |
    And the shipment "shipment1" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    And the order "bo_order1" should have "2" shipments:
    And the shipment "shipment2" should have the following products:
      | product_name               | quantity |
      | Mug Today is a good day    | 1        |
    When I edit product "Mug Today is a good day" to order "bo_order1" with following products details:
      | shipment_mapping       | shipment1:2, shipment2:3     |
      | price                  | 11.90        |
      | amount                 | 5            |
    Then order "bo_order1" should contain 5 products "Mug Today is a good day"
    And product "Mug Today is a good day" in order "bo_order1" should have no specific price
    And product "Mug Today is a good day" in order "bo_order1" has following details:
      | product_quantity            | 5      |
    Then the shipment "shipment1" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 2        |
    Then the shipment "shipment2" should have the following products:
      | product_name               | quantity |
      | Mug Today is a good day    | 3        |

  Scenario: Update product in order with shipments with zero quantity
    Given I add order "bo_order2" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I reference order "bo_order2" delivery address as "US"
    And the order "bo_order2" should have the following shipments:
      | shipment  | carrier         | tracking_number | address | shipping_cost_tax_excl | shipping_cost_tax_incl |
      | shipment3 | default_carrier |                 | US      |                    7.0 |                   7.42 |
    And the shipment "shipment3" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come |        1 |
      | Mug Today is a good day     |        2 |
    And I split the shipment "shipment3" to create a new shipment with "pickup_carrier" with following products:
      | product_name             | quantity |
      | Mug Today is a good day  | 1        |
    And the shipment "shipment3" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    And the order "bo_order2" should have "2" shipments:
    And the shipment "shipment4" should have the following products:
      | product_name               | quantity |
      | Mug Today is a good day    | 1        |
    When I edit product "Mug Today is a good day" to order "bo_order2" with following products details:
      | shipment_mapping       | shipment3:1, shipment4:0     |
      | price                  | 11.90        |
      | amount                 | 1            |
    Then order "bo_order2" should contain 1 products "Mug Today is a good day"
    And product "Mug Today is a good day" in order "bo_order2" should have no specific price
    And product "Mug Today is a good day" in order "bo_order2" has following details:
      | product_quantity            | 1      |
    Then the shipment "shipment3" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then the shipment "shipment4" should be deleted
