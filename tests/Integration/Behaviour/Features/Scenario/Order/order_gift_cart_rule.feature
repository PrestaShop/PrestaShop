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
    And there is a product in the catalog named "Test Product Gift Cart Rule" with a price of 15.0 and 100 items in stock
    And the available stock for product "Test Product Gift Cart Rule" should be 100
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"

  Scenario: Add discount with gift product to cart, and remove it in the order
    Given I use a voucher "CartRuleGiftProduct" which provides a gift product "Test Product Gift Cart Rule" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoices
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
    And product "Test Product Gift Cart Rule" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$15.00"
    And the available stock for product "Test Product Gift Cart Rule" should be 99
    When I remove cart rule "CartRuleGiftProduct" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
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
    And order "bo_order1" should contain 0 product "Test Product Gift Cart Rule"
    And the available stock for product "Test Product Gift Cart Rule" should be 100

  Scenario: Add a product then add a discount with this product as a gift, and remove it from the order
    Given I add 1 products "Test Product Gift Cart Rule" to the cart "dummy_cart"
    And I use a voucher "CartRuleGiftProduct" which provides a gift product "Test Product Gift Cart Rule" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoices
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
    And product "Test Product Gift Cart Rule" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$15.00"
    And the available stock for product "Test Product Gift Cart Rule" should be 98
    When I remove cart rule "CartRuleGiftProduct" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoices
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
    And product "Test Product Gift Cart Rule" in order "bo_order1" has following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the available stock for product "Test Product Gift Cart Rule" should be 99

  Scenario: Add multiple order details contain the gift product, Then the one with at least 2 is updated
    And I use a voucher "CartRuleGiftProduct" which provides a gift product "Test Product Gift Cart Rule" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I generate invoice for "bo_order1" order
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Gift Cart Rule |
      | amount        | 2                           |
      | price         | 15.0                        |
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
    And the product "Test Product Gift Cart Rule" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the product "Test Product Gift Cart Rule" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 31.80 |
      | total_price_tax_excl        | 30.00 |
    # Discount is not correct here because of bug from issue #20778 (discount amount applied twice), current is 30 should be 15
#    And order "bo_order1" should have cart rule "CartRuleGiftProduct" with amount "$15.00"
    And the available stock for product "Test Product Gift Cart Rule" should be 97
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
    And the product "Test Product Gift Cart Rule" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the product "Test Product Gift Cart Rule" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1     |
      | product_price               | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | unit_price_tax_excl         | 15.00 |
      | total_price_tax_incl        | 15.90 |
      | total_price_tax_excl        | 15.00 |
    And the available stock for product "Test Product Gift Cart Rule" should be 98
