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
    Then order "bo_order1" should have 1 invoice
    When I add products to order "bo_order1" to the first invoice and the following products details:
      | name          | Test Product A |
      | amount        | 3              |
      | price         | 15             |
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product A |
      | amount        | 2              |
      | price         | 15             |
    And I add products to order "bo_order1" to the second invoice and the following products details:
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
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should contain 3 products "Test Product A"
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 68.800 |
      | total_products_wt       | 72.930 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 75.80  |
      | total_paid_tax_incl     | 80.35  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should contain 2 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 1 products "Test Product B"
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 40.000 |
      | total_products_wt       | 42.400 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 47.00  |
#      | total_paid_tax_incl     | 49.82  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And the third invoice from order "bo_order1" should contain 5 products "Test Product B"
    And the third invoice from order "bo_order1" should have following details:
      | total_products          | 50.000 |
      | total_products_wt       | 53.000 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 57.00  |
#      | total_paid_tax_incl     | 60.42  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 158.80 |
      | total_products_wt        | 168.33 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
#      | total_paid_tax_excl      | 179.80 |
#      | total_paid_tax_incl      | 190.59 |
#      | total_paid               | 190.59 |
      | total_paid_real          | 0.0    |
#      | total_shipping_tax_excl  | 21.0   |
#      | total_shipping_tax_incl  | 22.26  |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
#      | shipping_cost_tax_excl | 21.00 |
#      | shipping_cost_tax_incl | 22.26 |

  Scenario: I add products in two different invoices and apply a discount on only one
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoice
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product A |
      | amount        | 2              |
      | price         | 15             |
    And I add products to order "bo_order1" to the second invoice and the following products details:
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
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 30.800 |
      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should contain 2 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 1 products "Test Product B"
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 40.000 |
      | total_products_wt       | 42.400 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 47.00  |
#      | total_paid_tax_incl     | 49.82  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 63.80 |
      | total_products_wt        | 67.63 |
      | total_discounts_tax_excl | 0.0   |
      | total_discounts_tax_incl | 0.0   |
#      | total_paid_tax_excl      | 77.80 |
#      | total_paid_tax_incl      | 82.47 |
#      | total_paid               | 82.47 |
      | total_paid_real          | 0.0   |
#      | total_shipping_tax_excl  | 14.0  |
#      | total_shipping_tax_incl  | 14.84 |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
#      | shipping_cost_tax_excl | 14.00 |
#      | shipping_cost_tax_incl | 14.84 |
    When I add discount to order "bo_order1" on second invoice and following details:
      | name      | discount amount |
      | type      | amount          |
      | value     | 5.50            |
    Then the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
#      | total_discount_tax_excl | 0.0    |
#      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 30.800 |
#      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 40.000 |
      | total_products_wt       | 42.400 |
      | total_discount_tax_excl | 5.19   |
      | total_discount_tax_incl | 5.50   |
#      | total_paid_tax_excl     | 41.81  |
#      | total_paid_tax_incl     | 44.32  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 63.80 |
      | total_products_wt        | 67.63 |
      | total_discounts_tax_excl | 5.19  |
      | total_discounts_tax_incl | 5.50  |
#      | total_paid_tax_excl      | 72.61 |
#      | total_paid_tax_incl      | 76.97 |
#      | total_paid               | 76.97 |
      | total_paid_real          | 0.0   |
#      | total_shipping_tax_excl  | 14.0  |
#      | total_shipping_tax_incl  | 14.84 |

  Scenario: I add products in two different invoices and apply percent discount on one invoice
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoice
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product A |
      | amount        | 2              |
      | price         | 15             |
    And I add products to order "bo_order1" to the second invoice and the following products details:
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
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 30.800 |
      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should contain 2 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 3 products "Test Product B"
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 60.000 |
      | total_products_wt       | 63.600 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 67.00  |
#      | total_paid_tax_incl     | 71.02  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And the third invoice from order "bo_order1" should contain 5 products "Test Product B"
    And the third invoice from order "bo_order1" should have following details:
      | total_products          | 50.000 |
      | total_products_wt       | 53.000 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 57.00  |
#      | total_paid_tax_incl     | 60.42  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 133.80 |
      | total_products_wt        | 141.83 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
#      | total_paid_tax_excl      | 154.80 |
#      | total_paid_tax_incl      | 164.09 |
#      | total_paid               | 164.09 |
      | total_paid_real          | 0.0    |
#      | total_shipping_tax_excl  | 21.0   |
#      | total_shipping_tax_incl  | 22.26  |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
#      | shipping_cost_tax_excl | 21.00 |
#      | shipping_cost_tax_incl | 22.26 |
    When I add discount to order "bo_order1" on third invoice and following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    When I add discount to order "bo_order1" on second invoice and following details:
      | name      | discount amount |
      | type      | amount          |
      | value     | 5.50            |
    Then the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
