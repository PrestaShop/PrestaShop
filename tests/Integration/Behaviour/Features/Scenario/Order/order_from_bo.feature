# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order
@reset-database-before-feature
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  #  todo: fix the failing scenarios, make scenarios independent, not use legacy classes as much as possible

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    #    todo: use domain context to get employee
    And I am logged in as "test@prestashop.com" employee
    #    todo: use domain context to get customer
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country

  Scenario: Add order from Back Office with free shipping
    When I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products with reference "demo_13" to the cart "dummy_cart"
    And I set Free shipping to the cart "dummy_cart"
    And I add order "bo_order_for_free_shipping" with the following details:
      | cart                | dummy_cart          |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    Then order "bo_order_for_free_shipping" should have 2 products in total
    And order "bo_order_for_free_shipping" should have free shipping
    And order "bo_order_for_free_shipping" should have "dummy_payment" payment method

  Scenario: Update multiple orders statuses using Bulk actions
    Given I create an empty cart "dummy_cart2" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart2"
    And I add 2 products with reference "demo_13" to the cart "dummy_cart2"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart2          |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart2          |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    When I update orders "bo_order1,bo_order2" statuses to "Delivered"
    Then order "bo_order1" has status "Delivered"
    And order "bo_order2" has status "Delivered"

  Scenario: Update order status
    Given I create an empty cart "dummy_cart3" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart3"
    And I add 2 products with reference "demo_13" to the cart "dummy_cart3"
    And I add order "bo_order3" with the following details:
      | cart                | dummy_cart3         |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    When I update order "bo_order3" status to "Awaiting bank wire payment"
    Then order "bo_order3" has status "Awaiting bank wire payment"

  Scenario: Update order shipping details
    Given I create an empty cart "dummy_cart4" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart4"
    And I add 2 products with reference "demo_13" to the cart "dummy_cart4"
    And I add order "bo_order4" with the following details:
      | cart                | dummy_cart4         |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    When I update order "bo_order4" Tracking number to "TEST1234" and Carrier to "2 - My carrier (Delivery next day!)"
    Then order "bo_order4" has Tracking number "TEST1234"
    And order "bo_order4" has Carrier "2 - My carrier (Delivery next day!)"

  Scenario: pay order with negative amount and see it is not valid
    Given I create an empty cart "dummy_cart5" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart5"
    And I add 2 products with reference "demo_13" to the cart "dummy_cart5"
    And I add order "bo_order5" with the following details:
      | cart                | dummy_cart5            |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Awaiting check payment |
    When order "bo_order5" has 0 payments
    And I pay order "bo_order5" with the invalid following details:
      | date           | 2019-11-26 13:56:22 |
      | payment_method | Payments by check   |
      | transaction_id | test!@#$%%^^&* OR 1 |
      | id_currency    | 1                   |
      | amount         | -5.548              |
    Then order "bo_order5" has 0 payments

  Scenario: pay for order
    Given I create an empty cart "dummy_cart6" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart6"
    And I add 2 products with reference "demo_13" to the cart "dummy_cart6"
    And I add order "bo_order6" with the following details:
      | cart                | dummy_cart6            |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Awaiting check payment |
    When I pay order "bo_order6" with the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | id_currency    | 1                   |
      | amount         | 6.00                |
    Then order "bo_order6" payments should have the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | amount         | $6.00               |

  Scenario: change order state to Delivered to be able to add valid invoice to new Payment
    Given I create an empty cart "dummy_cart7" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart7"
    And I add 2 products with reference "demo_13" to the cart "dummy_cart7"
    And I add order "bo_order7" with the following details:
      | cart                | dummy_cart7             |
      | message             | test                    |
      | payment module name | dummy_payment           |
      | status              | Awaiting check payment  |
    When order "bo_order7" has 0 payments
    And I update order "bo_order7" status to "Delivered"
    Then order "bo_order7" payments should have invoice
