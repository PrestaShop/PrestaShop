# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-cancel-product
@reset-database-before-feature
Feature: Cancel Order Product from Back Office (BO)
  In order to cancel order products for FO customers
  As a BO user
  I need to be able to cancel an order's products from the BO

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
    And I add 5 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add 3 products "Mug Today is a good day" to the cart "dummy_cart"
    And I watch the stock of product "Mug The best is yet to come"
    And I watch the stock of product "Mug Today is a good day"

  @order-cancel-product
  Scenario: Cancel product feature has expected behavior
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And there are 5 less "Mug The best is yet to come" in stock
    And there are 3 less "Mug Today is a good day" in stock
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 2        |
      | Mug Today is a good day     | 1        |
    Then order "bo_order_cancel_product" should contain 3 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 2 products "Mug Today is a good day"
    And product "Mug The best is yet to come" in order "bo_order_cancel_product" has following details:
      | product_quantity            | 3           |
      | product_quantity_refunded   | 0           |
      | product_quantity_reinjected | 0           |
      | total_refunded_tax_excl     | 0.000000    |
      | total_refunded_tax_incl     | 0.000000    |
    And product "Mug Today is a good day" in order "bo_order_cancel_product" has following details:
      | product_quantity            | 2           |
      | product_quantity_refunded   | 0           |
      | product_quantity_reinjected | 0           |
      | total_refunded_tax_excl     | 0.000000    |
      | total_refunded_tax_incl     | 0.000000    |
    And there are 2 more "Mug The best is yet to come" in stock
    And there are 1 more "Mug Today is a good day" in stock

  @order-cancel-product
  Scenario: Order status is set to canceled when all products have been cancelled
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And there are 5 less "Mug The best is yet to come" in stock
    And there are 3 less "Mug Today is a good day" in stock
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 5        |
      | Mug Today is a good day     | 3        |
    Then order "bo_order_cancel_product" should contain 0 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 0 products "Mug Today is a good day"
    And order "bo_order_cancel_product" has status "Canceled"

  @order-cancel-product
  Scenario: Quantity is required
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And there are 5 less "Mug The best is yet to come" in stock
    And there are 3 less "Mug Today is a good day" in stock
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 0        |
      | Mug Today is a good day     | 1        |
    Then I should get error that cancel quantity is invalid

  @order-cancel-product
  Scenario: Quantity is too high
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And there are 5 less "Mug The best is yet to come" in stock
    And there are 3 less "Mug Today is a good day" in stock
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 565       |
      | Mug Today is a good day     | 1        |
    Then I should get error that cancel quantity is too high and max is 5

  @order-cancel-product
  Scenario: Order should not have invoice
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Payment accepted     |
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And there are 5 less "Mug The best is yet to come" in stock
    And there are 3 less "Mug Today is a good day" in stock
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 1       |
      | Mug Today is a good day     | 1        |
    Then I should get error that order is already paid
