# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order_return
@reset-database-before-feature
@clear-cache-before-feature
Feature: Order return Management
  As BO user I must be able to change status of order return

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I add order return state "orderReturnState1"
    And I add order return state "orderReturnState2"
    And I add order return "testOrderReturn" from order "bo_order1"

  Scenario: Change order return state
    When I change order return "testOrderReturn" state to "orderReturnState1"
    Then "testOrderReturn" has state "orderReturnState1"
    When I change order return "testOrderReturn" state to "orderReturnState2"
    Then "testOrderReturn" has state "orderReturnState2"
