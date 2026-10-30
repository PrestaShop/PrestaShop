# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-product-sales
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-product-sales
Feature: Sales counters follow quantity edits in Back Office orders
  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "salesCustomer" with email "pub@prestashop.com"
    And customer "salesCustomer" has address in "US" country
    And a carrier "sales_carrier" with name "My carrier" exists
    And I create an empty cart "sales_cart" for customer "salesCustomer"
    And I select "US" address as delivery and invoice address for customer "salesCustomer" in cart "sales_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "sales_cart"
    And I set sales counters for product "Mug The best is yet to come" to 0 units and 0 sales

  Scenario: Increasing, decreasing and repeating a quantity edit do not create another sale
    Given I add order "sales_order" with the following details:
      | cart                | sales_cart               |
      | message             | Sales counter regression |
      | payment module name | dummy_payment            |
      | status              | Payment accepted         |
    When I edit product "Mug The best is yet to come" to order "sales_order" with following products details:
      | amount | 5     |
      | price  | 11.90 |
    Then order "sales_order" should contain 5 products "Mug The best is yet to come"
    And sales counters for product "Mug The best is yet to come" should be 5 units and 1 sales
    When I edit product "Mug The best is yet to come" to order "sales_order" with following products details:
      | amount | 1     |
      | price  | 11.90 |
    Then sales counters for product "Mug The best is yet to come" should be 1 units and 1 sales
    When I edit product "Mug The best is yet to come" to order "sales_order" with following products details:
      | amount | 1     |
      | price  | 12.00 |
    Then sales counters for product "Mug The best is yet to come" should be 1 units and 1 sales

  Scenario: An invalid order only contributes its edited quantity when it becomes valid
    Given I add order "invalid_order" with the following details:
      | cart                | sales_cart               |
      | message             | Sales counter regression |
      | payment module name | dummy_payment            |
      | status              | Canceled                 |
    When I edit product "Mug The best is yet to come" to order "invalid_order" with following products details:
      | amount | 4     |
      | price  | 11.90 |
    Then order "invalid_order" should contain 4 products "Mug The best is yet to come"
    And sales counters for product "Mug The best is yet to come" should be 0 units and 0 sales
    When I update order "invalid_order" status to "Payment accepted"
    Then sales counters for product "Mug The best is yet to come" should be 4 units and 1 sales
    When I edit product "Mug The best is yet to come" to order "invalid_order" with following products details:
      | amount | 3     |
      | price  | 11.90 |
    Then sales counters for product "Mug The best is yet to come" should be 3 units and 1 sales
    When I update order "invalid_order" status to "Canceled"
    Then sales counters for product "Mug The best is yet to come" should be 0 units and 0 sales

  Scenario: A rejected quantity edit leaves sales counters unchanged
    Given I add order "rejected_order" with the following details:
      | cart                | sales_cart               |
      | message             | Sales counter regression |
      | payment module name | dummy_payment            |
      | status              | Payment accepted         |
    When I edit product "Mug The best is yet to come" to order "rejected_order" with following products details:
      | amount | 0     |
      | price  | 11.90 |
    Then I should get error that product quantity is invalid for order
    And order "rejected_order" should contain 2 products "Mug The best is yet to come"
    And sales counters for product "Mug The best is yet to come" should be 2 units and 1 sales