#      | total_discount_tax_excl | 0.0    |
#      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 30.800 |
#      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 60.000 |
      | total_products_wt       | 63.600 |
#      | total_discount_tax_excl | 5.19   |
#      | total_discount_tax_incl | 5.50   |
#      | total_paid_tax_excl     | 61.81  |
#      | total_paid_tax_incl     | 65.52  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And the third invoice from order "bo_order1" should have following details:
      | total_products          | 50.000 |
      | total_products_wt       | 53.000 |
#      | total_discount_tax_excl | 25.00  |
#      | total_discount_tax_incl | 26.50  |
#      | total_paid_tax_excl     | 32.00  |
#      | total_paid_tax_incl     | 33.92  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 133.80 |
      | total_products_wt        | 141.83 |
#      | total_discounts_tax_excl | 30.19  |
#      | total_discounts_tax_incl | 32.00  |
#      | total_paid_tax_excl      | 124.61 |
#      | total_paid_tax_incl      | 132.09 |
#      | total_paid               | 132.09 |
      | total_paid_real          | 0.0    |
#      | total_shipping_tax_excl  | 21.0   |
#      | total_shipping_tax_incl  | 22.26  |

  Scenario: I add products in two different invoices and apply a shipping discount on only one
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoice
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product A |
      | amount        | 2              |
      | price         | 15             |
    And I add products to order "bo_order1" to the second invoice and the following products details:
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
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 30.800 |
      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should contain 2 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 1 products "Test Product B"
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 40.000 |
      | total_products_wt       | 42.400 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 47.00  |
#      | total_paid_tax_incl     | 49.82  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 63.80 |
      | total_products_wt        | 67.63 |
      | total_discounts_tax_excl | 0.0   |
      | total_discounts_tax_incl | 0.0   |
#      | total_paid_tax_excl      | 77.80 |
#      | total_paid_tax_incl      | 82.47 |
#      | total_paid               | 82.47 |
      | total_paid_real          | 0.0   |
#      | total_shipping_tax_excl  | 14.0  |
#      | total_shipping_tax_incl  | 14.84 |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
#      | shipping_cost_tax_excl | 14.00 |
#      | shipping_cost_tax_incl | 14.84 |
    When I add discount to order "bo_order1" on second invoice and following details:
      | name      | discount shipping |
      | type      | free_shipping     |
    Then the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
#      | total_discount_tax_excl | 0.0    |
#      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 30.800 |
#      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 40.000 |
      | total_products_wt       | 42.400 |
      | total_discount_tax_excl | 7.00   |
      | total_discount_tax_incl | 7.42   |
#      | total_paid_tax_excl     | 40.00  |
#      | total_paid_tax_incl     | 42.40  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 63.80 |
      | total_products_wt        | 67.63 |
      | total_discounts_tax_excl | 7.00  |
      | total_discounts_tax_incl | 7.42  |
#      | total_paid_tax_excl      | 70.80 |
#      | total_paid_tax_incl      | 75.05 |
#      | total_paid               | 75.05 |
      | total_paid_real          | 0.0   |
#      | total_shipping_tax_excl  | 14.0  |
#      | total_shipping_tax_incl  | 14.84 |
    #todo: should the OrderCarrier values count the shipping discount and be only 7.0 ?
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
#      | shipping_cost_tax_excl | 14.00 |
#      | shipping_cost_tax_incl | 14.84 |

  Scenario: I add products in two different invoices, a product has automatic discount when I move it to an order the discount is also moved
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a cart rule named "CartRulePercentForSpecificProduct" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "CartRulePercentForSpecificProduct" is restricted to product "Test Product B"
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoice
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product A |
      | amount        | 2              |
      | price         | 15             |
    And I add products to order "bo_order1" to the second invoice and the following products details:
      | name          | Test Product B |
      | amount        | 3              |
      | price         | 10             |
    Then order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 7 products in total
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And order "bo_order1" should contain 2 products "Test Product A"
    And order "bo_order1" should contain 3 products "Test Product B"
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have cart rule "CartRulePercentForSpecificProduct" with amount "$15.00"
    And the available stock for product "Test Product A" should be 98
    And the available stock for product "Test Product B" should be 97
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 30.800 |
      | total_paid_tax_incl     | 32.650 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should contain 2 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 3 products "Test Product B"
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 60.000 |
      | total_products_wt       | 63.600 |
      | total_discount_tax_excl | 15.0   |
      | total_discount_tax_incl | 15.9   |
