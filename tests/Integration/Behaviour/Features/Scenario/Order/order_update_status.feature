# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-update-status
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-update-status
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to update the order status and see its history

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists

  Scenario: I update the order status while logged as an employee
    Given I am logged in as "test@prestashop.com" employee
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And order "bo_order1" has status "Awaiting bank wire payment"
    And order "bo_order1" has the following status history:
      | status                     | employee_first_name | employee_last_name | api_client_id |
      | Awaiting bank wire payment | Puff                | Daddy              |               |
    When I update order "bo_order1" status to "Payment accepted"
    Then order "bo_order1" has status "Payment accepted"
    And order "bo_order1" has the following status history:
      | status                     | employee_first_name | employee_last_name | api_client_id |
      | Payment accepted           | Puff                | Daddy              |               |
      | Awaiting bank wire payment | Puff                | Daddy              |               |
    Given I am not logged in as an employee
    When I update order "bo_order1" status to "Delivered"
    Then order "bo_order1" has status "Delivered"
    And order "bo_order1" has the following status history:
      | status                     | employee_first_name | employee_last_name | api_client_id |
      | Delivered                  |                     |                    |               |
      | Payment accepted           | Puff                | Daddy              |               |
      | Awaiting bank wire payment | Puff                | Daddy              |               |

  Scenario: I update the order status while logged as an api client
    Given I create an api client "AC-1" with following properties:
      | clientName  | Thomas               |
      | apiClientId | api_client           |
      | enabled     | true                 |
      | description | a simple description |
      | lifetime    | 3600                 |
    Given I am not logged in as an employee
    Given I am logged in as api client with id "api_client"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And order "bo_order2" has status "Awaiting bank wire payment"
    And order "bo_order2" has the following status history:
      | status                     | employee_first_name | employee_last_name | api_client_id |
      | Awaiting bank wire payment |                     |                    |               |
    When I update order "bo_order2" status to "Payment accepted"
    Then order "bo_order2" has status "Payment accepted"
    And order "bo_order2" has the following status history:
      | status                     | employee_first_name | employee_last_name | api_client_id |
      | Payment accepted           |                     |                    | api_client    |
      | Awaiting bank wire payment |                     |                    |               |
    Given I am not logged in as an api client
    When I update order "bo_order2" status to "Delivered"
    Then order "bo_order2" has status "Delivered"
    And order "bo_order2" has the following status history:
      | status                     | employee_first_name | employee_last_name | api_client_id |
      | Delivered                  |                     |                    |               |
      | Payment accepted           |                     |                    | api_client    |
      | Awaiting bank wire payment |                     |                    |               |
