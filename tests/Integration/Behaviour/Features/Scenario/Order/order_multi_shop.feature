# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-multi-shop
@reset-database-before-feature
@clear-cache-before-feature
@order-multi-shop
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO even when it has multi shops

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    # These tests were initially created to ensure the quantity can be updated even in multishop all context,
    # because StockManagementRepository cannot work in a multi shop context, but it is required to create a new
    # product So we force single shop context create the product, then we need to reboot the kernel so that
    # StockManagementRepository is created again in the following steps of the scenario
    And single shop context is loaded
    And there is a product in the catalog named "Test Added Product" with a price of 15.0 and 100 items in stock
    And I reboot kernel
    And multiple shop context is loaded

  Scenario: Update product in order
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | 3                       |
      | price         | 11.90                   |
    Then order "bo_order1" should contain 3 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" should have no specific price
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 3      |
      | product_price               | 11.90  |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.90  |
      | total_price_tax_incl        | 37.84  |
      | total_price_tax_excl        | 35.70  |
    Then order "bo_order1" should have following details:
      | total_products           | 35.700 |
      | total_products_wt        | 37.840 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 42.7   |
      | total_paid_tax_incl      | 45.260 |
      | total_paid               | 45.260 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product to an existing Order without invoice without free shipping and new invoice
    And order with reference "bo_order1" does not contain product "Test Added Product"
    And the available stock for product "Test Added Product" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Added Product |
      | amount        | 2                  |
      | price         | 16                 |
    Then order "bo_order1" should contain 2 products "Test Added Product"
    And product "Test Added Product" in order "bo_order1" should have specific price 16.0
    And the available stock for product "Test Added Product" should be 98
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Added Product" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 16.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_excl         | 16.00 |
      | unit_price_tax_incl         | 16.96 |
      | total_price_tax_excl        | 32.00 |
      | total_price_tax_incl        | 33.92 |
    And order "bo_order1" should have following details:
      | total_products           | 55.80 |
      | total_products_wt        | 59.15 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 62.80 |
      | total_paid_tax_incl      | 66.57 |
      | total_paid               | 66.57 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |

  Scenario: Delete product from order
    Given order with reference "bo_order1" does not contain product "Test Added Product"
    And the available stock for product "Test Added Product" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Added Product |
      | amount        | 2                  |
      | price         | 16                 |
    Then order "bo_order1" should contain 2 products "Test Added Product"
    And product "Test Added Product" in order "bo_order1" should have specific price 16.0
    And the available stock for product "Test Added Product" should be 98
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Added Product" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 16.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_excl         | 16.00 |
      | unit_price_tax_incl         | 16.96 |
      | total_price_tax_excl        | 32.00 |
      | total_price_tax_incl        | 33.92 |
    And order "bo_order1" should have following details:
      | total_products           | 55.80 |
      | total_products_wt        | 59.15 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 62.80 |
      | total_paid_tax_incl      | 66.57 |
      | total_paid               | 66.57 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
    When I remove product "Test Added Product" from order "bo_order1"
    Then product "Test Added Product" in order "bo_order1" should have no specific price
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should contain 0 product "Test Added Product"
    And cart of order "bo_order1" should contain 0 product "Test Added Product"
    And the available stock for product "Test Added Product" should be 100
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