#      | total_paid_tax_excl     | 52.00  |
#      | total_paid_tax_incl     | 55.12  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 83.80 |
      | total_products_wt        | 88.83 |
      | total_discounts_tax_excl | 15.0  |
      | total_discounts_tax_incl | 15.9  |
#      | total_paid_tax_excl      | 82.80 |
#      | total_paid_tax_incl      | 87.77 |
#      | total_paid               | 87.77 |
      | total_paid_real          | 0.0   |
#      | total_shipping_tax_excl  | 14.0  |
#      | total_shipping_tax_incl  | 14.84 |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
#      | shipping_cost_tax_excl | 14.00 |
#      | shipping_cost_tax_incl | 14.84 |
    When I edit product "Test Product B" in second invoice from order "bo_order1" with following products details:
      | amount        | 3                       |
      | price         | 10                      |
      | invoice       | first                   |
    Then the available stock for product "Test Product A" should be 98
    And the available stock for product "Test Product B" should be 97
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should contain 3 products "Test Product B"
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 53.800 |
      | total_products_wt       | 57.030 |
      | total_discount_tax_excl | 15.0   |
      | total_discount_tax_incl | 15.9   |
      | total_paid_tax_excl     | 45.800 |
      | total_paid_tax_incl     | 48.550 |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should contain 2 products "Test Product A"
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 30.000 |
      | total_products_wt       | 31.800 |
      | total_discount_tax_excl | 0.00   |
      | total_discount_tax_incl | 0.00   |
#      | total_paid_tax_excl     | 37.00  |
#      | total_paid_tax_incl     | 39.22  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 83.80 |
      | total_products_wt        | 88.83 |
      | total_discounts_tax_excl | 15.0  |
      | total_discounts_tax_incl | 15.9  |
#      | total_paid_tax_excl      | 82.80 |
#      | total_paid_tax_incl      | 87.77 |
#      | total_paid               | 87.77 |
      | total_paid_real          | 0.0   |
#      | total_shipping_tax_excl  | 14.0  |
#      | total_shipping_tax_incl  | 14.84 |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
#      | shipping_cost_tax_excl | 14.00 |
#      | shipping_cost_tax_incl | 14.84 |

  Scenario: I move order detail from invoices to invoices and check that duplicate are not allowed
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoice
    When I add products to order "bo_order1" to the first invoice and the following products details:
      | name          | Test Product A |
      | amount        | 3              |
      | price         | 15             |
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product A |
      | amount        | 2              |
      | price         | 15             |
    And I add products to order "bo_order1" to the second invoice and the following products details:
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
    # This is the same base scenario as the first one, we don't check values here to make it shorter
    When I edit product "Test Product A" in second invoice from order "bo_order1" with following products details:
      | amount        | 2                       |
      | price         | 15                      |
      | invoice       | third                   |
    Then the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should contain 3 products "Test Product A"
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 68.800 |
      | total_products_wt       | 72.930 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 75.80  |
      | total_paid_tax_incl     | 80.35  |
      | total_shipping_tax_excl | 7.0    |
      | total_shipping_tax_incl | 7.42   |
    And the second invoice from order "bo_order1" should contain 0 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 1 products "Test Product B"
    And the second invoice from order "bo_order1" should have following details:
      | total_products          | 10.000 |
      | total_products_wt       | 10.600 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 17.00  |
#      | total_paid_tax_incl     | 18.02  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And the third invoice from order "bo_order1" should contain 2 products "Test Product A"
    And the third invoice from order "bo_order1" should contain 5 products "Test Product B"
    And the third invoice from order "bo_order1" should have following details:
      | total_products          | 80.000 |
      | total_products_wt       | 84.800 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 87.00  |
#      | total_paid_tax_incl     | 92.22  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
    And order "bo_order1" should have following details:
      | total_products           | 158.80 |
      | total_products_wt        | 168.33 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
#      | total_paid_tax_excl      | 179.80 |
#      | total_paid_tax_incl      | 190.59 |
#      | total_paid               | 190.59 |
      | total_paid_real          | 0.0    |
#      | total_shipping_tax_excl  | 21.0   |
#      | total_shipping_tax_incl  | 22.26  |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
#      | shipping_cost_tax_excl | 21.00 |
#      | shipping_cost_tax_incl | 22.26 |
    When I edit product "Test Product A" in third invoice from order "bo_order1" with following products details:
      | amount        | 2                       |
      | price         | 15                      |
      | invoice       | first                   |
    Then I should get error that adding duplicate product in invoice is forbidden
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should contain 3 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 0 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 1 products "Test Product B"
    And the third invoice from order "bo_order1" should contain 2 products "Test Product A"
    And the third invoice from order "bo_order1" should contain 5 products "Test Product B"
    When I add products to order "bo_order1" to the third invoice and the following products details:
      | name          | Test Product A |
      | amount        | 1              |
      | price         | 10             |
    Then I should get error that adding duplicate product in invoice is forbidden
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should contain 3 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 0 products "Test Product A"
    And the second invoice from order "bo_order1" should contain 1 products "Test Product B"
    And the third invoice from order "bo_order1" should contain 2 products "Test Product A"
    And the third invoice from order "bo_order1" should contain 5 products "Test Product B"

  # This scenario is completely commented for now because it includes too many features that still need to be fixed
  #  - discount targetting specific invoice
  #  - free shipping for a specific invoice on creation
  #  - shipping computations separated on each individual invoice
