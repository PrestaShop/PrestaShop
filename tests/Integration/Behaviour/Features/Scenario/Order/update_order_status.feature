# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --name 'Orders statuses'
@reset-database-before-feature
Feature: Orders statuses from Back Office
  In order to change statuses of multiple orders
  As a Back Office (BO) user
  I need to be able to select order/orders and change status

  Background:
    Given email sending is disabled
    Given the current currency is "EUR"
    Given there is existing order with id 1

  Scenario: Update multiple orders statuses using Bulk actions
    Given there is existing order with id 2
    When I update orders "1,2" to status "Delivered"
    Then order 1 has status "Delivered"
    And order 2 has status "Delivered"

  Scenario: Update order status
    When I update order 1 to status "Awaiting bank wire payment"
    Then order 1 has status "Awaiting bank wire payment"
