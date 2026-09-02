# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order_return
@restore-all-tables-before-feature
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
    And I add 1 products "Mug The adventure begins" to the cart "dummy_cart"
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

  Scenario: List products attached to an order return
    When I query the products of order return "testOrderReturn"
    Then the queried products of order return "testOrderReturn" should contain 2 rows
    And the queried products of order return "testOrderReturn" should contain a product with reference "demo_11" and quantity 2
    And the queried products of order return "testOrderReturn" should contain a product with reference "demo_12" and quantity 1

  Scenario: Delete one product from an order return
    When I delete from order return "testOrderReturn" the product with reference "demo_12"
    And I query the products of order return "testOrderReturn"
    Then the queried products of order return "testOrderReturn" should contain 1 row
    And the queried products of order return "testOrderReturn" should contain a product with reference "demo_11" and quantity 2

  Scenario: Bulk-delete products staged through the form leaves the return intact when at least one product remains
    When I stage for deletion in order return "testOrderReturn" the product with reference "demo_12"
    And I commit staged deletions on order return "testOrderReturn"
    And I query the products of order return "testOrderReturn"
    Then the queried products of order return "testOrderReturn" should contain 1 row
    And the queried products of order return "testOrderReturn" should contain a product with reference "demo_11" and quantity 2

  Scenario: Cannot delete the last product from an order return
    When I delete from order return "testOrderReturn" the product with reference "demo_12"
    And I try to delete from order return "testOrderReturn" the product with reference "demo_11"
    Then I should get a "CannotDeleteLastProductFromOrderReturnException"

  Scenario: Cannot bulk-delete every product from an order return
    When I stage for deletion in order return "testOrderReturn" the product with reference "demo_11"
    And I stage for deletion in order return "testOrderReturn" the product with reference "demo_12"
    And I try to commit staged deletions on order return "testOrderReturn"
    Then I should get a "CannotDeleteLastProductFromOrderReturnException"
