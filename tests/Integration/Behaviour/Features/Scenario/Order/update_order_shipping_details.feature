# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --name 'Order shipping'
@reset-database-before-feature
Feature: Order shipping details from Back Office
  PrestaShop allows to Update shipping details of the chosen order
  As a BO user
  I need to be able to go to order view, select Shipping and Edit->Update shipping details

  Background:
    Given the current currency is "USD"
    Given there is existing order with reference "XKBKNABJK"

  Scenario: Update order shipping details
    When I update order "XKBKNABJK" Tracking number to "TEST1234" and Carrier to "My carrier"
    Then order "XKBKNABJK" has Tracking number "TEST1234"
    And order "XKBKNABJK" has Carrier "My carrier"
    When I update order "XKBKNABJK" Tracking number to "TEST123" and Carrier to "0"
    Then order "XKBKNABJK" has Tracking number "TEST123"
    And order "XKBKNABJK" has Carrier "0"