#  Scenario: I add products in different invoices using the free shipping option (only works for new invoice)
#    When I generate invoice for "bo_order1" order
#    Then order "bo_order1" should have 1 invoice
#    When I add products to order "bo_order1" to the first invoice and the following products details:
#      | name          | Test Product A |
#      | amount        | 3              |
#      | price         | 15             |
#      | free_shipping | true           |
#    And I add products to order "bo_order1" with new invoice and the following products details:
#      | name          | Test Product A |
#      | amount        | 2              |
#      | price         | 15             |
#      | free_shipping | true           |
#    And I add products to order "bo_order1" to the second invoice and the following products details:
#      | name          | Test Product B |
#      | amount        | 1              |
#      | price         | 10             |
#    And I add products to order "bo_order1" with new invoice and the following products details:
#      | name          | Test Product B |
#      | amount        | 5              |
#      | price         | 10             |
#      | free_shipping | false          |
#    And I add products to order "bo_order1" with new invoice and the following products details:
#      | name          | Test Product A |
#      | amount        | 1              |
#      | price         | 15             |
#      | free_shipping | true           |
#    Then order "bo_order1" should have 4 invoices
#    And order "bo_order1" should have 14 products in total
#    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
#    And order "bo_order1" should contain 6 products "Test Product A"
#    And order "bo_order1" should contain 6 products "Test Product B"
#    And the available stock for product "Test Product A" should be 94
#    And the available stock for product "Test Product B" should be 94
#    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
#    And the first invoice from order "bo_order1" should contain 3 products "Test Product A"
#    And the first invoice from order "bo_order1" should have following details:
#      | total_products          | 68.800 |
#      | total_products_wt       | 72.930 |
#      | total_discount_tax_excl | 0.0    |
#      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 75.80  |
#      | total_paid_tax_incl     | 80.35  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
#    And the second invoice from order "bo_order1" should contain 2 products "Test Product A"
#    And the second invoice from order "bo_order1" should contain 1 products "Test Product B"
#    And the second invoice from order "bo_order1" should have following details:
#      | total_products          | 40.000 |
#      | total_products_wt       | 42.400 |
#      | total_discount_tax_excl | 7.0    |
#      | total_discount_tax_incl | 7.42   |
#      | total_paid_tax_excl     | 40.00  |
#      | total_paid_tax_incl     | 42.40  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
#    And the third invoice from order "bo_order1" should contain 5 products "Test Product B"
#    And the third invoice from order "bo_order1" should have following details:
#      | total_products          | 50.000 |
#      | total_products_wt       | 53.000 |
#      | total_discount_tax_excl | 0.0    |
#      | total_discount_tax_incl | 0.0    |
#      | total_paid_tax_excl     | 57.00  |
#      | total_paid_tax_incl     | 60.42  |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
#    And fourth invoice from order "bo_order1" should contain 1 products "Test Product A"
#    And fourth invoice from order "bo_order1" should have following details:
#      | total_products          | 15.000 |
#      | total_products_wt       | 15.900 |
#      | total_discount_tax_excl | 7.0    |
#      | total_discount_tax_incl | 7.42   |
#      | total_paid_tax_excl     | 15.000 |
#      | total_paid_tax_incl     | 15.900 |
#      | total_shipping_tax_excl | 7.0    |
#      | total_shipping_tax_incl | 7.42   |
#    And order "bo_order1" should have following details:
#      | total_products           | 173.80 |
#      | total_products_wt        | 184.23 |
#      | total_discounts_tax_excl | 14.0   |
#      | total_discounts_tax_incl | 14.84  |
#      | total_paid_tax_excl      | 187.80 |
#      | total_paid_tax_incl      | 199.07 |
#      | total_paid               | 199.07 |
#      | total_paid_real          | 0.0    |
#      | total_shipping_tax_excl  | 28.0   |
#      | total_shipping_tax_incl  | 29.68  |
#    And order "bo_order1" carrier should have following details:
#      | weight                 | 0.000 |
#      | shipping_cost_tax_excl | 28.00 |
#      | shipping_cost_tax_incl | 29.68 |
