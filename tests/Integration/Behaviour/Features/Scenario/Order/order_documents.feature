# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-documents
@restore-all-tables-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@order-documents
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to get documents from orders from the BO

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And the customer "testCustomer" has SIRET "49791663500061"
    And the customer "testCustomer" has APE "5829C"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Default : no documents
    Then the order "bo_order1" should have 0 document

  Scenario: Status : Payment accepted
    Given I update order "bo_order1" status to "Payment accepted"
    Then the order "bo_order1" should have 1 document
    Then the order "bo_order1" should have following documents:
      | referenceNumber | type          | amount |
      | #IN000001       | invoice       | $32.65 |

  Scenario: Status : Delivered
    Given I update order "bo_order1" status to "Delivered"
    Then the order "bo_order1" should have 2 documents
    Then the order "bo_order1" should have following documents:
      | referenceNumber | type          | amount |
      | #IN000002       | invoice       | $32.65 |
      | #DE000001       | delivery_slip | $32.65 |
