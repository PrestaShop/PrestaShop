# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --name 'Order shipping'
@reset-database-before-feature
Feature: Order shipping details from Back Office
  In order to manage customer shipping details
  As a BO user
  I need to be able to update order shipping details

  Background:
    Given email sending is disabled
    And the current currency is "USD"


