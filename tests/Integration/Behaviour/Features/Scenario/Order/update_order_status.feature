# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --name 'Orders statuses'
@reset-database-before-feature
Feature: Orders statuses from Back Office
  In order to change statuses of single/multiple customer orders
  As a Back Office (BO) user
  I need to be able to select order/orders and change status

  Background:
    Given email sending is disabled
    And the current currency is "USD"

  Scenario: Update multiple orders statuses using Bulk actions
    When I update orders "1,2" statuses to "Delivered"
    Then order 1 has status "Delivered"
    And order 2 has status "Delivered"

  Scenario: Update order status
    When I update order 1 status to "Awaiting bank wire payment"
    Then order 1 has status "Awaiting bank wire payment"
