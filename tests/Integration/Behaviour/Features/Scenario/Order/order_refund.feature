# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-refund
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

  @order-refund
  Scenario: Partial refund of products without restock
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 10.5   |
      | Mug Today is a good day     | 1                        | 3.5    |
    Then "bo_order_refund" last credit slip is:
      | amount                  | 14.0 |
      | shipping_cost_amount    | 0.0  |
      | total_products_tax_excl | 14.0 |
      | total_products_tax_incl | 14.0 |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And order "bo_order_refund" should contain 1 refunded products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 refunded products "Mug Today is a good day"
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  Scenario: Partial refund of products with restock
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 2                        | 7.5    |
    Then "bo_order_refund" last credit slip is:
      | amount                  | 7.5  |
      | shipping_cost_amount    | 0.0  |
      | total_products_tax_excl | 7.5  |
      | total_products_tax_incl | 7.5  |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And order "bo_order_refund" should contain 2 refunded products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 0 refunded products "Mug Today is a good day"
    And there are 2 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  Scenario: Partial refund of products restock when not delivered yet (even with restock false)
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Payment accepted           |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 2                        | 7.5    |
    Then "bo_order_refund" last credit slip is:
      | amount                  | 7.5  |
      | shipping_cost_amount    | 0.0  |
      | total_products_tax_excl | 7.5  |
      | total_products_tax_incl | 7.5  |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And order "bo_order_refund" should contain 2 refunded products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 0 refunded products "Mug Today is a good day"
    And there are 2 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  Scenario: Partial refund of products with shipping discount
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 8      |
      | shipping_refund             |                          | 7.5    |
    Then "bo_order_refund" last credit slip is:
      | amount                  | 8.0  |
      | shipping_cost_amount    | 7.5  |
      | total_products_tax_excl | 8.0  |
      | total_products_tax_incl | 8.0  |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And order "bo_order_refund" should contain 0 refunded products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 refunded products "Mug Today is a good day"
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 1 more "Mug Today is a good day" in stock

  @order-refund
  Scenario: Partial refund of products paid partially with voucher
    Given I use a voucher "PROMO5" for a discount of 5.0 on the cart "dummy_cart"
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 8      |
      | shipping_refund             |                          | 7.5    |
    # Weird behavior, we are in tax EXCLUDED display, so total_products_tax_excl contains the initial refund
    # amount, and total_products_tax_incl the real one (minus voucher) If we had been in tax INCLUDED display
    # it would have been the opposite
    Then "bo_order_refund" last credit slip is:
      | amount                  | 8.0 |
      | shipping_cost_amount    | 7.5 |
      | total_products_tax_excl | 8.0 |
      | total_products_tax_incl | 3.0 |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And order "bo_order_refund" should contain 0 refunded products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 refunded products "Mug Today is a good day"
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  Scenario: Partial refund of products via a generated voucher
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock with voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 8      |
      | shipping_refund             |                          | 7.5    |
    Then "bo_order_refund" last credit slip is:
      | amount                  | 8.0 |
      | shipping_cost_amount    | 7.5 |
      | total_products_tax_excl | 8.0 |
      | total_products_tax_incl | 8.0 |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And order "bo_order_refund" should contain 0 refunded products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 refunded products "Mug Today is a good day"
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock
    And customer "testCustomer" has voucher of 15.5

  @order-refund
  Scenario: Quantity is required
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 0                        | 8      |
    Then I should get error that refund quantity is empty

  @order-refund
  Scenario: Amount is required
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And order "bo_order_refund" should contain 2 products "Mug The best is yet to come"
    And order "bo_order_refund" should contain 1 products "Mug Today is a good day"
    And there are 2 less "Mug The best is yet to come" in stock
    And there are 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 0      |
    Then I should get error that refund amount is empty
