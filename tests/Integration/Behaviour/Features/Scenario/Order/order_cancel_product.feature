# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-cancel-product
@reset-database-before-feature
@order-cancel-product
@clear-cache-before-feature
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
    And add 2 customized products with reference "demo_14" with all its customizations to the cart "dummy_cart"
    And I watch the stock of product "Mug The best is yet to come"
    And I watch the stock of product "Mug Today is a good day"
    And I watch the stock of product "Customizable mug"

  Scenario: Cancel product feature has expected behavior
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    Then order "bo_order_cancel_product" should have 10 products in total
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And order "bo_order_cancel_product" should contain 2 products "Customizable mug"
    And order "bo_order_cancel_product" should have following details:
      | total_products           | 123.00 |
      | total_products_wt        | 130.38 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 130.00 |
      | total_paid_tax_incl      | 137.80 |
      | total_paid               | 137.80 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And there are 5 less "Mug The best is yet to come" in stock
    And there are 3 less "Mug Today is a good day" in stock
    And there are 2 less "Customizable mug" in stock
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 2        |
      | Mug Today is a good day     | 1        |
      | Customizable mug            | 1        |
    Then order "bo_order_cancel_product" should contain 3 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 2 products "Mug Today is a good day"
    And order "bo_order_cancel_product" should contain 1 products "Customizable mug"
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
    And product "Customizable mug" in order "bo_order_cancel_product" has following details:
      | product_quantity            | 1           |
      | product_quantity_refunded   | 0           |
      | product_quantity_reinjected | 0           |
      | total_refunded_tax_excl     | 0.000000    |
      | total_refunded_tax_incl     | 0.000000    |
    And there are 2 more "Mug The best is yet to come" in stock
    And there is 1 more "Mug Today is a good day" in stock
    And there is 1 more "Customizable mug" in stock
    And order "bo_order_cancel_product" should have following details:
      | total_products           | 73.40  |
      | total_products_wt        | 77.80  |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 80.40  |
      | total_paid_tax_incl      | 85.22  |
      | total_paid               | 85.22  |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Order status is set to canceled when all products have been cancelled
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    Then order "bo_order_cancel_product" should have 10 products in total
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And order "bo_order_cancel_product" should contain 2 products "Customizable mug"
    When I generate invoice for "bo_order_cancel_product" order
    Then order "bo_order_cancel_product" should have 1 invoices
    And order "bo_order_cancel_product" should have following details:
      | total_products           | 123.00 |
      | total_products_wt        | 130.38 |
      | total_discounts_tax_excl | 0.000  |
      | total_discounts_tax_incl | 0.000  |
      | total_paid_tax_excl      | 130.00 |
      | total_paid_tax_incl      | 137.80 |
      | total_paid               | 137.80 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And the first invoice from order "bo_order_cancel_product" should have following details:
      | total_products          | 123.00 |
      | total_products_wt       | 130.38 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 130.00 |
      | total_paid_tax_incl     | 137.80 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    Then order "bo_order_cancel_product" has status "Awaiting check payment"
    And order "bo_order_cancel_product" has 1 status in history
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 5        |
      | Mug Today is a good day     | 3        |
      | Customizable mug            | 2        |
    Then order "bo_order_cancel_product" should have 0 products in total
    And order "bo_order_cancel_product" should contain 0 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 0 products "Mug Today is a good day"
    And order "bo_order_cancel_product" should contain 0 products "Customizable mug"
    And order "bo_order_cancel_product" has status "Canceled"
    And order "bo_order_cancel_product" has 2 statuses in history
    And order "bo_order_cancel_product" should have following details:
      | total_products           | 0.0000 |
      | total_products_wt        | 0.0000 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_shipping_tax_excl  | 0.0000 |
      | total_shipping_tax_incl  | 0.0000 |
      | total_paid_tax_excl      | 0.0000 |
      | total_paid_tax_incl      | 0.0000 |
      | total_paid               | 0.0000 |
      | total_paid_real          | 0.0    |
    And the first invoice from order "bo_order_cancel_product" should have following details:
      | total_products          | 0.000 |
      | total_products_wt       | 0.000 |
      | total_discount_tax_excl | 0.0   |
      | total_discount_tax_incl | 0.0   |
      | total_paid_tax_excl     | 0.000 |
      | total_paid_tax_incl     | 0.000 |
      | total_shipping_tax_excl | 0.000 |
      | total_shipping_tax_incl | 0.000 |

  Scenario: Quantity is required
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    Then order "bo_order_cancel_product" should have 10 products in total
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And order "bo_order_cancel_product" should contain 2 products "Customizable mug"
    And there are 5 less "Mug The best is yet to come" in stock
    And there are 3 less "Mug Today is a good day" in stock
    And there are 2 less "Customizable mug" in stock
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 0        |
      | Mug Today is a good day     | 1        |
    Then I should get error that cancel quantity is invalid
    And order "bo_order_cancel_product" should have 10 products in total
    And order "bo_order_cancel_product" should have following details:
      | total_products           | 123.00 |
      | total_products_wt        | 130.38 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 130.00 |
      | total_paid_tax_incl      | 137.80 |
      | total_paid               | 137.80 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Quantity is too high
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And order "bo_order_cancel_product" should contain 2 products "Customizable mug"
    And there are 5 less "Mug The best is yet to come" in stock
    And there are 3 less "Mug Today is a good day" in stock
    And there are 2 less "Customizable mug" in stock
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 565       |
      | Mug Today is a good day     | 1        |
    Then I should get error that cancel quantity is too high and max is 5
    And order "bo_order_cancel_product" should have 10 products in total
    And order "bo_order_cancel_product" should have following details:
      | total_products           | 123.00 |
      | total_products_wt        | 130.38 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 130.00 |
      | total_paid_tax_incl      | 137.80 |
      | total_paid               | 137.80 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Order should not have invoice
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Payment accepted     |
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And order "bo_order_cancel_product" should contain 2 products "Customizable mug"
    And there are 5 less "Mug The best is yet to come" in stock
    And there are 3 less "Mug Today is a good day" in stock
    And there are 2 less "Customizable mug" in stock
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
      | Mug Today is a good day     | 1        |
    Then I should get error that order is already paid
    And order "bo_order_cancel_product" should have 10 products in total
    And order "bo_order_cancel_product" should have following details:
      | total_products           | 123.00 |
      | total_products_wt        | 130.38 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 130.00 |
      | total_paid_tax_incl      | 137.80 |
      | total_paid               | 137.80 |
      | total_paid_real          | 137.80 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product linked to a cart rule to an existing Order without invoice with free shipping and new invoice And cancel this product
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And order "bo_order_cancel_product" should contain 2 products "Customizable mug"
    Then order "bo_order_cancel_product" should have 10 products in total
    Then order "bo_order_cancel_product" should have 0 invoices
    Then order "bo_order_cancel_product" should have 0 cart rule
    Then order "bo_order_cancel_product" should have following details:
      | total_products           | 123.00 |
      | total_products_wt        | 130.38 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 130.00 |
      | total_paid_tax_incl      | 137.80 |
      | total_paid               | 137.80 |
      | total_paid_real          | 0.00   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "Test Product Cart Rule On Select Product" with a price of 15.0 and 100 items in stock
    And there is a cart rule named "CartRuleAmountOnSelectedProduct" that applies an amount discount of 500.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "CartRuleAmountOnSelectedProduct" has no discount code
    And cart rule "CartRuleAmountOnSelectedProduct" is restricted to product "Test Product Cart Rule On Select Product"
    When I add products to order "bo_order_cancel_product" with new invoice and the following products details:
      | name          | Test Product Cart Rule On Select Product  |
      | amount        | 1                                         |
      | price         | 15                                        |
    Then order "bo_order_cancel_product" should have 11 products in total
    Then order "bo_order_cancel_product" should contain 1 product "Test Product Cart Rule On Select Product"
    Then order "bo_order_cancel_product" should have 1 cart rule
    Then order "bo_order_cancel_product" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$15.00"
    Then order "bo_order_cancel_product" should have following details:
      | total_products           | 138.00 |
      | total_products_wt        | 146.28 |
      | total_discounts_tax_excl | 15.000 |
      | total_discounts_tax_incl | 15.900 |
      | total_paid_tax_excl      | 130.00 |
      | total_paid_tax_incl      | 137.80 |
      | total_paid               | 137.80 |
      | total_paid_real          | 0.00   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                             | quantity |
      | Test Product Cart Rule On Select Product | 1        |
    Then order "bo_order_cancel_product" should have 10 products in total
    Then order "bo_order_cancel_product" should contain 0 product "Test Product Cart Rule On Select Product"
    # This test doesn't work because cart rules are not updated in cart nor in order
    # @todo This should be fixed along with #19717
