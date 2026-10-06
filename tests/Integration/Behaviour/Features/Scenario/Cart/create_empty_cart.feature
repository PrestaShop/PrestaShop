# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags create-empty-cart
@restore-all-tables-before-feature
@create-empty-cart
Feature: Create an empty cart for a customer
  As a BO user
  I must be able to create an empty cart for an existing customer only

  Scenario: Create an empty cart for an existing customer
    Given there is customer "customer1" with email "pub@prestashop.com"
    When I create an empty cart "cart1" for customer "customer1"
    Then cart "cart1" should belong to customer "customer1"

  Scenario: Creating an empty cart for a customer that does not exist is rejected
    Given customer "unknownCustomer" does not exist
    When I create an empty cart "cart2" for customer "unknownCustomer"
    Then I should get error that customer was not found
