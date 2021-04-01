# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-gift-cart-rule
@reset-database-before-feature
@order-gift-cart-rule
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
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "Test Product Gifted" with a price of 15.0 and 100 items in stock
    And the available stock for product "Test Product Gifted" should be 100
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"

  Scenario: Add discount with gift product to cart, and remove it in the order
    Given I use a voucher "CartRuleGiftProduct" which provides a gift product "Test Product Gifted" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 15.0   |
      | total_discounts_tax_incl | 15.9   |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$15.00"
    And the available stock for product "Test Product Gifted" should be 99
    When I remove cart rule "CartRuleGiftProduct" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoice
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
    And order "bo_order1" should contain 0 product "Test Product Gifted"
    And the available stock for product "Test Product Gifted" should be 100

  Scenario: Add a product then add a discount with this product as a gift, and remove it from the order
    Given I add 1 products "Test Product Gifted" to the cart "dummy_cart"
    And I use a voucher "CartRuleGiftProduct" which provides a gift product "Test Product Gifted" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 15.0   |
      | total_discounts_tax_incl | 15.9   |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$15.00"
    And the available stock for product "Test Product Gifted" should be 98
    And gifted product "Test Product Gifted" quantity in cart "dummy_cart" should be 1
    When I remove cart rule "CartRuleGiftProduct" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the available stock for product "Test Product Gifted" should be 99
    And cart "dummy_cart" should not contain gift product "Test Product Gifted"
    But cart "dummy_cart" should contain product "Test Product Gifted"

  Scenario: Add discount with gift product to cart, and remove the gifted product in the order and add it again the discount is still present
    Given I use a voucher "CartRuleGiftProduct" which provides a gift product "Test Product Gifted" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 15.0   |
      | total_discounts_tax_incl | 15.9   |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$15.00"
    And the available stock for product "Test Product Gifted" should be 99
    When I remove product "Test Product Gifted" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    And cart "dummy_cart" should not contain gift product "Test Product Gifted"
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
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
    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$0.00"
    And order "bo_order1" should contain 0 product "Test Product Gifted"
    And the available stock for product "Test Product Gifted" should be 100
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Gifted |
      | amount        | 1                   |
      | price         | 15.0                |
    Then order "bo_order1" should have 3 products in total
    And gifted product "Test Product Gifted" quantity in cart "dummy_cart" should be 1
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 15.0   |
      | total_discounts_tax_incl | 15.9   |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$15.00"
    And the available stock for product "Test Product Gifted" should be 99

  Scenario: Add multiple order details contain the gift product, Then the one with at least 2 is updated
    And I use a voucher "CartRuleGiftProduct" which provides a gift product "Test Product Gifted" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I generate invoice for "bo_order1" order
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Gifted |
      | amount        | 2                   |
      | price         | 15.0                |
    Then order "bo_order1" should have 5 products in total
    And order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 68.800 |
      | total_products_wt        | 72.930 |
      # Total is not correct here because of bug from issue #20778 (discount amount applied twice), current is 30 should be 15
#      | total_discounts_tax_excl | 15.0   |
#      | total_discounts_tax_incl | 15.9   |
#      | total_paid_tax_excl      | 60.800 |
#      | total_paid_tax_incl      | 64.450 |
#      | total_paid               | 64.450 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And the product "Test Product Gifted" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the product "Test Product Gifted" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    # Discount is not correct here because of bug from issue #20778 (discount amount applied twice), current is 30 should be 15
#    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$15.00"
    And the available stock for product "Test Product Gifted" should be 97
    When I remove cart rule "CartRuleGiftProduct" from order "bo_order1"
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 60.800 |
      | total_paid_tax_incl      | 64.450 |
      | total_paid               | 64.450 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And the product "Test Product Gifted" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the product "Test Product Gifted" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the available stock for product "Test Product Gifted" should be 98

  Scenario: Add multiple order details that contain only one gift product, the first one is removed by default with the gift cart rule
    And I use a voucher "CartRuleGiftProduct" which provides a gift product "Test Product Gifted" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I generate invoice for "bo_order1" order
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Gifted |
      | amount        | 1                           |
      | price         | 15.0                        |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      # Total is not correct here because of bug from issue #20778 (discount amount applied twice), current is 30 should be 15
