# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --name 'Orders statuses'
@reset-database-before-feature
Feature: Orders statuses from Back Office
  In order to change statuses of multiple orders
  As a Back Office (BO) user
  I need to be able to select order/orders and change status

  Background:
    Given the current currency is "EUR"
    Given there is existing order with reference "XKBKNABJK"

  Scenario: Update multiple orders statuses using Bulk actions
    Given there is existing order with reference "OHSATSERP"
    When I update orders "XKBKNABJK,OHSATSERP" to status "Delivered"
    Then order "XKBKNABJK" has status "Delivered"
    And order "OHSATSERP" has status "Delivered"

  Scenario: Update order status
    When I update order "XKBKNABJK" to status "Awaiting bank wire payment"
    Then order "XKBKNABJK" has status "Awaiting bank wire payment"
