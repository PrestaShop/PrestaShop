# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --name 'Order shipping'
@reset-database-before-feature
Feature: Order shipping details from Back Office
  PrestaShop allows to Update shipping details of the chosen order
  As a BO user
  I need to be able to Update order shipping details

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And there is existing order with id 1

  Scenario: Update order shipping details
    When I update order 1 Tracking number to "TEST1234" and Carrier to "My carrier"
    Then order 1 has Tracking number "TEST1234"
    And order 1 has Carrier "My carrier"
    When I update order 1 Tracking number to "TEST123" and Carrier to "0"
    Then order 1 has Tracking number "TEST123"
    And order 1 has Carrier "0"
