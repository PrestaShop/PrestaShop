# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shipment
@restore-all-tables-before-feature
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
