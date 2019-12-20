# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order
@reset-database-before-feature
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  #  todo: fix the failing scenarios/code
  #  todo: make scenarios independent
  #  todo: change legacy classes with domain where possible
  #  todo: increase code re-use

  Background:
    Given email sending is disabled
    #    todo: improve context to accept EditableCurrency|ReferenceCurrency instead of legacy Currency object
    #    todo: use domain GetCurrencyForEditing|GetReferenceCurrency to add currency to context
    And the current currency is "USD"
    #    todo: use domain context for Country
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
     #    todo: use domain context to get customer: GetCustomerForViewing;
     #    todo: find a way how to get customer object/id by its properties without using legacy objects
     #    possible solution can be create new customer with AddCustomerHandler
     #    but then how to add Customer Address using domain classes???
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    #    todo: find a way to create country without legacy object
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Update order status
    When I update order "bo_order1" status to "Awaiting Cash On Delivery validation"
    Then order "bo_order1" has status "Awaiting Cash On Delivery validation"

  Scenario: Update order shipping details
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "2 - My carrier (Delivery next day!)"
    Then order "bo_order1" has Tracking number "TEST1234"
    And order "bo_order1" has Carrier "2 - My carrier (Delivery next day!)"

  Scenario: pay order with negative amount and see it is not valid
    When order "bo_order1" has 0 payments
    And I pay order "bo_order1" with the invalid following details:
      | date           | 2019-11-26 13:56:22 |
      | payment_method | Payments by check   |
      | transaction_id | test!@#$%%^^&* OR 1 |
      | id_currency    | 1                   |
      | amount         | -5.548              |
    Then order "bo_order1" has 0 payments

  Scenario: pay for order
    When I pay order "bo_order1" with the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | id_currency    | 1                   |
      | amount         | 6.00                |
    Then order "bo_order1" payments should have the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | amount         | $6.00               |

  Scenario: Change order state to Delivered to be able to add valid invoice to new Payment
    When order "bo_order1" has 0 payments
    And I update order "bo_order1" status to "Delivered"
    Then order "bo_order1" payments should have invoice

  Scenario: Duplicate order cart
    When I duplicate order "bo_order1" cart "dummy_cart" with reference "duplicated_dummy_cart"
    Then there is duplicated cart "duplicated_dummy_cart" for cart dummy_cart

  Scenario: Add product to an existing Order with free shipping and new invoice
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    # no exception is thrown when zero/negative amount is passed and nothing changes in the db`
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | -1                      |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 0                       |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 3 products "Mug Today is a good day"

  Scenario: Generating invoice for Order
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice

  Scenario: Add order from Back Office with free shipping
    And I set Free shipping to the cart "dummy_cart"
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart          |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    Then order "bo_order2" should have 2 products in total
    And order "bo_order2" should have free shipping
    And order "bo_order2" should have "dummy_payment" payment method

  Scenario: Update multiple orders statuses using Bulk actions
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart          |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    When I update orders "bo_order1,bo_order2" statuses to "Delivered"
    Then order "bo_order1" has status "Delivered"
    And order "bo_order2" has status "Delivered"

  Scenario: Change order shipping address
    Given I create new address with following details:
      | Customer email  | pub@prestashop.com   |
      | Address alias   | dummyCustomerAddress |
      | First Name      | CustomerName         |
      | Last Name       | CustomerSurname      |
      | Address         | Street st. 1         |
      | Zip/Postal Code | 11111                |
      | City            | Paris                |
      | Country         | France               |
    When I change order "bo_order1" shipping address to "1601 Willow Rd Menlo Park"
    Then order "bo_order1" shipping address should be "1601 Willow Rd Menlo Park"
