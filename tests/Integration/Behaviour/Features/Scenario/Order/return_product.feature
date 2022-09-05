# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-return-product
@restore-all-tables-before-feature
@clear-cache-before-feature
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
  @order-return-product
  Scenario: Return product can't be done if merchandise return not enabled
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then I should get error that return product is disabled
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-return-product
  Scenario: Return product can't be done if order is not delivered
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Awaiting check payment |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then I should get error that order is not delivered
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-return-product
  Scenario: Return product can't be done if order is not delivered
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then I should get error that order is not delivered
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-return-product
  Scenario: Return product with restock
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    Then order "bo_order_refund" should have following details:
      | total_products           | 35.7  |
      | total_products_wt        | 37.84 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 0.0   |
      | total_discounts_tax_incl | 0.0   |
      | total_paid_tax_excl      | 42.7  |
      | total_paid_tax_incl      | 45.26 |
      | total_paid               | 45.26 |
      | total_paid_real          | 45.26 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 23.8   |
      | shipping_cost_amount    | 0.0    |
      | total_shipping_tax_incl | 0.0    |
      | total_shipping_tax_excl | 0.0    |
      | total_products_tax_excl | 23.8   |
      | total_products_tax_incl | 25.220000 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And there is 1 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock
    And order "bo_order_refund" should have following details:
      | total_products           | 35.7  |
      | total_products_wt        | 37.84 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 0.0   |
      | total_discounts_tax_incl | 0.0   |
      | total_paid_tax_excl      | 42.7  |
      | total_paid_tax_incl      | 45.26 |
      | total_paid               | 45.26 |
      | total_paid_real          | 45.26 |

  @order-refund
  @order-return-product
  Scenario: Return product without restock
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 23.8   |
      | shipping_cost_amount    | 0.0    |
      | total_shipping_tax_incl | 0.0    |
      | total_shipping_tax_excl | 0.0    |
      | total_products_tax_excl | 23.8   |
      | total_products_tax_incl | 25.220000 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 0      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 0      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  @order-return-product
  Scenario: Return product of products with shipping discount
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug Today is a good day     | 1        |
      | shipping_refund             | 1        |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 11.9  |
      | shipping_cost_amount    | 7.42   |
      | total_shipping_tax_incl | 7.42  |
      | total_shipping_tax_excl | 7.0   |
      | total_products_tax_excl | 11.9  |
      | total_products_tax_incl | 12.61 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
      | product_quantity_refunded   | 0 |
      | product_quantity_return     | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock

  @order-refund
  @order-return-product
  Scenario: Return product of products paid partially with voucher
    Given I use a voucher "PROMO5" for a discount of 5.0 on the cart "dummy_cart"
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    Then order "bo_order_refund" should have following details:
      | total_products           | 35.7  |
      | total_products_wt        | 37.84 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 5.0   |
      | total_discounts_tax_incl | 5.3   |
      | total_paid_tax_excl      | 37.7  |
      | total_paid_tax_incl      | 39.96 |
      | total_paid               | 39.96 |
      | total_paid_real          | 39.96 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug Today is a good day     | 1        |
      | shipping_refund             | 1        |
    Then "bo_order_refund" has 1 credit slips
    # Weird behavior, we are in tax EXCLUDED display, so total_products_tax_excl contains the initial refund
    # amount, and total_products_tax_incl the real one (minus voucher) If we had been in tax INCLUDED display
    # it would have been the opposite
    Then "bo_order_refund" last credit slip is:
      | amount                  | 11.9 |
      | shipping_cost_amount    | 7.42 |
      | total_shipping_tax_incl | 7.42 |
      | total_shipping_tax_excl | 7.0  |
      | total_products_tax_excl | 11.9 |
      | total_products_tax_incl | 7.31 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
      | product_quantity_refunded   | 0 |
      | product_quantity_return     | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock
    And order "bo_order_refund" should have following details:
      | total_products           | 35.7  |
      | total_products_wt        | 37.84 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 5.0   |
      | total_discounts_tax_incl | 5.3   |
      | total_paid_tax_excl      | 37.7  |
      | total_paid_tax_incl      | 39.96 |
      | total_paid               | 39.96 |
      | total_paid_real          | 39.96 |

  @order-refund
  @order-return-product
  Scenario: Return product of products via a generated voucher
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip with voucher on following products:
      | product_name                | quantity |
      | Mug Today is a good day     | 1        |
      | shipping_refund             | 1        |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 11.9  |
      | shipping_cost_amount    | 7.42  |
      | total_shipping_tax_incl | 7.42  |
      | total_shipping_tax_excl | 7.0   |
      | total_products_tax_excl | 11.9  |
      | total_products_tax_incl | 12.61 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
      | product_quantity_refunded   | 0 |
      | product_quantity_return     | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock
    And customer "testCustomer" last voucher is 18.9

  @order-refund
  @order-return-product
  Scenario: Return product of products without credit slip (voucher generation is required then)
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock without credit slip with voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then "bo_order_refund" has 0 credit slips
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And there is 1 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock
    And customer "testCustomer" last voucher is 23.8

  @order-refund
  @order-return-product
  Scenario: Quantity is required
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug Today is a good day     | 0        |
    Then I should get error that refund quantity is invalid
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-return-product
  Scenario: Minimum one product refund
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
    Then I should get error that no refunds is invalid
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-return-refund
  Scenario: Refund shipping only is allowed
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name    | quantity |
      | shipping_refund | 1        |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 0    |
      | shipping_cost_amount    | 7.42 |
      | total_shipping_tax_incl | 7.42 |
      | total_shipping_tax_excl | 7.0  |
      | total_products_tax_excl | 0    |
      | total_products_tax_incl | 0    |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
      | product_quantity_refunded   | 0 |
      | product_quantity_return     | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
      | product_quantity_refunded   | 0 |
      | product_quantity_return     | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  @order-return-product
  Scenario: Quantity too high is forbidden
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 3        |
    Then I should get error that refund quantity is too high and max is 2
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-return-product
  Scenario: Quantity must be positive
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug Today is a good day     | -1       |
    Then I should get error that refund quantity is invalid
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-return-product
  Scenario: Return product of products paid partially with a big voucher, too high refund
    Given I use a voucher "PROMO20" for a discount of 20.0 on the cart "dummy_cart"
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug Today is a good day     | 1        |
      | shipping_refund             | 1        |
    Then I should get error that refund amount is invalid
    Then "bo_order_refund" has 0 credit slips

  @order-refund
  @order-return-product
  Scenario: Return product with no generation is invalid
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock without credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug Today is a good day     | 1        |
      | shipping_refund             | 1        |
    Then I should get error that no generation is invalid
    Then "bo_order_refund" has 0 credit slips

  @order-refund
  @order-return-product
  Scenario: Multiple Return product of products
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart             |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Processing in progress |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And return product is enabled
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 11.9   |
      | shipping_cost_amount    | 0.0    |
      | total_shipping_tax_incl | 0.0    |
      | total_shipping_tax_excl | 0.0    |
      | total_products_tax_excl | 11.9   |
      | total_products_tax_incl | 12.610000 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
      | product_quantity_refunded   | 0 |
      | product_quantity_return     | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And there is 1 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock
    When I issue a return product on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then "bo_order_refund" has 2 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 23.8   |
      | shipping_cost_amount    | 0.0    |
      | total_shipping_tax_incl | 0.0    |
      | total_shipping_tax_excl | 0.0    |
      | total_products_tax_excl | 23.8   |
      | total_products_tax_incl | 25.220000 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 2      |
      | product_quantity_reinjected | 2      |
      | total_refunded_tax_excl     | 23.8   |
      | total_refunded_tax_incl     | 25.220000 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1      |
      | product_quantity_refunded   | 0      |
      | product_quantity_return     | 1      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And there is 1 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock
