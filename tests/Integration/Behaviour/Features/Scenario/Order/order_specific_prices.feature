# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-specific-prices
@restore-all-tables-before-feature
@order-specific-prices
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to edit specific prices on my Order

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
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Add product with regular price, add it again with different specific price The first price is updated
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
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
    Given there is a product in the catalog named "Test Product With Specific Price" with a price of 15.0 and 100 items in stock
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 15                                |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 45.8   |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Then product "Test Product With Specific Price" in order "bo_order1" should have no specific price
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 12                                |
    Then the product "Test Product With Specific Price" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have following details:
      | total_products           | 47.800 |
      | total_products_wt        | 50.670 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 54.800 |
      | total_paid_tax_incl      | 58.090 |
      | total_paid               | 58.090 |
      | total_paid_real          | 48.550 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product with specific price, add it again with regular price The first price is updated and specific price is removed
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
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
    Given there is a product in the catalog named "Test Product With Specific Price" with a price of 15.0 and 100 items in stock
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 12                                |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should have following details:
      | total_products           | 35.800 |
      | total_products_wt        | 37.950 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 42.8   |
      | total_paid_tax_incl      | 45.370 |
      | total_paid               | 45.370 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Given I update order "bo_order1" status to "Payment accepted"
    Then the product "Test Product With Specific Price" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have 1 invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 15                                |
    Then product "Test Product With Specific Price" in order "bo_order1" should have no specific price
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 60.800 |
      | total_paid_tax_incl      | 64.450 |
      | total_paid               | 64.450 |
      | total_paid_real          | 45.370 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add twice products product with specific price, when one is edited the other is updated as well
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
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
    Given there is a product in the catalog named "Test Product With Specific Price" with a price of 15.0 and 100 items in stock
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 12                                |
    Given I update order "bo_order1" status to "Payment accepted"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 15                                |
    Then product "Test Product With Specific Price" in order "bo_order1" should have no specific price
    And order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 60.800 |
      | total_paid_tax_incl      | 64.450 |
      | total_paid               | 64.450 |
      | total_paid_real          | 45.370 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I edit product "Test Product With Specific Price" to order "bo_order1" with following products details:
      | amount        | 1                     |
      | price         | 16                    |
    Then the product "Test Product With Specific Price" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 16.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 16.96 |
      | unit_price_tax_excl         | 16.00 |
      | total_price_tax_incl        | 16.96 |
      | total_price_tax_excl        | 16.00 |
    And the product "Test Product With Specific Price" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 16.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 16.96 |
      | unit_price_tax_excl         | 16.00 |
      | total_price_tax_incl        | 16.96 |
      | total_price_tax_excl        | 16.00 |
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have following details:
      | total_products           | 55.800 |
      | total_products_wt        | 59.150 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 62.800 |
      | total_paid_tax_incl      | 66.570 |
      | total_paid               | 66.570 |
      | total_paid_real          | 45.370 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I edit product "Test Product With Specific Price" to order "bo_order1" with following products details:
      | amount        | 1                     |
      | price         | 15                    |
    Then product "Test Product With Specific Price" in order "bo_order1" should have no specific price
    Then the product "Test Product With Specific Price" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the product "Test Product With Specific Price" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 60.800 |
      | total_paid_tax_incl      | 64.450 |
      | total_paid               | 64.450 |
      | total_paid_real          | 45.370 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I edit product "Test Product With Specific Price" in second invoice from order "bo_order1" with following products details:
      | amount        | 1                     |
      | price         | 12                    |
    Then product "Test Product With Specific Price" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have following details:
      | total_products           | 47.800 |
      | total_products_wt        | 50.670 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 54.800 |
      | total_paid_tax_incl      | 58.090 |
      | total_paid               | 58.090 |
      | total_paid_real          | 45.370 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product with specific price, add then remove it The specific price should be removed
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
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
    Given there is a product in the catalog named "Test Product With Specific Price" with a price of 15.0 and 100 items in stock
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 12                                |
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should contain 1 product "Test Product With Specific Price"
    And cart of order "bo_order1" should contain 1 product "Test Product With Specific Price"
    Then order "bo_order1" should have following details:
      | total_products           | 35.800 |
      | total_products_wt        | 37.950 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 42.8   |
      | total_paid_tax_incl      | 45.370 |
      | total_paid               | 45.370 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Then product "Test Product With Specific Price" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    When I remove product "Test Product With Specific Price" from order "bo_order1"
    Then product "Test Product With Specific Price" in order "bo_order1" should have no specific price
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should contain 0 product "Test Product With Specific Price"
    And cart of order "bo_order1" should contain 0 product "Test Product With Specific Price"
    Then order "bo_order1" should have following details:
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

  Scenario: Add product with specific price twice, the specific price is only removed when both products are removed
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
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
    Given there is a product in the catalog named "Test Product With Specific Price" with a price of 15.0 and 100 items in stock
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 3                                 |
      | price         | 12                                |
    Then order "bo_order1" should have 5 products in total
    And order "bo_order1" should contain 3 products "Test Product With Specific Price"
    And cart of order "bo_order1" should contain 3 products "Test Product With Specific Price"
    And the available stock for product "Test Product With Specific Price" should be 97
    Then product "Test Product With Specific Price" in order "bo_order1" has following details:
      | product_quantity            | 3     |
      | product_price               | 12.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 38.16 |
      | total_price_tax_excl        | 36.00 |
    Then order "bo_order1" should have following details:
      | total_products           | 59.800 |
      | total_products_wt        | 63.390 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 66.8   |
      | total_paid_tax_incl      | 70.810 |
      | total_paid               | 70.810 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 2                                 |
      | price         | 14                                |
    Then the product "Test Product With Specific Price" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 3     |
      | product_price               | 14.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 14.84 |
      | unit_price_tax_excl         | 14.00 |
      | total_price_tax_incl        | 44.52 |
      | total_price_tax_excl        | 42.00 |
    And the product "Test Product With Specific Price" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2     |
      | product_price               | 14.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 14.84 |
      | unit_price_tax_excl         | 14.00 |
      | total_price_tax_incl        | 29.68 |
      | total_price_tax_excl        | 28.00 |
    And order "bo_order1" should have 7 products in total
    And order "bo_order1" should contain 5 products "Test Product With Specific Price"
    And cart of order "bo_order1" should contain 5 products "Test Product With Specific Price"
    And the available stock for product "Test Product With Specific Price" should be 95
    Then order "bo_order1" should have following details:
      | total_products           | 93.800 |
      | total_products_wt        | 99.430 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 100.80 |
      | total_paid_tax_incl      | 106.85 |
      | total_paid               | 106.85 |
      | total_paid_real          | 70.810 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove product "Test Product With Specific Price" from order "bo_order1"
    Then the first invoice from order "bo_order1" should contain 0 product "Test Product With Specific Price"
    And the product "Test Product With Specific Price" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2     |
      | product_price               | 14.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_incl         | 14.84 |
      | unit_price_tax_excl         | 14.00 |
      | total_price_tax_incl        | 29.68 |
      | total_price_tax_excl        | 28.00 |
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should contain 2 products "Test Product With Specific Price"
    And cart of order "bo_order1" should contain 2 products "Test Product With Specific Price"
    And the available stock for product "Test Product With Specific Price" should be 98
    Then order "bo_order1" should have following details:
      | total_products           | 51.800 |
      | total_products_wt        | 54.910 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 58.800 |
      | total_paid_tax_incl      | 62.330 |
      | total_paid               | 62.330 |
      | total_paid_real          | 70.810 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove product "Test Product With Specific Price" from order "bo_order1"
    Then product "Test Product With Specific Price" in order "bo_order1" should have no specific price
    And the second invoice from order "bo_order1" should contain 0 product "Test Product With Specific Price"
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should contain 0 product "Test Product With Specific Price"
    And cart of order "bo_order1" should contain 0 product "Test Product With Specific Price"
    And the available stock for product "Test Product With Specific Price" should be 100
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 70.810 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product which has specific price rules for a discount, when we change its price in the order it doesn't affect the existing price rule
    Given order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
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
    Given there is a product in the catalog named "Test Product With Percentage Discount" with a price of 16.0 and 100 items in stock
    And product "Test Product With Percentage Discount" has a specific price named "discount20" with a discount of 25.0 percent
    And product "Test Product With Percentage Discount" should have specific price "discount20" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Percentage Discount |
      | amount        | 1                                     |
      | price         | 12                                    |
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should contain 1 products "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 1 products "Test Product With Percentage Discount"
    And the available stock for product "Test Product With Percentage Discount" should be 99
    And product "Test Product With Percentage Discount" should have specific price "discount20" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The edited price matches the price with discount applied so it's not a specific price for this order it follows the general rules
    And product "Test Product With Percentage Discount" in order "bo_order1" should have no specific price
    And order "bo_order1" should have following details:
      | total_products           | 35.800 |
      | total_products_wt        | 37.950 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 42.8   |
      | total_paid_tax_incl      | 45.370 |
      | total_paid               | 45.370 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    # When the product is removed assert we don't remove the global SpecificPrice either
    When I remove product "Test Product With Percentage Discount" from order "bo_order1"
    Then product "Test Product With Percentage Discount" in order "bo_order1" should have no specific price
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should contain 0 product "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 0 product "Test Product With Percentage Discount"
    Then order "bo_order1" should have following details:
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
    # Check that global SpecificPrice has been removed along with the product
    And product "Test Product With Percentage Discount" should have specific price "discount20" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |

  Scenario: Add product which has specific price rules for a discount but keeps catalog price, then the order has its own specific price
    Given order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
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
    Given there is a product in the catalog named "Test Product With Percentage Discount" with a price of 16.0 and 100 items in stock
    And product "Test Product With Percentage Discount" has a specific price named "discount20" with a discount of 25.0 percent
    And product "Test Product With Percentage Discount" should have specific price "discount20" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Percentage Discount |
      | amount        | 1                                     |
      | price         | 16                                    |
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should contain 1 products "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 1 products "Test Product With Percentage Discount"
    And the available stock for product "Test Product With Percentage Discount" should be 99
    And product "Test Product With Percentage Discount" should have specific price "discount20" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is the price without the discount so it is a specific price
    Then product "Test Product With Percentage Discount" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 16.00 |
      | original_product_price      | 16.00 |
      | unit_price_tax_incl         | 16.96 |
      | unit_price_tax_excl         | 16.00 |
      | total_price_tax_incl        | 16.96 |
      | total_price_tax_excl        | 16.00 |
    And order "bo_order1" should have following details:
      | total_products           | 39.800 |
      | total_products_wt        | 42.190 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 46.8   |
      | total_paid_tax_incl      | 49.610 |
      | total_paid               | 49.610 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    # When the product is removed assert we don't remove the global SpecificPrice either
    When I remove product "Test Product With Percentage Discount" from order "bo_order1"
    Then product "Test Product With Percentage Discount" in order "bo_order1" should have no specific price
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should contain 0 product "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 0 product "Test Product With Percentage Discount"
    Then order "bo_order1" should have following details:
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
    And product "Test Product With Percentage Discount" should have specific price "discount20" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |

  Scenario: I create a cart with a specific price, it should be present when order is created and stays present even if product price is modified
    And I create an empty cart "customized_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "customized_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "customized_cart"
    And I update product "Mug The best is yet to come" in the cart "customized_cart" to 15.00
    Then product "Mug The best is yet to come" in cart "customized_cart" should have specific price 15.00
    And I add order "bo_order1" with the following details:
      | cart                | customized_cart            |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    # When Order is added all the specific prices associated to the cart are deleted
    Then product "Mug The best is yet to come" in order "bo_order1" should have no specific price
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | original_product_price      | 11.90 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And order "bo_order1" should have following details:
      | total_products           | 30.000 |
      | total_products_wt        | 31.800 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 37.000 |
      | total_paid_tax_incl      | 39.220 |
      | total_paid               | 39.220 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
