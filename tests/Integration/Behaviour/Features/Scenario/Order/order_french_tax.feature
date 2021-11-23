# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-french-tax
@reset-database-before-feature
@clear-cache-before-feature
@order-french-tax
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO with french TAX (20%)

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "FR" is enabled
    And I add new tax "french-tax" with following properties:
      | name         | French Tax (20%) |
      | rate         | 20               |
      | is_enabled   | true             |
    And I add the tax rule group "french-tax-group" for the tax "french-tax" with the following conditions:
      | name         | French Tax (20%) |
      | country      | FR               |
    And I set tax rule group "french-tax-group" to product "Mug The best is yet to come"
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "FR" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And a carrier "price_carrier" with name "My cheap carrier" exists
    And I enable carrier "price_carrier"
    And I associate the tax rule group "french-tax-group" to carrier "price_carrier"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And cart "dummy_cart" should have "price_carrier" as a carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Generate order then modify product price then add same product on another invoice and check the price
    Given order "bo_order1" does not have any invoices
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 11.90 |
      | original_product_price      | 11.90 |
      | unit_price_tax_incl         | 14.28 |
      | unit_price_tax_excl         | 11.90 |
      | total_price_tax_incl        | 28.56 |
      | total_price_tax_excl        | 23.80 |
    And order "bo_order1" should have following details:
      | total_products           | 23.80 |
      | total_products_wt        | 28.56 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 28.80 |
      | total_paid_tax_incl      | 34.56 |
      | total_paid               | 34.56 |
      | total_paid_real          | 0.00  |
      | total_shipping_tax_excl  | 5.00  |
      | total_shipping_tax_incl  | 6.00  |
    # Edit with two values that match (with computed tax values)
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount         | 2                       |
      | price          | 83.33                   |
      | price_tax_incl | 99.996                  |
    When I generate invoice for "bo_order1" order
    Then the product "Mug The best is yet to come" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2      |
      | product_price               | 83.33  |
      | original_product_price      | 11.90  |
      | unit_price_tax_incl         | 99.996 |
      | unit_price_tax_excl         | 83.33  |
      | total_price_tax_incl        | 199.99 |
      | total_price_tax_excl        | 166.66 |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have following details:
      | total_products           | 166.66 |
      | total_products_wt        | 199.99 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 168.66 |
      | total_paid_tax_incl      | 202.39 |
      | total_paid               | 202.39 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.40   |
    # Edit with values that are not strictly equals, then the specific price is recomputed with additional precision
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount         | 2                       |
      | price          | 83.33                   |
      | price_tax_incl | 100.00                  |
    # product_price is computed for backward compatibility which is why it is rounded (database value is correct though)
    And the product "Mug The best is yet to come" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2         |
      | product_price               | 83.33     |
      | original_product_price      | 11.90     |
      | unit_price_tax_incl         | 100       |
      | unit_price_tax_excl         | 83.333333 |
      | total_price_tax_incl        | 200       |
      | total_price_tax_excl        | 166.67    |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have following details:
      | total_products           | 166.67 |
      | total_products_wt        | 200.00 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 168.67 |
      | total_paid_tax_incl      | 202.40 |
      | total_paid               | 202.40 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.40   |
    # Now same thing with add product (with matching prices)
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name           | Mug The best is yet to come |
      | amount         | 2                           |
      | price          | 83.33                       |
      | price_tax_incl | 99.996                      |
    Then the product "Mug The best is yet to come" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2      |
      | product_price               | 83.33  |
      | original_product_price      | 11.90  |
      | unit_price_tax_incl         | 99.996 |
      | unit_price_tax_excl         | 83.33  |
      | total_price_tax_incl        | 199.99 |
      | total_price_tax_excl        | 166.66 |
    And the product "Mug The best is yet to come" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2      |
      | product_price               | 83.33  |
      | original_product_price      | 11.90  |
      | unit_price_tax_incl         | 99.996 |
      | unit_price_tax_excl         | 83.33  |
      | total_price_tax_incl        | 199.99 |
      | total_price_tax_excl        | 166.66 |
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have following details:
      | total_products           | 333.32 |
      | total_products_wt        | 399.98 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 335.32 |
      | total_paid_tax_incl      | 402.38 |
      | total_paid               | 402.38 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.40   |
    # Now again with add product (but prices don't match) We use 4 as quantity to avoid a round total with 6 or 7 products
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name           | Mug The best is yet to come |
      | amount         | 4                           |
      | price          | 83.33                       |
      | price_tax_incl | 100.00                      |
    And the product "Mug The best is yet to come" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2         |
      | product_price               | 83.33     |
      | original_product_price      | 11.90     |
      | unit_price_tax_incl         | 100       |
      | unit_price_tax_excl         | 83.333333 |
      | total_price_tax_incl        | 200       |
      | total_price_tax_excl        | 166.67    |
    And the product "Mug The best is yet to come" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2         |
      | product_price               | 83.33     |
      | original_product_price      | 11.90     |
      | unit_price_tax_incl         | 100       |
      | unit_price_tax_excl         | 83.333333 |
      | total_price_tax_incl        | 200       |
      | total_price_tax_excl        | 166.67    |
    And the product "Mug The best is yet to come" in the third invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 4         |
      | product_price               | 83.33     |
      | original_product_price      | 11.90     |
      | unit_price_tax_incl         | 100       |
      | unit_price_tax_excl         | 83.333333 |
      | total_price_tax_incl        | 400.00    |
      | total_price_tax_excl        | 333.33    |
    And order "bo_order1" should have 8 products in total
    And order "bo_order1" should have following details:
      | total_products           | 666.67 |
      | total_products_wt        | 800.00 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 668.67 |
      | total_paid_tax_incl      | 802.40 |
      | total_paid               | 802.40 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.40   |