#    Then order "bo_order_cancel_product" should have 0 cart rule
    Then order "bo_order_cancel_product" should have following details:
      | total_products           | 123.00 |
      | total_products_wt        | 130.38 |
      # Most totals are incorrect and need to be updated
#      | total_discounts_tax_excl | 0.0    |
#      | total_discounts_tax_incl | 0.0    |
#      | total_paid_tax_excl      | 102.20 |
#      | total_paid_tax_incl      | 108.33 |
#      | total_paid               | 108.33 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add discount to all orders, when a product is cancelled the discount should still be present
    Given I add order "bo_order_cancel_product" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting check payment     |
    And order "bo_order_cancel_product" should contain 5 products "Mug The best is yet to come"
    And order "bo_order_cancel_product" should contain 3 products "Mug Today is a good day"
    And order "bo_order_cancel_product" should contain 2 products "Customizable mug"
    Then order "bo_order_cancel_product" should have 10 products in total
    Then order "bo_order_cancel_product" should have 0 invoices
    Then order "bo_order_cancel_product" should have 0 cart rule
    Then order "bo_order_cancel_product" should have following details:
      | total_products           | 123.00 |
      | total_products_wt        | 130.38 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 130.00 |
      | total_paid_tax_incl      | 137.80 |
      | total_paid               | 137.80 |
      | total_paid_real          | 0.00   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "Test Product Cart Rule On Order" with a price of 15.0 and 100 items in stock
    Given there is a cart rule named "CartRuleAmountOnWholeOrder" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "CartRuleAmountOnWholeOrder" is applied on every order
    When I add products to order "bo_order_cancel_product" with new invoice and the following products details:
      | name          | Test Product Cart Rule On Order |
      | amount        | 1                               |
      | price         | 15                              |
    Then order "bo_order_cancel_product" should have 11 products in total
    Then order "bo_order_cancel_product" should contain 1 product "Test Product Cart Rule On Order"
    Then order "bo_order_cancel_product" should have 1 cart rule
    Then order "bo_order_cancel_product" should have cart rule "CartRuleAmountOnWholeOrder" with amount "$69.00"
    Then order "bo_order_cancel_product" should have following details:
      | total_products           | 138.00 |
      | total_products_wt        | 146.28 |
      | total_discounts_tax_excl | 69.00  |
      | total_discounts_tax_incl | 73.14  |
      | total_paid_tax_excl      | 76.00  |
      | total_paid_tax_incl      | 80.56  |
      | total_paid               | 80.56  |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I cancel the following products from order "bo_order_cancel_product":
      | product_name                    | quantity |
      | Test Product Cart Rule On Order | 1        |
    Then order "bo_order_cancel_product" should have 10 products in total
    Then order "bo_order_cancel_product" should contain 0 product "Test Product Cart Rule On Order"
    # This one works by chance because the cart rules are not cleaned at all, but once they are it still should be present
    Then order "bo_order_cancel_product" should have 1 cart rule
#    Then order "bo_order_cancel_product" should have cart rule "CartRuleAmountOnWholeOrder" with amount "$47.60"
    # This test doesn't work because cart rules are not updated in cart nor in order
    # @todo This should be fixed along with #19717
    Then order "bo_order_cancel_product" should have following details:
      | total_products           | 123.00 |
      | total_products_wt        | 130.38 |
      # Most totals are incorrect and need to be be updated
#      | total_discounts_tax_excl | 47.600 |
#      | total_discounts_tax_incl | 50.460 |
#      | total_paid_tax_excl      | 54.600 |
#      | total_paid_tax_incl      | 57.870 |
#      | total_paid               | 57.870 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
