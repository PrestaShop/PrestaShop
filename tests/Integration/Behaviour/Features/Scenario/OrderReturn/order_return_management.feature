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

  Scenario: Each order return of the same order only lists its own lines, customized lines once with their data
    Given I create an empty cart "dummy_cart2" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart2"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart2"
    And I add 1 customized products with reference "demo_14" with all its customizations to the cart "dummy_cart2"
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart2                |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I add order return "classicReturn" from order "bo_order2" with the following products:
      | reference | quantity |
      | demo_11   | 2        |
    And I add order return "customizedReturn" from order "bo_order2" with the following products:
      | reference | quantity |
      | demo_14   | 1        |
    When I query the products of order return "classicReturn"
    Then the queried products of order return "classicReturn" should contain 1 row
    And the queried products of order return "classicReturn" should contain a product with reference "demo_11" and quantity 2
    When I query the products of order return "customizedReturn"
    Then the queried products of order return "customizedReturn" should contain 1 row
    And the queried products of order return "customizedReturn" should contain a product with reference "demo_14" and quantity 1
    And the queried product with reference "demo_14" of order return "customizedReturn" should be customized with text "Toto"

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

  Scenario: Deleting a line that is not part of the order return is refused
    When I try to delete from order return "testOrderReturn" the unknown order detail 999999
    Then I should get a "OrderReturnProductNotFoundException"
    When I query the products of order return "testOrderReturn"
    Then the queried products of order return "testOrderReturn" should contain 2 rows

  Scenario: An unknown order return does not stop the bulk delete of the others
    When I try to bulk delete the order return "testOrderReturn" and the unknown order return 999999
    Then I should get a "BulkDeleteOrderReturnsException"
    And the order return "testOrderReturn" should not exist
