# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-multi-invoices
@reset-database-before-feature
@order-multi-invoices
@clear-cache-before-feature
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

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
    And there is a product in the catalog named "Test Product A" with a price of 15.0 and 100 items in stock
    And there is a product in the catalog named "Test Product B" with a price of 10.0 and 100 items in stock
    Then the available stock for product "Test Product A" should be 100
    And the available stock for product "Test Product B" should be 100

  Scenario: I add products twice in three different invoices and check that invoice totals and order totals are logical
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoices
    When I add products to order "bo_order1" to first invoice and the following products details:
      | name          | Test Product A |
      | amount        | 3              |
      | price         | 15             |
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product A |
      | amount        | 2              |
      | price         | 15             |
    And I add products to order "bo_order1" to second invoice and the following products details:
      | name          | Test Product B |
      | amount        | 1              |
      | price         | 10             |
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product B |
      | amount        | 5              |
      | price         | 10             |
    Then order "bo_order1" should have 3 invoices
    And order "bo_order1" should have 13 products in total
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And order "bo_order1" should contain 5 products "Test Product A"
    And order "bo_order1" should contain 6 products "Test Product B"
    And the available stock for product "Test Product A" should be 95
    And the available stock for product "Test Product B" should be 94
    And first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And first invoice from order "bo_order1" should contain 3 products "Test Product A"
    And first invoice from order "bo_order1" should have following details:
      | total_products          | 68.800 |
      | total_products_wt       | 72.930 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 75.80  |
      | total_paid_tax_incl     | 80.35  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And second invoice from order "bo_order1" should contain 2 products "Test Product A"
    And second invoice from order "bo_order1" should contain 1 products "Test Product B"
    And second invoice from order "bo_order1" should have following details:
      | total_products          | 40.000 |
      | total_products_wt       | 42.400 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 47.00  |
      | total_paid_tax_incl     | 49.82  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And third invoice from order "bo_order1" should contain 5 products "Test Product B"
    And third invoice from order "bo_order1" should have following details:
      | total_products          | 50.000 |
      | total_products_wt       | 53.000 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 57.00  |
      | total_paid_tax_incl     | 60.42  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 158.80 |
      | total_products_wt        | 168.33 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 179.80 |
      | total_paid_tax_incl      | 190.59 |
      | total_paid               | 190.59 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 21.0   |
      | total_shipping_tax_incl  | 22.26  |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.000 |
      | shipping_cost_tax_excl | 21.00 |
      | shipping_cost_tax_incl | 22.26 |

  Scenario: I add products in two different invoices and apply a discount on only one
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoices
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product A |
      | amount        | 2              |
      | price         | 15             |
    And I add products to order "bo_order1" to second invoice and the following products details:
      | name          | Test Product B |
      | amount        | 1              |
      | price         | 10             |
    Then order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 5 products in total
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And order "bo_order1" should contain 2 products "Test Product A"
    And order "bo_order1" should contain 1 products "Test Product B"
    And the available stock for product "Test Product A" should be 98
    And the available stock for product "Test Product B" should be 99
    And first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 30.800 |
      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And second invoice from order "bo_order1" should contain 2 products "Test Product A"
    And second invoice from order "bo_order1" should contain 1 products "Test Product B"
    And second invoice from order "bo_order1" should have following details:
      | total_products          | 40.000 |
      | total_products_wt       | 42.400 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 47.00  |
      | total_paid_tax_incl     | 49.82  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 63.80 |
      | total_products_wt        | 67.63 |
      | total_discounts_tax_excl | 0.0   |
      | total_discounts_tax_incl | 0.0   |
      | total_paid_tax_excl      | 77.80 |
      | total_paid_tax_incl      | 82.47 |
      | total_paid               | 82.47 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 14.0  |
      | total_shipping_tax_incl  | 14.84 |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.000 |
      | shipping_cost_tax_excl | 14.00 |
      | shipping_cost_tax_incl | 14.84 |
    When I add discount to order "bo_order1" on second invoice and following details:
      | name      | discount amount |
      | type      | amount          |
      | value     | 5.50            |
    Then first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 30.800 |
      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And second invoice from order "bo_order1" should have following details:
      | total_products          | 40.000 |
      | total_products_wt       | 42.400 |
      | total_discount_tax_excl | 5.19   |
      | total_discount_tax_incl | 5.50   |
      | total_paid_tax_excl     | 41.81  |
      | total_paid_tax_incl     | 44.32  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 63.80 |
      | total_products_wt        | 67.63 |
      | total_discounts_tax_excl | 5.19  |
      | total_discounts_tax_incl | 5.50  |
      | total_paid_tax_excl      | 72.61 |
      | total_paid_tax_incl      | 76.97 |
      | total_paid               | 76.97 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 14.0  |
      | total_shipping_tax_incl  | 14.84 |

  Scenario: I add products in two different invoices and apply percent discount on one invoice
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoices
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product A |
      | amount        | 2              |
      | price         | 15             |
    And I add products to order "bo_order1" to second invoice and the following products details:
      | name          | Test Product B |
      | amount        | 3              |
      | price         | 10             |
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product B |
      | amount        | 5              |
      | price         | 10             |
    Then order "bo_order1" should have 3 invoices
    And order "bo_order1" should have 12 products in total
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And order "bo_order1" should contain 2 products "Test Product A"
    And order "bo_order1" should contain 8 products "Test Product B"
    And the available stock for product "Test Product A" should be 98
    And the available stock for product "Test Product B" should be 92
    And first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 30.800 |
      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And second invoice from order "bo_order1" should contain 2 products "Test Product A"
    And second invoice from order "bo_order1" should contain 3 products "Test Product B"
    And second invoice from order "bo_order1" should have following details:
      | total_products          | 60.000 |
      | total_products_wt       | 63.600 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 67.00  |
      | total_paid_tax_incl     | 71.02  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And third invoice from order "bo_order1" should contain 5 products "Test Product B"
    And third invoice from order "bo_order1" should have following details:
      | total_products          | 50.000 |
      | total_products_wt       | 53.000 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 57.00  |
      | total_paid_tax_incl     | 60.42  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 133.80 |
      | total_products_wt        | 141.83 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 154.80 |
      | total_paid_tax_incl      | 164.09 |
      | total_paid               | 164.09 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 21.0   |
      | total_shipping_tax_incl  | 22.26  |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.000 |
      | shipping_cost_tax_excl | 21.00 |
      | shipping_cost_tax_incl | 22.26 |
    When I add discount to order "bo_order1" on third invoice and following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    When I add discount to order "bo_order1" on second invoice and following details:
      | name      | discount amount |
      | type      | amount          |
      | value     | 5.50            |
    Then first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 30.800 |
      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And second invoice from order "bo_order1" should have following details:
      | total_products          | 60.000 |
      | total_products_wt       | 63.600 |
      | total_discount_tax_excl | 5.19   |
      | total_discount_tax_incl | 5.50   |
      | total_paid_tax_excl     | 61.81  |
      | total_paid_tax_incl     | 65.52  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And third invoice from order "bo_order1" should have following details:
      | total_products          | 50.000 |
      | total_products_wt       | 53.000 |
      | total_discount_tax_excl | 25.00  |
      | total_discount_tax_incl | 26.50  |
      | total_paid_tax_excl     | 32.00  |
      | total_paid_tax_incl     | 33.92  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 133.80 |
      | total_products_wt        | 141.83 |
      | total_discounts_tax_excl | 30.19  |
      | total_discounts_tax_incl | 32.00  |
      | total_paid_tax_excl      | 124.61 |
      | total_paid_tax_incl      | 132.09 |
      | total_paid               | 132.09 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 21.0   |
      | total_shipping_tax_incl  | 22.26  |
