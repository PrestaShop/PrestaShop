# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --name 'Orders statuses'
@reset-database-before-feature
Feature: Orders statuses from Back Office
  In order to change statuses of single/multiple customer orders
  As a Back Office (BO) user
  I need to be able to select order/orders and change status

#  todo: refactor code to use domain classes when code base will mature - pull requests to be merged

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And the module "dummy_payment" is installed
    #    todo: use domain context to get employee
    And I am logged in as "test@prestashop.com" employee
    #    todo: use domain context to get customer
    And there is customer "testCustomer" with email "pub@prestashop.com"

  Scenario: Update multiple orders statuses using Bulk actions
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
#    And I add order "bo_order1" from cart "dummy_cart" with "dummy_payment" payment method and "Payment accepted" order status
#    And I add order "bo_order2" from cart "dummy_cart" with "dummy_payment" payment method and "Payment accepted" order status
    When I update orders "bo_order1,bo_order2" statuses to "Delivered"
    Then order "bo_order1" has status "Delivered"
    And order "bo_order2" has status "Delivered"

  Scenario: Update order status
    Given I create an empty cart "dummy_cart2" for customer "testCustomer2"
#    And I add order "bo_order3" from cart "dummy_cart2" with "dummy_payment2" payment method and "Payment accepted" order status
    When I update order "bo_order3" status to "Awaiting bank wire payment"
    Then order "bo_order3" has status "Awaiting bank wire payment"
