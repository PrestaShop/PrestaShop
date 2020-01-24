# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-standard-refund
@reset-database-before-feature
Feature: Refund Order from Back Office (BO)
  In order to refund orders for FO customers
  As a BO user
  I need to be able to refund orders from the BO

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
    And I add 1 products "Mug Today is a good day" to the cart "dummy_cart"
    And I watch the stock of product "Mug The best is yet to come"
    And I watch the stock of product "Mug Today is a good day"

  @order-standard-refund
  Scenario: Standard refund can't be done if not enabled
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    When I issue a standard refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 |
      | Mug The best is yet to come | 1                        |
      | Mug Today is a good day     | 1                        |
    Then I should get error that return product is disabled
    And "bo_order_refund" has 0 credit slips

  @order-standard-refund
  Scenario: Standard refund of products without restock
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a standard refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 |
      | Mug The best is yet to come | 1                        |
      | Mug Today is a good day     | 1                        |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 23.8 |
      | shipping_cost_amount    | 0.0   |
      | total_products_tax_excl | 23.8 |
      | total_products_tax_incl | 23.8 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2    |
      | product_quantity_refunded   | 1    |
      | product_quantity_reinjected | 0    |
      | total_refunded_tax_excl     | 11.9 |
      | total_refunded_tax_incl     | 11.9 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1   |
      | product_quantity_refunded   | 1   |
      | product_quantity_reinjected | 0   |
      | total_refunded_tax_excl     | 11.9 |
      | total_refunded_tax_incl     | 11.9 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-standard-refund
  Scenario: Standard refund of products with restock
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a standard refund on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity                 |
      | Mug The best is yet to come | 2                        |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 23.8  |
      | shipping_cost_amount    | 0.0  |
      | total_products_tax_excl | 23.8  |
      | total_products_tax_incl | 23.8  |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2   |
      | product_quantity_refunded   | 2   |
      | product_quantity_reinjected | 2   |
      | total_refunded_tax_excl     | 23.8 |
      | total_refunded_tax_incl     | 23.8 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
      | product_quantity_refunded   | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And there are 2 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock
