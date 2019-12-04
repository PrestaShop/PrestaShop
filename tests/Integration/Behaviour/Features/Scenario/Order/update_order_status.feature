# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --name 'Orders statuses'
@reset-database-before-feature
Feature: Orders statuses from Back Office
  In order to change statuses of multiple orders
  As a Back Office (BO) user
  I need to be able to select order/orders and change status

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And there is existing order with id 1

  Scenario: Update multiple orders statuses using Bulk actions
    Given there is existing order with id 2
    When I update orders with ids "1,2" status to "Delivered"
    Then order with id 1 has status "Delivered"
    And order with id 2 has status "Delivered"

  Scenario: Update order status
    When I update order with id 1 to status "Awaiting bank wire payment"
    Then order with id 1 has status "Awaiting bank wire payment"
