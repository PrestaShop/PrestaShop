# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --name 'Orders statuses'
@reset-database-before-feature
Feature: Orders statuses from Back Office
  In order to change statuses of multiple orders
  As a Back Office (BO) user
  I need to be able to select order/orders and change status

  Background:
    Given the current currency is "EUR"

  Scenario: Update multiple orders statuses using Bulk actions
    Given there are 2 existing orders
    When I update 2 orders to status "Delivered"
    Then each of 2 orders should contain status "Delivered"

  Scenario: Update order status by clicking on Status column and choosing the status
    Given there are 1 existing orders
    When I update order 1 to status "Awaiting bank wire payment"
    Then each of 1 orders should contain status "Awaiting bank wire payment"
