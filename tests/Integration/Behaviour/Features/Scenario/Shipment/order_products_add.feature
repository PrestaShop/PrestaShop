# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shipment --tags order_products_add
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-products-add-with-shipments
@order_products_add
Feature: Add order products with shipments
  As a BO users
  I want to add order products with shipments

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

  Scenario: Add product to order assigned to an existing shipment
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
    And there is a product in the catalog named "Test Added Product" with a price of 15.0 and 100 items in stock
    And order with reference "bo_order1" does not contain product "Test Added Product"
    And the available stock for product "Test Added Product" should be 100
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Test Added Product |
      | amount        | 3                  |
      | price         | 15                 |
      | shipment_id   | shipment2          |
      | is_virtual    | false              |
    Then order "bo_order1" should contain 3 products "Test Added Product"
    And the available stock for product "Test Added Product" should be 97
    And product "Test Added Product" in order "bo_order1" has following details:
      | product_quantity            | 3     |
    Then the shipment "shipment1" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then the shipment "shipment2" should have the following products:
      | product_name               | quantity |
      | Mug Today is a good day    | 1        |
      | Test Added Product         | 3        |

  Scenario: Add product to order creating a new shipment
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
    And I create carrier "pickup_carrier2" with specified properties:
      | name | Pickup Express |
    And there is a product in the catalog named "Test Added Product" with a price of 15.0 and 100 items in stock
    And order with reference "bo_order2" does not contain product "Test Added Product"
    And the available stock for product "Test Added Product" should be 100
    When I add products to order "bo_order2" without invoice and the following products details:
      | name          | Test Added Product |
      | amount        | 2                  |
      | price         | 15                 |
      | carrier_id    | pickup_carrier2    |
      | is_virtual    | false              |
    Then order "bo_order2" should contain 2 products "Test Added Product"
    And the available stock for product "Test Added Product" should be 98
    And product "Test Added Product" in order "bo_order2" has following details:
      | product_quantity            | 2     |
    And the order "bo_order2" should have "2" shipments:
    Then the shipment "shipment3" should have the following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 2        |
    Then the shipment "shipment4" should have the following products:
      | product_name               | quantity |
      | Test Added Product         | 2        |
