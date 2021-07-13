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

  Scenario: In an order, adding a product without combination, which has specific price rules with quantity threshold, will apply the specific price (if the condition is met when the product is added AND no customized price has been used)
    Given order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 0 cart rule
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
    And product "Test Product With Percentage Discount" has a specific price named "discount25" with a discount of 25.0 percent from quantity 5
    Then product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
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
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is the price without the discount so it is not a customized price
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
    Given I remove product "Test Product With Percentage Discount" from order "bo_order1"
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
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Percentage Discount |
      | amount        | 6                                     |
      | price         | 16                                    |
    Then order "bo_order1" should have 8 products in total
    And order "bo_order1" should contain 6 products "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 6 products "Test Product With Percentage Discount"
    And the available stock for product "Test Product With Percentage Discount" should be 94
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is the price without the discount so it is not a customized price, therefore the specific price by quantity is applied
    Then product "Test Product With Percentage Discount" in order "bo_order1" has following details:
      | product_quantity            | 6     |
      | product_price               | 12.00 |
      | original_product_price      | 16.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 76.32 |
      | total_price_tax_excl        | 72.00 |
    And order "bo_order1" should have following details:
      | total_products           | 95.800 |
      | total_products_wt        | 101.55 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 102.80 |
      | total_paid_tax_incl      | 108.97 |
      | total_paid               | 108.97 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    # User customized price
    Given I remove product "Test Product With Percentage Discount" from order "bo_order1"
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
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Percentage Discount |
      | amount        | 10                                    |
      | price         | 50                                    |
    Then order "bo_order1" should have 12 products in total
    And order "bo_order1" should contain 10 products "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 10 products "Test Product With Percentage Discount"
    And the available stock for product "Test Product With Percentage Discount" should be 90
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is the not price without the discount so it is a customized price, therefore the specific price is not applied even if the quantity condition is met
    Then product "Test Product With Percentage Discount" in order "bo_order1" has following details:
      | product_quantity            | 10     |
      | product_price               | 50.00  |
      | original_product_price      | 16.00  |
      | unit_price_tax_incl         | 53.00  |
      | unit_price_tax_excl         | 50.00  |
      | total_price_tax_incl        | 530.00 |
      | total_price_tax_excl        | 500.00 |
    And order "bo_order1" should have following details:
      | total_products           | 523.80   |
      | total_products_wt        | 555.230  |
      | total_discounts_tax_excl | 0.0000   |
      | total_discounts_tax_incl | 0.0000   |
      | total_paid_tax_excl      | 530.800  |
      | total_paid_tax_incl      | 562.650  |
      | total_paid               | 562.650  |
      | total_paid_real          | 0.0      |
      | total_shipping_tax_excl  | 7.0      |
      | total_shipping_tax_incl  | 7.42     |

  Scenario: In an order, updating a product without combination which has specific price rules with quantity threshold, won't apply the specific price
    Given order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 0 cart rule
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
    And product "Test Product With Percentage Discount" has a specific price named "discount25" with a discount of 25.0 percent from quantity 5
    Then product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
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
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is the price without the discount so it is not a customized price
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
    # quantity higher to threshold
    When I edit product "Test Product With Percentage Discount" to order "bo_order1" with following products details:
      | amount        | 6                     |
      | price         | 16                    |
    Then order "bo_order1" should have 8 products in total
    And order "bo_order1" should contain 6 products "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 6 products "Test Product With Percentage Discount"
    And the available stock for product "Test Product With Percentage Discount" should be 94
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is the price without the discount so it is not a customized price, however we don't apply rules from specific prices during a product update
    And product "Test Product With Percentage Discount" in order "bo_order1" has following details:
      | product_quantity            | 6      |
      | product_price               | 16.00  |
      | original_product_price      | 16.00  |
      | unit_price_tax_incl         | 16.96  |
      | unit_price_tax_excl         | 16.00  |
      | total_price_tax_incl        | 101.76 |
      | total_price_tax_excl        | 96.00  |
    And order "bo_order1" should have following details:
      | total_products           | 119.80   |
      | total_products_wt        | 126.990  |
      | total_discounts_tax_excl | 0.0000   |
      | total_discounts_tax_incl | 0.0000   |
      | total_paid_tax_excl      | 126.800  |
      | total_paid_tax_incl      | 134.410  |
      | total_paid               | 134.410  |
      | total_paid_real          | 0.0     |
      | total_shipping_tax_excl  | 7.0     |
      | total_shipping_tax_incl  | 7.42    |
    # User customized price
    When I edit product "Test Product With Percentage Discount" to order "bo_order1" with following products details:
      | amount        | 10                    |
      | price         | 50                    |
    Then order "bo_order1" should have 12 products in total
    And order "bo_order1" should contain 10 products "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 10 products "Test Product With Percentage Discount"
    And the available stock for product "Test Product With Percentage Discount" should be 90
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is not the price without the discount so it is a customized price, so it makes two reasons not to apply the specific price related to quantity
    Then product "Test Product With Percentage Discount" in order "bo_order1" has following details:
      | product_quantity            | 10     |
      | product_price               | 50.00  |
      | original_product_price      | 16.00  |
      | unit_price_tax_incl         | 53.00  |
      | unit_price_tax_excl         | 50.00  |
      | total_price_tax_incl        | 530.00 |
      | total_price_tax_excl        | 500.00 |
    And order "bo_order1" should have following details:
      | total_products           | 523.80   |
      | total_products_wt        | 555.230  |
      | total_discounts_tax_excl | 0.0000   |
      | total_discounts_tax_incl | 0.0000   |
      | total_paid_tax_excl      | 530.800  |
      | total_paid_tax_incl      | 562.650  |
      | total_paid               | 562.650  |
      | total_paid_real          | 0.0      |
      | total_shipping_tax_excl  | 7.0      |
      | total_shipping_tax_incl  | 7.42     |

  Scenario: In an order, adding any combination of a product with specific price, will apply the discount when quantity threshold is reached
    # quantity discounts based on combinations
    # Ignored for now - Given shop configuration for "PS_QTY_DISCOUNT_ON_COMBINATION" is set to 1
    Given order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 0 cart rule
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
    Given there is a product in the catalog named "Test Product With Combination and Specific Price" with a price of 16.0 and 200 items in stock
    And product "Test Product With Combination and Specific Price" has combinations with following details:
      | reference    | quantity | attributes |
      | combination1 | 100      | Size:L     |
      | combination2 | 100      | Size:M     |
    Then the available stock for combination "combination1" of product "Test Product With Combination and Specific Price" should be 100
    And the available stock for combination "combination2" of product "Test Product With Combination and Specific Price" should be 100
    And product "Test Product With Combination and Specific Price" has a specific price named "discount25" with a discount of 25.0 percent from quantity 5
    Then product "Test Product With Combination and Specific Price" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # Adding the 2 combinations
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Combination and Specific Price    |
      | combination   | combination1  |
      | amount        | 4             |
      | price         | 16            |
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Combination and Specific Price    |
      | combination   | combination2  |
      | amount        | 6             |
      | price         | 16            |
    Then order "bo_order1" should have 12 products in total
    And order "bo_order1" should contain 10 products "Test Product With Combination and Specific Price"
    And cart of order "bo_order1" should contain 0 products "Test Product With Combination and Specific Price"
    And cart of order "bo_order1" should contain 4 combinations "combination1" of product "Test Product With Combination and Specific Price"
    And cart of order "bo_order1" should contain 6 combinations "combination2" of product "Test Product With Combination and Specific Price"
    And the available stock for product "Test Product With Combination and Specific Price" should be 190
    And order "bo_order1" should contain 4 combination "combination1" of product "Test Product With Combination and Specific Price"
    And order "bo_order1" should contain 6 combination "combination2" of product "Test Product With Combination and Specific Price"
    Then combination "combination1" of product "Test Product With Combination and Specific Price" in order "bo_order1" has following details:
      | product_quantity            | 4      |
      | product_price               | 16.00  |
      | original_product_price      | 16.00  |
      | unit_price_tax_incl         | 16.96  |
      | unit_price_tax_excl         | 16.00  |
      | total_price_tax_incl        | 67.84  |
      | total_price_tax_excl        | 64.00  |
    Then combination "combination2" of product "Test Product With Combination and Specific Price" in order "bo_order1" has following details:
      | product_quantity            | 6      |
      | product_price               | 12.00  |
      | original_product_price      | 16.00  |
      | unit_price_tax_incl         | 12.72  |
      | unit_price_tax_excl         | 12.00  |
      | total_price_tax_incl        | 76.32  |
      | total_price_tax_excl        | 72.00  |
    And order "bo_order1" should have following details:
      | total_products           | 159.80  |
      | total_products_wt        | 169.390 |
      | total_discounts_tax_excl | 0.0000  |
      | total_discounts_tax_incl | 0.0000  |
      | total_paid_tax_excl      | 166.80  |
      | total_paid_tax_incl      | 176.810 |
      | total_paid               | 176.810 |
      | total_paid_real          | 0.0     |
      | total_shipping_tax_excl  | 7.0     |
      | total_shipping_tax_incl  | 7.42    |

  Scenario: In an order, adding a product without combination, which has specific price rules with quantity threshold in another invoice, will keep the specific price of the first invoice
    Given order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 0 cart rule
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
    And product "Test Product With Percentage Discount" has a specific price named "discount25" with a discount of 25.0 percent from quantity 5
    Then product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Percentage Discount |
      | amount        | 6                                     |
      | price         | 16                                    |
    Then order "bo_order1" should have 8 products in total
    And order "bo_order1" should contain 6 products "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 6 products "Test Product With Percentage Discount"
    And the available stock for product "Test Product With Percentage Discount" should be 94
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is the price without the discount so it is a specific price
    Then product "Test Product With Percentage Discount" in order "bo_order1" has following details:
      | product_quantity            | 6     |
      | product_price               | 12.00 |
      | original_product_price      | 16.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 76.32 |
      | total_price_tax_excl        | 72.00 |
    And order "bo_order1" should have following details:
      | total_products           | 95.800 |
      | total_products_wt        | 101.55 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 102.80 |
      | total_paid_tax_incl      | 108.97 |
      | total_paid               | 108.97 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I generate invoice for "bo_order1" order
    And the first invoice from order "bo_order1" should have following details:
      | total_products              | 95.800   |
      | total_products_wt           | 101.55   |
      | total_discount_tax_excl     | 0.0      |
      | total_discount_tax_incl     | 0.0      |
      | total_paid_tax_excl         | 102.80   |
      | total_paid_tax_incl         | 108.97   |
      | total_shipping_tax_excl     | 7.0      |
      | total_shipping_tax_incl     | 7.42     |
    # Add the same product a second time with quantity threshold not reached
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Percentage Discount |
      | amount        | 2                                     |
      | price         | 16                                    |
    Then order "bo_order1" should have 10 products in total
    And order "bo_order1" should contain 8 products "Test Product With Percentage Discount"
    And cart of order "bo_order1" should contain 8 products "Test Product With Percentage Discount"
    And the available stock for product "Test Product With Percentage Discount" should be 92
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 5          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is the price without the discount so it is a specific price
    Then the first orderDetail for product "Test Product With Percentage Discount" in order "bo_order1" has following details:
      | product_quantity            | 6     |
      | product_price               | 12.00 |
      | original_product_price      | 16.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 76.32 |
      | total_price_tax_excl        | 72.00 |
    Then the second orderDetail for product "Test Product With Percentage Discount" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 12.00 |
      | original_product_price      | 16.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 25.44 |
      | total_price_tax_excl        | 24.00 |
    And order "bo_order1" should have following details:
      | total_products           | 119.800 |
      | total_products_wt        | 126.99 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 126.80 |
      | total_paid_tax_incl      | 134.41 |
      | total_paid               | 134.41 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And the first invoice from order "bo_order1" should have following details:
      | total_products              | 95.800   |
      | total_products_wt           | 101.55   |
      | total_discount_tax_excl     | 0.0      |
      | total_discount_tax_incl     | 0.0      |
      | total_paid_tax_excl         | 102.80   |
      | total_paid_tax_incl         | 108.97   |
      | total_shipping_tax_excl     | 7.0      |
      | total_shipping_tax_incl     | 7.42     |
    And the second invoice from order "bo_order1" should have following details:
      | total_products              | 24.000   |
      | total_products_wt           | 25.44    |
      | total_discount_tax_excl     | 0.0      |
      | total_discount_tax_incl     | 0.0      |
      | total_paid_tax_excl         | 24.000   |
      | total_paid_tax_incl         | 25.440   |
      | total_shipping_tax_excl     | 7.0      |
      | total_shipping_tax_incl     | 7.42     |

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
    And product "Test Product With Percentage Discount" has a specific price named "discount25" with a discount of 25.0 percent
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
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
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The edited price matches the price with discount applied so it's not a custom price for this order it follows the general rules
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
    And product "Test Product With Percentage Discount" in order "bo_order1" has following details:
      | product_quantity            | 1       |
      | original_product_price      | 16.00   |
      | product_price               | 12.00   |
      | unit_price_tax_incl         | 12.72   |
      | unit_price_tax_excl         | 12.00   |
      | total_price_tax_incl        | 12.72   |
      | total_price_tax_excl        | 12.00   |
    # When the product is removed assert we don't remove the global CustomPrice either
    When I remove product "Test Product With Percentage Discount" from order "bo_order1"
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
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |

  Scenario: Add product which has specific price rules for a discount but keeps catalog price, then the order has its own customized price
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
    And product "Test Product With Percentage Discount" has a specific price named "discount25" with a discount of 25.0 percent
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
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
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    # The price set is the price without the discount so it is a customized price
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
    # When the product is removed assert we don't remove the global CustomPrice either
    When I remove product "Test Product With Percentage Discount" from order "bo_order1"
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
    And product "Test Product With Percentage Discount" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |