# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-multi-shop
@reset-database-before-feature
@mock-context-on-scenario
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
      | original_product_price      | 11.90 |
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

  Scenario: Partial refund product from order
    When I update order "bo_order1" status to "Processing in progress"
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 32.650 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2 |
    And I watch the stock of product "Mug The best is yet to come"
    When I issue a partial refund on "bo_order1" with restock with credit slip without voucher on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 2                        | 7.5    |
    Then "bo_order1" has 1 credit slips
    Then "bo_order1" last credit slip is:
      | amount                  | 7.5  |
      | total_products_tax_excl | 7.5  |
      | total_products_tax_incl | 7.95 |
      | shipping_cost_amount    | 0.0  |
      | total_shipping_tax_incl | 0.0  |
      | total_shipping_tax_excl | 0.0  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2    |
      | product_quantity_refunded   | 2    |
      | product_quantity_reinjected | 2    |
      | total_refunded_tax_excl     | 7.5  |
      | total_refunded_tax_incl     | 7.95 |
    And there are 2 more "Mug The best is yet to come" in stock
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 32.650 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Return product from order
    When I update order "bo_order1" status to "Processing in progress"
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 32.650 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2 |
    And I watch the stock of product "Mug The best is yet to come"
    And return product is enabled
    When I issue a return product on "bo_order1" with restock with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
    Then "bo_order1" has 1 credit slips
    Then "bo_order1" last credit slip is:
      | amount                  | 11.9   |
      | shipping_cost_amount    | 0.0    |
      | total_shipping_tax_incl | 0.0    |
      | total_shipping_tax_excl | 0.0    |
      | total_products_tax_excl | 11.9   |
      | total_products_tax_incl | 12.610 |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_quantity_refunded   | 0     |
      | product_quantity_return     | 1     |
      | product_quantity_reinjected | 1     |
      | total_refunded_tax_excl     | 11.90 |
      | total_refunded_tax_incl     | 12.61 |
    And there is 1 more "Mug The best is yet to come" in stock
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 32.650 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Standard refund product from order
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 00.000 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2 |
    And I watch the stock of product "Mug The best is yet to come"
    # We add a payment to allow the standard refund, instead of changing its status to Payment accepted Because then it
    # would create an invoice which would update the shop context, and it wouldn't validate the bug we want to prevent
    And I pay order "bo_order1" with the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | currency       | USD                 |
      | amount         | 6.00                |
    And "bo_order1" has 0 credit slips
    And order "bo_order1" has status "Awaiting bank wire payment"
    And return product is enabled
    When I issue a standard refund on "bo_order1" with credit slip without voucher on following products:
      | product_name                | quantity |
      | Mug The best is yet to come | 1        |
    Then "bo_order1" has 1 credit slips
    Then "bo_order1" last credit slip is:
      | amount                  | 11.90 |
      | shipping_cost_amount    | 0.0   |
      | total_shipping_tax_excl | 0.0   |
      | total_shipping_tax_incl | 0.0   |
      | total_products_tax_excl | 11.90 |
      | total_products_tax_incl | 12.61 |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_quantity_refunded   | 1     |
      | product_quantity_reinjected | 1     |
      | total_refunded_tax_excl     | 11.90 |
      | total_refunded_tax_incl     | 12.61 |
    And there is 1 more "Mug The best is yet to come" in stock
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 6.00   |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: In All Shop Context, Update product in order
    # Create Shop Group & Shops
    When I add a shop group "shopGroup1" with name "Shop Group 1"
    And I add a shop "shop2" with name "Shop 2" and color "blue" for the group "Shop Group 1"
    And I copy "country" shop data from "test_shop" to "Shop 2"
    And I copy "currency" shop data from "test_shop" to "Shop 2"
    Then I should have 2 shop groups
    And I should have 1 shop in group "Default"
    And I should have 1 shop in group "Shop Group 1"
    # Create Products
    When shop context "test_shop" is loaded
    And there is a product in the catalog named "Product A" with a price of 12.3 and 0 items in stock
    And product "Product A" cannot be ordered out of stock
    Then the available stock for product "Product A" should be 0
    When shop context "Shop 2" is loaded
    And there is a product in the catalog named "Product B in Shop 2" with a price of 45.6 and 100 items in stock
    And product "Product B in Shop 2" cannot be ordered out of stock
    Then the available stock for product "Product B in Shop 2" should be 100
    # Create Customer
    # Context : Shop 2
    When there is a customer named "testCustomerShop2" whose email is "pubshop2@prestashop.com"
    And I add new address to customer "testCustomerShop2" with following details:
      | Address alias    | test-customer-address              |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    Then customer "testCustomerShop2" has address in "US" country
    # Create Order
    # Context : Shop 2
    When I create an empty cart "cart_product_B" for customer "testCustomerShop2"
    And I select "US" address as delivery and invoice address for customer "testCustomerShop2" in cart "cart_product_B"
    And I add 2 products "Product B in Shop 2" to the cart "cart_product_B"
    Then the available stock for product "Product B in Shop 2" should be 100
    When I add order "order_product_B" with the following details:
      | cart                | cart_product_B             |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then the available stock for product "Product B in Shop 2" should be 98
    And order "order_product_B" should contain 2 products "Product B in Shop 2"
    # Check Stock
    When shop context "Shop 2" is loaded
    Then the available stock for product "Product B in Shop 2" should be 98
    When shop context "test_shop" is loaded
    Then the available stock for product "Product A" should be 0
    # Change Context
    # Broken on develop branch
    When multiple shop context is loaded
    And I edit product "Product B in Shop 2" to order "order_product_B" with following products details:
      | amount        | 30                      |
      | price         | 78.90                   |
    Then I should get no error
