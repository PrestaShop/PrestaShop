# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-odd-tax
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-odd-tax
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO with odd TAX (21%)

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "FR" is enabled
    And I add new tax "odd-tax" with following properties:
      | name         | Odd Tax (21%) |
      | rate         | 21            |
      | is_enabled   | true          |
    And I add the tax rule group "odd-tax-group" for the tax "odd-tax" with the following conditions:
      | name         | Odd Tax (21%) |
      | country      | FR            |
    And there is a product in the catalog named "Test Product With Odd Tax" with a price of 7.80 and 100 items in stock
    And I set tax rule group "odd-tax-group" to product "Test Product With Odd Tax"
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "FR" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 70 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And a carrier "price_carrier" with name "My cheap carrier" exists
    And I enable carrier "price_carrier"
    And I associate the tax rule group "odd-tax-group" to carrier "price_carrier"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And cart "dummy_cart" should have "price_carrier" as a carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: I update product quantities only, the prices should not change
    Given order "bo_order1" does not have any invoices
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 70     |
      | product_price               | 7.80   |
      | original_product_price      | 7.80   |
      | unit_price_tax_incl         | 9.438  |
      | unit_price_tax_excl         | 7.80   |
      | total_price_tax_excl        | 546.00 |
      | total_price_tax_incl        | 660.66 |
    And order "bo_order1" should have 70 products in total
    And order "bo_order1" should have following details:
      | total_products           | 546.00 |
      | total_products_wt        | 660.66 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 548.00 |
      | total_paid_tax_incl      | 663.08 |
      | total_paid               | 663.08 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.42   |
    # Edit with two values that match (with computed tax values)
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount         | 80    |
      | price          | 7.80  |
      | price_tax_incl | 9.438 |
    Then product "Test Product With Odd Tax" in order "bo_order1" should have no specific price
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80     |
      | product_price               | 7.80   |
      | original_product_price      | 7.80   |
      | unit_price_tax_incl         | 9.438  |
      | unit_price_tax_excl         | 7.80   |
      | total_price_tax_excl        | 624.00 |
      | total_price_tax_incl        | 755.04 |
    And order "bo_order1" should have 80 products in total
    And order "bo_order1" should have following details:
      | total_products           | 624.00 |
      | total_products_wt        | 755.04 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 626.00 |
      | total_paid_tax_incl      | 757.46 |
      | total_paid               | 757.46 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.42   |
    # Edit with values that are not strictly equals, but price tax excluded is not different from the catalog price
    # so it is not recomputed
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount         | 80   |
      | price          | 7.80 |
      | price_tax_incl | 9.44 |
    Then product "Test Product With Odd Tax" in order "bo_order1" should have no specific price
    # product_price is computed for backward compatibility which is why it is rounded (database value is correct though)
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80     |
      | product_price               | 7.80   |
      | original_product_price      | 7.80   |
      | unit_price_tax_incl         | 9.438  |
      | unit_price_tax_excl         | 7.80   |
      | total_price_tax_excl        | 624.00 |
      | total_price_tax_incl        | 755.04 |
    And order "bo_order1" should have 80 products in total
    And order "bo_order1" should have following details:
      | total_products           | 624.00 |
      | total_products_wt        | 755.04 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 626.00 |
      | total_paid_tax_incl      | 757.46 |
      | total_paid               | 757.46 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.42   |

  Scenario: I update product quantities and prices, the price tax excluded is recomputed
    Given order "bo_order1" does not have any invoices
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 70     |
      | product_price               | 7.80   |
      | original_product_price      | 7.80   |
      | unit_price_tax_incl         | 9.438  |
      | unit_price_tax_excl         | 7.80   |
      | total_price_tax_excl        | 546.00 |
      | total_price_tax_incl        | 660.66 |
    And order "bo_order1" should have 70 products in total
    And order "bo_order1" should have following details:
      | total_products           | 546.00 |
      | total_products_wt        | 660.66 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 548.00 |
      | total_paid_tax_incl      | 663.08 |
      | total_paid               | 663.08 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.42   |
    # Edit with two values that match (with computed tax values)
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount         | 80    |
      | price          | 78.00 |
      | price_tax_incl | 94.38 |
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80      |
      | product_price               | 78.00   |
      | original_product_price      | 7.80    |
      | unit_price_tax_incl         | 94.38   |
      | unit_price_tax_excl         | 78.00   |
      | total_price_tax_excl        | 6240.00 |
      | total_price_tax_incl        | 7550.40 |
    And order "bo_order1" should have 80 products in total
    And order "bo_order1" should have following details:
      | total_products           | 6240.00 |
      | total_products_wt        | 7550.40 |
      | total_discounts_tax_excl | 0.00000 |
      | total_discounts_tax_incl | 0.00000 |
      | total_paid_tax_excl      | 6242.00 |
      | total_paid_tax_incl      | 7552.82 |
      | total_paid               | 7552.82 |
      | total_paid_real          | 0.0     |
      | total_shipping_tax_excl  | 2.0     |
      | total_shipping_tax_incl  | 2.42    |
    # Edit with values that are not strictly equals, so price tax excluded is recomputed with additional decimals
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount         | 80    |
      | price          | 78.02 |
      | price_tax_incl | 94.40 |
    # product_price is computed for backward compatibility which is why it is rounded (database value is correct though)
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80        |
      | product_price               | 78.02     |
      | original_product_price      | 7.80      |
      | unit_price_tax_incl         | 94.40     |
      | unit_price_tax_excl         | 78.016528 |
      | total_price_tax_excl        | 6241.32   |
      | total_price_tax_incl        | 7552.00   |
    And order "bo_order1" should have 80 products in total
    And order "bo_order1" should have following details:
      | total_products           | 6241.32 |
      | total_products_wt        | 7552.00 |
      | total_discounts_tax_excl | 0.00000 |
      | total_discounts_tax_incl | 0.00000 |
      | total_paid_tax_excl      | 6243.32 |
      | total_paid_tax_incl      | 7554.42 |
      | total_paid               | 7554.42 |
      | total_paid_real          | 0.0     |
      | total_shipping_tax_excl  | 2.0     |
      | total_shipping_tax_incl  | 2.42    |

  Scenario: I update product price with small difference
    Given order "bo_order1" does not have any invoices
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 70     |
      | product_price               | 7.80   |
      | original_product_price      | 7.80   |
      | unit_price_tax_incl         | 9.438  |
      | unit_price_tax_excl         | 7.80   |
      | total_price_tax_excl        | 546.00 |
      | total_price_tax_incl        | 660.66 |
    And order "bo_order1" should have 70 products in total
    And order "bo_order1" should have following details:
      | total_products           | 546.00 |
      | total_products_wt        | 660.66 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 548.00 |
      | total_paid_tax_incl      | 663.08 |
      | total_paid               | 663.08 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.42   |
    # Edit with two values that match (with computed tax values)
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount         | 80     |
      | price          | 7.85   |
      | price_tax_incl | 9.4985 |
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80     |
      | product_price               | 7.85   |
      | original_product_price      | 7.80   |
      | unit_price_tax_incl         | 9.4985 |
      | unit_price_tax_excl         | 7.85   |
      | total_price_tax_excl        | 628.00 |
      | total_price_tax_incl        | 759.88 |
    And order "bo_order1" should have 80 products in total
    And order "bo_order1" should have following details:
      | total_products           | 628.00 |
      | total_products_wt        | 759.88 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 630.00 |
      | total_paid_tax_incl      | 762.30 |
      | total_paid               | 762.30 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.42   |
    # Edit with values that are not strictly equals, and price tax excluded is different from the catalog price
    # so price tax excluded is recomputed
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount         | 80   |
      | price          | 7.85 |
      | price_tax_incl | 9.50 |
    # product_price is computed for backward compatibility which is why it is rounded (database value is correct though)
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80       |
      | product_price               | 7.85     |
      | original_product_price      | 7.80     |
      | unit_price_tax_incl         | 9.50     |
      | unit_price_tax_excl         | 7.851239 |
      | total_price_tax_excl        | 628.10   |
      | total_price_tax_incl        | 760.00   |
    And order "bo_order1" should have 80 products in total
    And order "bo_order1" should have following details:
      | total_products           | 628.10 |
      | total_products_wt        | 760.00 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 630.10 |
      | total_paid_tax_incl      | 762.42 |
      | total_paid               | 762.42 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.42   |
    # Edit with values that are not strictly equals, but price tax excluded is not different from the catalog price
    # so no specific price is computed
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount         | 80   |
      | price          | 7.44 |
      | price_tax_incl | 9.00 |
    # product_price is computed for backward compatibility which is why it is rounded (database value is correct though)
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80       |
      | product_price               | 7.44     |
      | original_product_price      | 7.80     |
      | unit_price_tax_incl         | 9.00     |
      | unit_price_tax_excl         | 7.438016 |
      | total_price_tax_excl        | 595.04   |
      | total_price_tax_incl        | 720.00   |
    And order "bo_order1" should have 80 products in total
    And order "bo_order1" should have following details:
      | total_products           | 595.04 |
      | total_products_wt        | 720.00 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 597.04 |
      | total_paid_tax_incl      | 722.42 |
      | total_paid               | 722.42 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.42   |