#      | total_discounts_tax_excl | 15.0   |
#      | total_discounts_tax_incl | 15.9   |
#      | total_paid_tax_excl      | 45.800 |
#      | total_paid_tax_incl      | 48.550 |
#      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And the product "Test Product Gifted" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the product "Test Product Gifted" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    # Discount is not correct here because of bug from issue #20778 (discount amount applied twice), current is 30 should be 15
#    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$15.00"
    And the available stock for product "Test Product Gifted" should be 98
    When I remove cart rule "CartRuleGiftProduct" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And the first invoice from order "bo_order1" should contain 0 product "Test Product Gifted"
    And the product "Test Product Gifted" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the available stock for product "Test Product Gifted" should be 99

  Scenario: I have a cart rule that is associated to a product which adds a gift product, I add this product to cart and then I remove it
    Given there is a product in the catalog named "Test Product With Auto Gift" with a price of 12.0 and 100 items in stock
    And there is a cart rule named "MultiGiftAutoCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "MultiGiftAutoCartRule" has no discount code
    And cart rule "MultiGiftAutoCartRule" is restricted to product "Test Product With Auto Gift"
    And cart rule "MultiGiftAutoCartRule" offers free shipping
    And cart rule "MultiGiftAutoCartRule" offers a gift product "Test Product Gifted"
    And I add 1 products "Test Product With Auto Gift" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 50.800 |
      | total_products_wt        | 53.850 |
      | total_discounts_tax_excl | 23.00  |
      | total_discounts_tax_incl | 24.38  |
      | total_paid_tax_excl      | 34.800 |
      | total_paid_tax_incl      | 36.890 |
      | total_paid               | 36.890 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$23.00"
    And the available stock for product "Test Product Gifted" should be 99
    And the available stock for product "Test Product With Auto Gift" should be 99
    When I remove product "Test Product With Auto Gift" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should contain 0 product "Test Product With Auto Gift"
    Then order "bo_order1" should contain 0 product "Test Product Gifted"
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
    And the available stock for product "Test Product With Auto Gift" should be 100
    And the available stock for product "Test Product Gifted" should be 100

  Scenario: I have a cart rule that is associated to a product which adds a gift product, I add this product to the order and then I remove it
    Given there is a product in the catalog named "Test Product With Auto Gift" with a price of 12.0 and 100 items in stock
    And there is a cart rule named "MultiGiftAutoCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "MultiGiftAutoCartRule" has no discount code
    And cart rule "MultiGiftAutoCartRule" is restricted to product "Test Product With Auto Gift"
    And cart rule "MultiGiftAutoCartRule" offers free shipping
    And cart rule "MultiGiftAutoCartRule" offers a gift product "Test Product Gifted"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 2 products in total
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
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Auto Gift |
      | amount        | 1                           |
      | price         | 12.00                       |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 50.800 |
      | total_products_wt        | 53.850 |
      | total_discounts_tax_excl | 23.00  |
      | total_discounts_tax_incl | 24.38  |
      | total_paid_tax_excl      | 34.800 |
      | total_paid_tax_incl      | 36.890 |
      | total_paid               | 36.890 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$23.00"
    And the available stock for product "Test Product Gifted" should be 99
    And the available stock for product "Test Product With Auto Gift" should be 99
    When I remove product "Test Product With Auto Gift" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should contain 0 product "Test Product With Auto Gift"
    Then order "bo_order1" should contain 0 product "Test Product Gifted"
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
    And the available stock for product "Test Product With Auto Gift" should be 100
    And the available stock for product "Test Product Gifted" should be 100

  Scenario: I remove a product with associated gift product, but this gift was already present in the cart
    Given there is a product in the catalog named "Test Product With Auto Gift" with a price of 12.0 and 100 items in stock
    And there is a cart rule named "MultiGiftAutoCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "MultiGiftAutoCartRule" has no discount code
    And cart rule "MultiGiftAutoCartRule" is restricted to product "Test Product With Auto Gift"
    And cart rule "MultiGiftAutoCartRule" offers free shipping
    And cart rule "MultiGiftAutoCartRule" offers a gift product "Test Product Gifted"
    And I add 1 products "Test Product Gifted" to the cart "dummy_cart"
    And I add 1 products "Test Product With Auto Gift" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 5 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 65.800 |
      | total_products_wt        | 69.750 |
      | total_discounts_tax_excl | 23.00  |
      | total_discounts_tax_incl | 24.38  |
      | total_paid_tax_excl      | 49.800 |
      | total_paid_tax_incl      | 52.790 |
      | total_paid               | 52.790 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$23.00"
    And the available stock for product "Test Product Gifted" should be 98
    And the available stock for product "Test Product With Auto Gift" should be 99
    When I remove product "Test Product With Auto Gift" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 0 product "Test Product With Auto Gift"
    Then order "bo_order1" should contain 1 product "Test Product Gifted"
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the available stock for product "Test Product With Auto Gift" should be 100
    And the available stock for product "Test Product Gifted" should be 99

  Scenario: I add the product with associated gift when the cart already has the gift, the quantity should be updated
    Given there is a product in the catalog named "Test Product With Auto Gift" with a price of 12.0 and 100 items in stock
    And there is a cart rule named "MultiGiftAutoCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "MultiGiftAutoCartRule" has no discount code
    And cart rule "MultiGiftAutoCartRule" is restricted to product "Test Product With Auto Gift"
    And cart rule "MultiGiftAutoCartRule" offers free shipping
    And cart rule "MultiGiftAutoCartRule" offers a gift product "Test Product Gifted"
    And I add 1 products "Test Product Gifted" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the available stock for product "Test Product Gifted" should be 99
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Auto Gift |
      | amount        | 1                           |
      | price         | 12.00                       |
    Then order "bo_order1" should have 5 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 65.800 |
      | total_products_wt        | 69.750 |
      | total_discounts_tax_excl | 23.00  |
      | total_discounts_tax_incl | 24.38  |
      | total_paid_tax_excl      | 49.800 |
      | total_paid_tax_incl      | 52.790 |
      | total_paid               | 52.790 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$23.00"
    And the available stock for product "Test Product Gifted" should be 98
    And the available stock for product "Test Product With Auto Gift" should be 99
    When I remove product "Test Product With Auto Gift" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 0 product "Test Product With Auto Gift"
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the available stock for product "Test Product With Auto Gift" should be 100
    And the available stock for product "Test Product Gifted" should be 99

  Scenario: I add the product with associated gift, I remove the CartRule from the order
    Given there is a product in the catalog named "Test Product With Auto Gift" with a price of 12.0 and 100 items in stock
    And there is a cart rule named "MultiGiftAutoCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "MultiGiftAutoCartRule" has no discount code
    And cart rule "MultiGiftAutoCartRule" is restricted to product "Test Product With Auto Gift"
    And cart rule "MultiGiftAutoCartRule" offers free shipping
    And cart rule "MultiGiftAutoCartRule" offers a gift product "Test Product Gifted"
    And I add 1 products "Test Product With Auto Gift" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 50.800 |
      | total_products_wt        | 53.850 |
      | total_discounts_tax_excl | 23.00  |
      | total_discounts_tax_incl | 24.38  |
      | total_paid_tax_excl      | 34.800 |
      | total_paid_tax_incl      | 36.890 |
      | total_paid               | 36.890 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$23.00"
    And the available stock for product "Test Product Gifted" should be 99
    And the available stock for product "Test Product With Auto Gift" should be 99
    When I remove cart rule "MultiGiftAutoCartRule" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 35.800 |
      | total_products_wt        | 37.950 |
      | total_discounts_tax_excl | 0.00   |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 42.800 |
      | total_paid_tax_incl      | 45.370 |
      | total_paid               | 45.370 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should contain 0 product "Test Product Gifted"
    And the available stock for product "Test Product Gifted" should be 100
    And the available stock for product "Test Product With Auto Gift" should be 99

  Scenario: I add the product with associated gift, I remove the gift from order
    Given there is a product in the catalog named "Test Product With Auto Gift" with a price of 12.0 and 100 items in stock
    And there is a cart rule named "MultiGiftAutoCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "MultiGiftAutoCartRule" has no discount code
    And cart rule "MultiGiftAutoCartRule" is restricted to product "Test Product With Auto Gift"
    And cart rule "MultiGiftAutoCartRule" offers free shipping
    And cart rule "MultiGiftAutoCartRule" offers a gift product "Test Product Gifted"
    And I add 1 products "Test Product With Auto Gift" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 50.800 |
      | total_products_wt        | 53.850 |
      | total_discounts_tax_excl | 23.00  |
      | total_discounts_tax_incl | 24.38  |
      | total_paid_tax_excl      | 34.800 |
      | total_paid_tax_incl      | 36.890 |
      | total_paid               | 36.890 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$23.00"
    And the available stock for product "Test Product Gifted" should be 99
    And the available stock for product "Test Product With Auto Gift" should be 99
    When I remove product "Test Product Gifted" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 35.800 |
      | total_products_wt        | 37.950 |
      | total_discounts_tax_excl | 8.00   |
      | total_discounts_tax_incl | 8.48   |
      | total_paid_tax_excl      | 34.800 |
      | total_paid_tax_incl      | 36.890 |
      | total_paid               | 36.890 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$8.00"
    And order "bo_order1" should contain 0 product "Test Product Gifted"
    And the available stock for product "Test Product Gifted" should be 100
    And the available stock for product "Test Product With Auto Gift" should be 99

  Scenario: I add the product with associated gift when the cart already has the gift, then I change the product quantity several times and finally delete it
    Given there is a product in the catalog named "Test Product With Auto Gift" with a price of 12.0 and 100 items in stock
    And there is a cart rule named "MultiGiftAutoCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "MultiGiftAutoCartRule" has no discount code
    And cart rule "MultiGiftAutoCartRule" is restricted to product "Test Product With Auto Gift"
    And cart rule "MultiGiftAutoCartRule" offers free shipping
    And cart rule "MultiGiftAutoCartRule" offers a gift product "Test Product Gifted"
    And I add 1 products "Test Product Gifted" to the cart "dummy_cart"
    And I add 1 products "Test Product With Auto Gift" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 5 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 65.800 |
      | total_products_wt        | 69.750 |
      | total_discounts_tax_excl | 23.00  |
      | total_discounts_tax_incl | 24.38  |
      | total_paid_tax_excl      | 49.800 |
      | total_paid_tax_incl      | 52.790 |
      | total_paid               | 52.790 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$23.00"
    And the available stock for product "Test Product Gifted" should be 98
    And the available stock for product "Test Product With Auto Gift" should be 99
    When I edit product "Test Product Gifted" to order "bo_order1" with following products details:
      | amount        | 1                       |
      | price         | 15                      |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 50.800 |
      | total_products_wt        | 53.850 |
      | total_discounts_tax_excl | 23.00  |
      | total_discounts_tax_incl | 24.38  |
      | total_paid_tax_excl      | 34.800 |
      | total_paid_tax_incl      | 36.890 |
      | total_paid               | 36.890 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$23.00"
    And the available stock for product "Test Product Gifted" should be 99
    And the available stock for product "Test Product With Auto Gift" should be 99
    When I edit product "Test Product Gifted" to order "bo_order1" with following products details:
      | amount        | 2                       |
      | price         | 15                      |
    Then order "bo_order1" should have 5 products in total
    And order "bo_order1" should have following details:
      | total_products           | 65.800 |
      | total_products_wt        | 69.750 |
      | total_discounts_tax_excl | 23.00  |
      | total_discounts_tax_incl | 24.38  |
      | total_paid_tax_excl      | 49.800 |
      | total_paid_tax_incl      | 52.790 |
      | total_paid               | 52.790 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$23.00"
    And the available stock for product "Test Product Gifted" should be 98
    And the available stock for product "Test Product With Auto Gift" should be 99
    When I remove product "Test Product Gifted" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 35.800 |
      | total_products_wt        | 37.950 |
      | total_discounts_tax_excl | 8.00   |
      | total_discounts_tax_incl | 8.48   |
      | total_paid_tax_excl      | 34.800 |
      | total_paid_tax_incl      | 36.890 |
      | total_paid               | 36.890 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Test Product With Auto Gift" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 12.72 |
      | total_price_tax_excl        | 12.00 |
    And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "$8.00"
    And order "bo_order1" should contain 0 product "Test Product Gifted"
    And the available stock for product "Test Product Gifted" should be 100
    And the available stock for product "Test Product With Auto Gift" should be 99

  Scenario: I have a cart rule that automatically adds a gift product, I add this product to cart and then I decrease it
    And there is a cart rule named "AutoGiftCartRule" that applies an amount discount of 0.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "AutoGiftCartRule" has no discount code
    And cart rule "AutoGiftCartRule" offers a gift product "Test Product Gifted"
    And I delete product "Mug The best is yet to come" from cart "dummy_cart"
    And I add 1 products "Test Product Gifted" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 30.00 |
      | total_products_wt        | 31.80 |
      | total_discounts_tax_excl | 15.00 |
      | total_discounts_tax_incl | 15.90 |
      | total_paid_tax_excl      | 22.00 |
      | total_paid_tax_incl      | 23.32 |
      | total_paid               | 23.32 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And order "bo_order1" should have cart rule "AutoGiftCartRule" with amount "$15.00"
    And the available stock for product "Test Product Gifted" should be 98
    When I edit product "Test Product Gifted" to order "bo_order1" with following products details:
      | amount        | 1     |
      | price         | 15.00 |
    Then order "bo_order1" should have 0 products in total
    Then order "bo_order1" should contain 0 product "Test Product Gifted"
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 0.00 |
      | total_products_wt        | 0.00 |
      | total_discounts_tax_excl | 0.00 |
      | total_discounts_tax_incl | 0.00 |
      | total_paid_tax_excl      | 0.00 |
      | total_paid_tax_incl      | 0.00 |
      | total_paid               | 0.00 |
      | total_paid_real          | 0.00 |
      | total_shipping_tax_excl  | 0.00 |
      | total_shipping_tax_incl  | 0.00 |
    And the available stock for product "Test Product Gifted" should be 100
    Then order "bo_order1" has status "Awaiting bank wire payment"
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Gifted |
      | amount        | 1                   |
      | price         | 15.0                |
    Then order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 30.00 |
      | total_products_wt        | 31.80 |
      | total_discounts_tax_excl | 15.00 |
      | total_discounts_tax_incl | 15.90 |
      | total_paid_tax_excl      | 22.00 |
      | total_paid_tax_incl      | 23.32 |
      | total_paid               | 23.32 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
    And product "Test Product Gifted" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And order "bo_order1" should have cart rule "AutoGiftCartRule" with amount "$15.00"
    And the available stock for product "Test Product Gifted" should be 98

  Scenario: I add the product with associated gift when the cart already has the gift, and the gift product quantity in stock before cart rule applies is 1
    Given there is a product in the catalog named "product triggering gift" with a price of 12.0 and 100 items in stock
    Given there is a product in the catalog named "gifted product" with a price of 15.0 and 2 items in stock
    And there is a cart rule named "GiftAutoCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "GiftAutoCartRule" has no discount code
    And cart rule "GiftAutoCartRule" is restricted to product "product triggering gift"
    And cart rule "GiftAutoCartRule" offers free shipping
    And cart rule "GiftAutoCartRule" offers a gift product "gifted product"
    And I add 1 products "gifted product" to the cart "dummy_cart"
    Given I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoice
    And the available stock for product "gifted product" should be 1
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.00   |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | product triggering gift  |
      | amount        | 1                        |
      | price         | 12.0                     |
    Then order "bo_order1" should have 5 products in total
    And the available stock for product "gifted product" should be 0
    And product "gifted product" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And order "bo_order1" should have following details:
      | total_products           | 65.800 |
      | total_products_wt        | 69.750 |
      | total_discounts_tax_excl | 23.000 |
      | total_discounts_tax_incl | 24.380 |
      | total_paid_tax_excl      | 49.800 |
      | total_paid_tax_incl      | 52.790 |
      | total_paid               | 52.790 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: I have a cart rule that is associated to a product which adds a discount,
      and an another cart rule which adds a free gift which is the same product,
      I add this product,
      I order,
      I remove all cart rules,
      I remove the product
    Given there is a product in the catalog named "Product 12345" with a price of 12.0 and 100 items in stock
    # Create a cart rule : No cade + Product restriction with min quanity + Discount 50%
    And there is a cart rule named "cartRulePercentDiscountOnSpecificProduct" that applies a percent discount of 50.0% with priority 1, quantity of 100 and quantity per user 100
    And cart rule "cartRulePercentDiscountOnSpecificProduct" has no discount code
    And cart rule "cartRulePercentDiscountOnSpecificProduct" is restricted to product "Product 12345" with a quantity of 2
    # Create a cart rule : No cade + no conditions + free gift = demo_6
    And there is a cart rule named "cartRuleFreeGift" that applies no discount with priority 1, quantity of 100 and quantity per user 100
    And cart rule "cartRuleFreeGift" has no discount code
    And cart rule "cartRuleFreeGift" offers a gift product "Product 12345"
    # Make an order
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 1 products "Product 12345" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 2 cart rules
    And order "bo_order1" should have following details:
      | total_products           | 24.00 |
      | total_products_wt        | 25.44 |
      | total_discounts_tax_excl | 18.00 |
      | total_discounts_tax_incl | 19.08 |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_paid_tax_excl      | 13.00 |
      | total_paid_tax_incl      | 13.78 |
      | total_paid               | 13.78 |
      | total_paid_real          | 0.0   |
    # total_products : 12 +12 = 24 (+ 6% (1.44) = 25.44)
    # total_discounts : 12 (cartRuleFreeGift) + 6 (cartRulePercentDiscountOnSpecificProduct) = 18 (+ 6% (1.08) = 19.08)
    # total_paid_tax_excl : 24 - 18 + 7 = 19

    # Remove cart rule "cartRulePercentDiscountOnSpecificProduct"
    Then I remove cart rule "cartRulePercentDiscountOnSpecificProduct" from order "bo_order1"
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rules
    And order "bo_order1" should have following details:
      | total_products           | 24.00 |
      | total_products_wt        | 25.44 |
      | total_discounts_tax_excl | 12.00 |
      | total_discounts_tax_incl | 12.72 |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_paid_tax_excl      | 19.00 |
      | total_paid_tax_incl      | 20.14 |
      | total_paid               | 20.14 |
      | total_paid_real          | 0.0   |
    # total_products : 12 +12 = 24 (+ 6% (1.44) = 25.44)
    # total_discounts : 12 (cartRuleFreeGift) = 12 (+ 6% (0.72) = 12.72)
    # total_paid_tax_excl : 24 - 12 + 7 = 19

    # Remove cart rule "cartRuleFreeGift"
    Then I remove cart rule "cartRuleFreeGift" from order "bo_order1"
    And order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 0 cart rules
    And order "bo_order1" should have following details:
      | total_products           | 12.00 |
      | total_products_wt        | 12.72 |
      | total_discounts_tax_excl | 0.00  |
      | total_discounts_tax_incl | 0.00  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_paid_tax_excl      | 19.00 |
      | total_paid_tax_incl      | 20.14 |
      | total_paid               | 20.14 |
      | total_paid_real          | 0.0   |
    # total_products : 12 = 12 (+ 6% (0.72) = 12.72)
    # total_discounts : 0
    # total_paid_tax_excl : 12 - 0 + 7 = 19

    # Delete the product
    Then I remove product "Product 12345" from order "bo_order1"
    And I should get no order error
    And order "bo_order1" should have 0 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 0 cart rules
    And order "bo_order1" should have following details:
      | total_products           | 0.00  |
      | total_products_wt        | 0.00  |
      | total_discounts_tax_excl | 0.00  |
      | total_discounts_tax_incl | 0.00  |
      | total_shipping_tax_excl  | 0.00  |
      | total_shipping_tax_incl  | 0.00  |
      | total_paid_tax_excl      | 0.00  |
      | total_paid_tax_incl      | 0.00  |
      | total_paid               | 0.00  |
      | total_paid_real          | 0.00  |

  Scenario: I have a cart rule that adds a free gift when delivery address is in the USA,
            when I change this delivery address, the free gift is removed

    Given country "US" is enabled
    And country "FR" is enabled
    And there is a product in the catalog named "Product 12345" with a price of 12.0 and 100 items in stock
    And there is a product in the catalog named "Gift product" with a price of 13.0 and 100 items in stock
    # Create a cart rule : No cade + no conditions + free gift = demo_6
    And there is a cart rule named "cartRuleFreeGift" that applies no discount with priority 1, quantity of 100 and quantity per user 100
    And cart rule "cartRuleFreeGift" has no discount code
    And cart rule "cartRuleFreeGift" offers a gift product "Gift product"
    And cart rule "cartRuleFreeGift" is restricted to country "US"
    # Make an order
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 1 products "Product 12345" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 25.00 |
      | total_products_wt        | 26.50 |
      | total_discounts_tax_excl | 13.00 |
      | total_discounts_tax_incl | 13.78 |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_paid_tax_excl      | 19.00 |
      | total_paid_tax_incl      | 20.14 |
      | total_paid               | 20.14 |
      | total_paid_real          | 0.0   |
    When I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-france-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
    And I change order "bo_order1" shipping address to "test-customer-france-address"
    Then order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 12.00 |
      | total_products_wt        | 12.00 |
      | total_discounts_tax_excl | 0.0   |
      | total_discounts_tax_incl | 0.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.0   |
      | total_paid_tax_excl      | 19.00 |
      | total_paid_tax_incl      | 19.00 |
      | total_paid               | 19.00 |
      | total_paid_real          | 0.0   |
    When I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-states-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Miami                        |
      | Country          | United States                |
      | State            | Florida                      |
      | Postal code      | 33133                        |
    And I change order "bo_order1" shipping address to "test-customer-states-address"
    Then order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 25.00 |
      | total_products_wt        | 26.50 |
      | total_discounts_tax_excl | 13.00 |
      | total_discounts_tax_incl | 13.78 |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_paid_tax_excl      | 19.00 |
      | total_paid_tax_incl      | 20.14 |
      | total_paid               | 20.14 |
      | total_paid_real          | 0.0   |


  Scenario: I have a cart rule that adds a free gift when a certain amount is reached,
            when the amount is not reached anymore, the free gift is removed

    Given there is a product in the catalog named "Product 12345" with a price of 12.0 and 100 items in stock
    And there is a product in the catalog named "Gift product" with a price of 13.0 and 100 items in stock
    # Create a cart rule : No cade + no conditions + free gift = demo_6
    And there is a cart rule named "cartRuleFreeGift" that applies no discount with priority 1, quantity of 100 and quantity per user 100
    And cart rule "cartRuleFreeGift" has no discount code
    And cart rule "cartRuleFreeGift" offers a gift product "Gift product"
    And cart rule "cartRuleFreeGift" applies discount only when cart total is above 30.00
    # Make an order
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I add 3 products "Product 12345" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 49.00 |
      | total_products_wt        | 49.00 |
      | total_discounts_tax_excl | 13.00 |
      | total_discounts_tax_incl | 13.00 |
      | total_shipping_tax_excl  | 0.0   |
      | total_shipping_tax_incl  | 0.0   |
      | total_paid_tax_excl      | 36.00 |
      | total_paid_tax_incl      | 36.00 |
      | total_paid               | 36.00 |
      | total_paid_real          | 0.0   |
    When I edit product "Product 12345" to order "bo_order1" with following products details:
      | amount | 1     |
      | price  | 12.00 |
    Then order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 12.00 |
      | total_products_wt        | 12.00 |
      | total_discounts_tax_excl | 0.0   |
      | total_discounts_tax_incl | 0.0   |
      | total_shipping_tax_excl  | 0.0   |
      | total_shipping_tax_incl  | 0.0   |
      | total_paid_tax_excl      | 12.00 |
      | total_paid_tax_incl      | 12.00 |
      | total_paid               | 12.00 |
      | total_paid_real          | 0.0   |
    When I edit product "Product 12345" to order "bo_order1" with following products details:
      | amount | 3 |
      | price  | 12.00 |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoice
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 49.00 |
      | total_products_wt        | 49.00 |
      | total_discounts_tax_excl | 13.00 |
      | total_discounts_tax_incl | 13.00 |
      | total_shipping_tax_excl  | 0.0   |
      | total_shipping_tax_incl  | 0.0   |
      | total_paid_tax_excl      | 36.00 |
      | total_paid_tax_incl      | 36.00 |
      | total_paid               | 36.00 |
      | total_paid_real          | 0.0   |
