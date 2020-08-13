# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-specific-prices
@reset-database-before-feature
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
    And order "bo_order1" should have 1 invoices
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 12                                |
    Then product "Test Product With Specific Price" in order "bo_order1" should have specific price 12.0
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
    Then product "Test Product With Specific Price" in order "bo_order1" should have specific price 12.0
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoices
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
    Then product "Test Product With Specific Price" in order "bo_order1" should have specific price 12.0
    When I remove product "Test Product With Specific Price" from order "bo_order1"
    Then product "Test Product With Specific Price" in order "bo_order1" should have no specific price
    And order "bo_order1" should have 2 products in total
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
