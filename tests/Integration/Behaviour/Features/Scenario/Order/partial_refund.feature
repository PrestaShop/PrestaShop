# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-partial-refund
@reset-database-before-feature
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
  @order-partial-refund
  Scenario: Partial refund of products without restock
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    Then order "bo_order_refund" should have following details:
      | total_products           | 35.7   |
      | total_products_wt        | 37.840000 |
      | total_shipping           | 7.42   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 42.7   |
      | total_paid_tax_incl      | 45.260000 |
      | total_paid               | 45.260000 |
      | total_paid_real          | 45.260000 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 10.5   |
      | Mug Today is a good day     | 1                        | 3.5    |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 14.0  |
      | shipping_cost_amount    | 0.0   |
      | total_products_tax_excl | 14.0  |
      | total_products_tax_incl | 14.84 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2     |
      | product_quantity_refunded   | 1     |
      | product_quantity_reinjected | 0     |
      | total_refunded_tax_excl     | 10.5  |
      | total_refunded_tax_incl     | 11.13 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1    |
      | product_quantity_refunded   | 1    |
      | product_quantity_reinjected | 0    |
      | total_refunded_tax_excl     | 3.5  |
      | total_refunded_tax_incl     | 3.71 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock
    And order "bo_order_refund" should have following details:
      | total_products           | 35.7   |
      | total_products_wt        | 37.840000 |
      | total_shipping           | 7.42   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 42.7   |
      | total_paid_tax_incl      | 45.260000 |
      | total_paid               | 45.260000 |
      | total_paid_real          | 45.260000 |

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products with restock
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
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 2                        | 7.5    |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 7.5  |
      | shipping_cost_amount    | 0.0  |
      | total_products_tax_excl | 7.5  |
      | total_products_tax_incl | 7.95 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2    |
      | product_quantity_refunded   | 2    |
      | product_quantity_reinjected | 2    |
      | total_refunded_tax_excl     | 7.5  |
      | total_refunded_tax_incl     | 7.95 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
      | product_quantity_refunded   | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And there are 2 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products restock when not delivered yet (even with restock false)
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Payment accepted           |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 2                        | 7.5    |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 7.5  |
      | shipping_cost_amount    | 0.0  |
      | total_products_tax_excl | 7.5  |
      | total_products_tax_incl | 7.95 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2    |
      | product_quantity_refunded   | 2    |
      | product_quantity_reinjected | 2    |
      | total_refunded_tax_excl     | 7.5  |
      | total_refunded_tax_incl     | 7.95 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
      | product_quantity_refunded   | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And there are 2 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products with shipping discount
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    Then order "bo_order_refund" should have following details:
      | total_products           | 35.7   |
      | total_products_wt        | 37.840000 |
      | total_shipping           | 7.42   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 42.7   |
      | total_paid_tax_incl      | 45.260000 |
      | total_paid               | 45.260000 |
      | total_paid_real          | 45.260000 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 8      |
      | shipping_refund             |                          | 5.5    |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 8.0  |
      | shipping_cost_amount    | 5.83 |
      | total_shipping_tax_incl | 5.83 |
      | total_shipping_tax_excl | 5.5  |
      | total_products_tax_excl | 8.0  |
      | total_products_tax_incl | 8.48 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
      | product_quantity_refunded   | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1    |
      | product_quantity_refunded   | 1    |
      | product_quantity_reinjected | 1    |
      | total_refunded_tax_excl     | 8.0  |
      | total_refunded_tax_incl     | 8.48 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock
    And order "bo_order_refund" should have following details:
      | total_products           | 35.7   |
      | total_products_wt        | 37.840000 |
      | total_shipping           | 7.42   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 42.7   |
      | total_paid_tax_incl      | 45.260000 |
      | total_paid               | 45.260000 |
      | total_paid_real          | 45.260000 |

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products paid partially with voucher
    Given I use a voucher "PROMO5" for a discount of 5.0 on the cart "dummy_cart"
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    Then order "bo_order_refund" should have following details:
      | total_products           | 35.7   |
      | total_products_wt        | 37.840000 |
      | total_shipping           | 7.42   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
      | total_discounts_tax_excl | 5.0    |
      | total_discounts_tax_incl | 5.3    |
      | total_paid_tax_excl      | 37.7   |
      | total_paid_tax_incl      | 39.960000 |
      | total_paid               | 39.960000 |
      | total_paid_real          | 39.960000 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 8      |
      | shipping_refund             |                          | 5.5    |
    Then "bo_order_refund" has 1 credit slips
    # Weird behavior, we are in tax EXCLUDED display, so total_products_tax_excl contains the initial refund
    # amount, and total_products_tax_incl the real one (minus voucher) If we had been in tax INCLUDED display
    # it would have been the opposite
    Then "bo_order_refund" last credit slip is:
      | amount                  | 8.0  |
      | shipping_cost_amount    | 5.83 |
      | total_products_tax_excl | 8.0  |
      | total_products_tax_incl | 3.18 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
      | product_quantity_refunded   | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1    |
      | product_quantity_refunded   | 1    |
      | product_quantity_reinjected | 0    |
      | total_refunded_tax_excl     | 8.0  |
      | total_refunded_tax_incl     | 8.48 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock
    And order "bo_order_refund" should have following details:
      | total_products           | 35.7   |
      | total_products_wt        | 37.840000 |
      | total_shipping           | 7.42   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
      | total_discounts_tax_excl | 5.0    |
      | total_discounts_tax_incl | 5.3    |
      | total_paid_tax_excl      | 37.7   |
      | total_paid_tax_incl      | 39.960000 |
      | total_paid               | 39.960000 |
      | total_paid_real          | 39.960000 |

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products via a generated voucher
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    Then order "bo_order_refund" should have following details:
      | total_products           | 35.7   |
      | total_products_wt        | 37.840000 |
      | total_shipping           | 7.42   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 42.7   |
      | total_paid_tax_incl      | 45.260000 |
      | total_paid               | 45.260000 |
      | total_paid_real          | 45.260000 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock with credit slip with voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 8      |
      | shipping_refund             |                          | 5.5    |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 8.0  |
      | shipping_cost_amount    | 5.83 |
      | total_shipping_tax_incl | 5.83 |
      | total_shipping_tax_excl | 5.5  |
      | total_products_tax_excl | 8.0  |
      | total_products_tax_incl | 8.48 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
      | product_quantity_refunded   | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1    |
      | product_quantity_refunded   | 1    |
      | product_quantity_reinjected | 0    |
      | total_refunded_tax_excl     | 8.0  |
      | total_refunded_tax_incl     | 8.48 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock
    And customer "testCustomer" last voucher is 13.5
    And order "bo_order_refund" should have following details:
      | total_products           | 35.7   |
      | total_products_wt        | 37.840000 |
      | total_shipping           | 7.42   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 42.7   |
      | total_paid_tax_incl      | 45.260000 |
      | total_paid               | 45.260000 |
      | total_paid_real          | 45.260000 |

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products avoids refunding too much
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
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 1024   |
      | Mug Today is a good day     | 1                        | 1024   |
      | shipping_refund             |                          | 51     |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 23.8   |
      | shipping_cost_amount    | 7.42   |
      | total_shipping_tax_incl | 7.42   |
      | total_shipping_tax_excl | 7.0    |
      | total_products_tax_excl | 23.8   |
      | total_products_tax_incl | 25.220 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2      |
      | product_quantity_refunded   | 1      |
      | product_quantity_reinjected | 0      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1      |
      | product_quantity_refunded   | 1      |
      | product_quantity_reinjected | 0      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products without credit slip (voucher generation is required then)
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
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock without credit slip with voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 10.5   |
      | Mug Today is a good day     | 1                        | 3.5    |
    Then "bo_order_refund" has 0 credit slips
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2     |
      | product_quantity_refunded   | 1     |
      | product_quantity_reinjected | 1     |
      | total_refunded_tax_excl     | 10.5  |
      | total_refunded_tax_incl     | 11.13 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1    |
      | product_quantity_refunded   | 1    |
      | product_quantity_reinjected | 1    |
      | total_refunded_tax_excl     | 3.5  |
      | total_refunded_tax_incl     | 3.71 |
    And there is 1 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock
    And customer "testCustomer" last voucher is 14.0

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products with a lot of precision
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
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount              |
      | Mug The best is yet to come | 1                        | 10.5456889623154870 |
      | Mug Today is a good day     | 1                        | 3.5456889623154870  |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 14.091378 |
      | shipping_cost_amount    | 0.0       |
      | total_shipping_tax_incl | 0.0       |
      | total_shipping_tax_excl | 0.0       |
      | total_products_tax_excl | 14.091378 |
      | total_products_tax_incl | 14.940000 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2         |
      | product_quantity_refunded   | 1         |
      | product_quantity_reinjected | 0         |
      | total_refunded_tax_excl     | 10.550000 |
      | total_refunded_tax_incl     | 11.180000 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1        |
      | product_quantity_refunded   | 1        |
      | product_quantity_reinjected | 0        |
      | total_refunded_tax_excl     | 3.550000 |
      | total_refunded_tax_incl     | 3.760000 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  @order-partial-refund
  Scenario: Quantity is required
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
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 0                        | 8      |
    Then I should get error that refund quantity is invalid
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-partial-refund
  Scenario: Minimum one product refund
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
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
    Then I should get error that no refunds is invalid
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-partial-refund
  Scenario: Refund shipping only is allowed
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
    When I issue a partial refund on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name    | quantity | amount |
      | shipping_refund |          | 3.5    |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 0    |
      | shipping_cost_amount    | 3.71  |
      | total_shipping_tax_incl | 3.71 |
      | total_shipping_tax_excl | 3.5  |
      | total_products_tax_excl | 0    |
      | total_products_tax_incl | 0    |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
      | product_quantity_refunded   | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
      | product_quantity_refunded   | 0 |
      | product_quantity_reinjected | 0 |
      | total_refunded_tax_excl     | 0 |
      | total_refunded_tax_incl     | 0 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  @order-partial-refund
  Scenario: Quantity too high is forbidden
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
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 3                        | 8      |
    Then I should get error that refund quantity is too high and max is 2
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-partial-refund
  Scenario: Amount is required
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
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 0      |
    Then I should get error that refund amount is invalid
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-partial-refund
  Scenario: Amount must be positive
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
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" with restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | -2.5   |
    Then I should get error that refund amount is invalid
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products paid partially with a big voucher, too high refund
    Given I use a voucher "PROMO20" for a discount of 20.0 on the cart "dummy_cart"
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 8      |
      | shipping_refund             |                          | 5.5    |
    Then I should get error that refund amount is invalid
    Then "bo_order_refund" has 0 credit slips

  @order-refund
  @order-partial-refund
  Scenario: Partial refund with no generation is invalid
    And I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock without credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug Today is a good day     | 1                        | 8      |
      | shipping_refund             |                          | 5.5    |
    Then I should get error that no generation is invalid
    Then "bo_order_refund" has 0 credit slips

  @order-refund
  @order-partial-refund
  Scenario: Partial refund can't be done if order is not paid
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 10.5   |
      | Mug Today is a good day     | 1                        | 3.5    |
    Then I should get error that order is not paid
    And "bo_order_refund" has 0 credit slips

  @order-refund
  @order-partial-refund
  Scenario: Partial refund is possible if order is not paid but has payments
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And I pay order "bo_order_refund" with the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | currency       | USD                 |
      | amount         | 6.00                |
    And order "bo_order_refund" has 1 payments
    And "bo_order_refund" has 0 credit slips
    And order "bo_order_refund" has status "Awaiting check payment"
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 10.5   |
      | Mug Today is a good day     | 1                        | 3.5    |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 14.0  |
      | shipping_cost_amount    | 0.0   |
      | total_shipping_tax_incl | 0.0   |
      | total_shipping_tax_excl | 0.0   |
      | total_products_tax_excl | 14.0  |
      | total_products_tax_incl | 14.84 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2     |
      | product_quantity_refunded   | 1     |
      | product_quantity_reinjected | 1     |
      | total_refunded_tax_excl     | 10.5  |
      | total_refunded_tax_incl     | 11.13 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1    |
      | product_quantity_refunded   | 1    |
      | product_quantity_reinjected | 1    |
      | total_refunded_tax_excl     | 3.5  |
      | total_refunded_tax_incl     | 3.71 |
    And there is 1 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products when merchandise return enabled and order is delivered store returned products
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
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 10.5   |
      | Mug Today is a good day     | 1                        | 3.5    |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 14.0  |
      | shipping_cost_amount    | 0.0   |
      | total_shipping_tax_incl | 0.0   |
      | total_shipping_tax_excl | 0.0   |
      | total_products_tax_excl | 14.0  |
      | total_products_tax_incl | 14.84 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2     |
      | product_quantity_refunded   | 0     |
      | product_quantity_return     | 1     |
      | product_quantity_reinjected | 0     |
      | total_refunded_tax_excl     | 10.5  |
      | total_refunded_tax_incl     | 11.13 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1    |
      | product_quantity_refunded   | 0    |
      | product_quantity_return     | 1    |
      | product_quantity_reinjected | 0    |
      | total_refunded_tax_excl     | 3.5  |
      | total_refunded_tax_incl     | 3.71 |
    And there are 0 more "Mug The best is yet to come" in stock
    And there are 0 more "Mug Today is a good day" in stock

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products when merchandise return enabled and order is NOT delivered store refunded products (auto restock)
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
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 10.5   |
      | Mug Today is a good day     | 1                        | 3.5    |
    Then "bo_order_refund" has 1 credit slips
    Then "bo_order_refund" last credit slip is:
      | amount                  | 14.0  |
      | shipping_cost_amount    | 0.0   |
      | total_shipping_tax_incl | 0.0   |
      | total_shipping_tax_excl | 0.0   |
      | total_products_tax_excl | 14.0  |
      | total_products_tax_incl | 14.84 |
    And product "Mug The best is yet to come" in order "bo_order_refund" has following details:
      | product_quantity            | 2     |
      | product_quantity_refunded   | 1     |
      | product_quantity_return     | 0     |
      | product_quantity_reinjected | 1     |
      | total_refunded_tax_excl     | 10.5  |
      | total_refunded_tax_incl     | 11.13 |
    And product "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1    |
      | product_quantity_refunded   | 1    |
      | product_quantity_return     | 0    |
      | product_quantity_reinjected | 1    |
      | total_refunded_tax_excl     | 3.5  |
      | total_refunded_tax_incl     | 3.71 |
    And there is 1 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock

  @order-refund
  @order-partial-refund
  Scenario: Partial refund of products after a product has changed its tax rate
    Given I add order "bo_order_refund" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    And product "product_mug_best_to_come" named "Mug The best is yet to come" in order bo_order_refund has following details:
      | product_quantity            | 2 |
    And product "product_mug_good_day" named "Mug Today is a good day" in order "bo_order_refund" has following details:
      | product_quantity            | 1 |
    And there are 2 less "Mug The best is yet to come" in stock
    And there is 1 less "Mug Today is a good day" in stock
    And I add new tax "state-tax" with following properties:
      | name         | State tax |
      | rate         | 10        |
      | is_enabled   | true      |
    And I add the tax rule group "state-tax-group" for the tax "state-tax" with the following conditions:
      | name         | US-FL Rate (10%) |
      | country      | US               |
      | state        | FL               |
    And I update product product_mug_best_to_come prices with following information:
      | tax rules group | US-FL Rate (10%) |
    When I issue a partial refund on "bo_order_refund" without restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 11.9   |
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
      | product_quantity_refunded   | 1      |
      | product_quantity_return     | 0      |
      | product_quantity_reinjected | 1      |
      | total_refunded_tax_excl     | 11.9   |
      | total_refunded_tax_incl     | 12.610000 |
    And there is 1 more "Mug The best is yet to come" in stock
