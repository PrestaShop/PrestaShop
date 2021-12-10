# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-from-bo
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-from-bo
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  #  todo: fix the failing scenarios/code
  #  todo: make scenarios independent
  #  todo: change legacy classes with domain where possible
  #  todo: increase code re-use

  Background:
    Given email sending is disabled
    #    todo: improve context to accept EditableCurrency|ReferenceCurrency instead of legacy Currency object
    #    todo: use domain GetCurrencyForEditing|GetReferenceCurrency to add currency to context
    And the current currency is "USD"
    #    todo: use domain context for Country
    And country "US" is enabled
    And the module "dummy_payment" is installed
    #    todo: use domain context to get employee when is merged: https://github.com/PrestaShop/PrestaShop/pull/16757
    And I am logged in as "test@prestashop.com" employee
     #    todo: use domain context to get customer: GetCustomerForViewing;
     #    todo: find a way how to get customer object/id by its properties without using legacy objects
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    #    todo: find a way to create country without legacy object
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Check customer message can be null
    When I create an empty cart "dummy_cart2" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart2"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart2"
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart2                 |
      | message             |                             |
      | payment module name | dummy_payment               |
      | status              | Awaiting bank wire payment  |
    Then order "bo_order2" must have no customer message

  Scenario: Check customer message is saved when creating an order
    When I create an empty cart "dummy_cart2" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart2"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart2"
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart2                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order2" must have customer message with content "test"

  Scenario: Update order status
    When I update order "bo_order1" status to "Awaiting Cash On Delivery validation"
    Then order "bo_order1" has status "Awaiting Cash On Delivery validation"

  Scenario: Update order shipping details
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "default_carrier"
    Then order "bo_order1" has Tracking number "TEST1234"
    And order "bo_order1" should have "default_carrier" as a carrier
    And order "bo_order1" has Carrier "2 - My carrier (Delivery next day!)"

  Scenario: pay order with negative amount and see it is not valid
    When order "bo_order1" has 0 payments
    And I pay order "bo_order1" with the invalid following details:
      | date           | 2019-11-26 13:56:22 |
      | payment_method | Payments by check   |
      | transaction_id | test!@#$%%^^&* OR 1 |
      | currency       | USD                 |
      | amount         | -5.548              |
    Then I should get error that payment amount is negative
    And order "bo_order1" has 0 payments

  Scenario: pay order with invalid payment method and see it is not valid
    When order "bo_order1" has 0 payments
    And I pay order "bo_order1" with the invalid following details:
      | date           | 2019-11-26 13:56:22 |
      | payment_method | Paym>>ents by check |
      | transaction_id | test!@#$%%^^&* OR 1 |
      | currency       | USD                 |
      | amount         | 1.548               |
    Then I should get error that payment method is invalid
    And order "bo_order1" has 0 payments

  Scenario: pay for order
    When I pay order "bo_order1" with the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | currency       | USD                 |
      | amount         | 6.00                |
    Then order "bo_order1" payment in first position should have the following details:
      | date           | 2019-11-26 13:56:23 |
      | paymentMethod  | Payments by check   |
      | transactionId  | test123             |
      | amount         | $6.00               |
    And order "bo_order1" should have following details:
      | total_products           | 23.80 |
      | total_products_wt        | 25.23 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 30.80 |
      | total_paid_tax_incl      | 32.65 |
      | total_paid               | 32.65 |
      | total_paid_real          | 6.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |

  Scenario: Change order state to Delivered to be able to add valid invoice to new Payment
    When order "bo_order1" has 0 payments
    And I update order "bo_order1" status to "Delivered"
    Then order "bo_order1" payments should have invoice

  Scenario: Duplicate order cart
    When I duplicate order "bo_order1" cart "dummy_cart" with reference "duplicated_dummy_cart"
    Then there is duplicated cart "duplicated_dummy_cart" for cart dummy_cart

  Scenario: Add product to an existing Order without invoice without free shipping and new invoice
    Given there is a product in the catalog named "Test Added Product" with a price of 15.0 and 100 items in stock
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

  # This test validates the out of stock behaviour and a bug that occured when a specific price had been set
  Scenario: Add product with specific price without stock, get error, allow out of stock order and retry, it should work (no unicity error)
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
    Given there is a product in the catalog named "Test Product With Specific Price" with a price of 15.0 and 0 items in stock
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 8                                 |
    Then I should get error that product is out of stock
    Given shop configuration for "PS_ORDER_OUT_OF_STOCK" is set to 1
    # Use different price to be sure this one will be used
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 10                                |
    # This is to avoid regression, previously a specific price was added but not cleared and it caused an unexpected bug
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should have following details:
      | total_products           | 33.800 |
      | total_products_wt        | 35.830 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 40.8   |
      | total_paid_tax_incl      | 43.250 |
      | total_paid               | 43.250 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product to an existing Order with invoice without free shipping to new invoice
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoice
    And order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    And order "bo_order1" should have 2 invoices
    And the product "Mug Today is a good day" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 2     |
      | product_price               | 16.00 |
      | original_product_price      | 11.90 |
      | unit_price_tax_incl         | 16.96 |
      | unit_price_tax_excl         | 16.00 |
      | total_price_tax_incl        | 33.92 |
      | total_price_tax_excl        | 32.00 |
    And order "bo_order1" should have following details:
      | total_products           | 55.80 |
      | total_products_wt        | 59.15 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 62.80 |
      | total_paid_tax_incl      | 66.57 |
      | total_paid               | 66.57 |
      | total_paid_real          | 32.65 |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |

  Scenario: Add product to an existing Order with invoice without free shipping to last invoice
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoice
    And order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" to the last invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    And order "bo_order1" should have 1 invoice
    And order "bo_order1" should have following details:
      | total_products           | 55.80 |
      | total_products_wt        | 59.15 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 62.80 |
      | total_paid_tax_incl      | 66.57 |
      | total_paid               | 66.57 |
      | total_paid_real          | 32.65 |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |

  Scenario: Add product with negative quantity is forbidden
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | -1                      |
      | price         | 16                      |
    Then I should get error that product quantity is invalid for order
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"

  Scenario: Add product with zero quantity is forbidden
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | -1                      |
      | price         | 16                      |
    Then I should get error that product quantity is invalid for order
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"

  Scenario: Add product with quantity higher than stock is forbidden
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1500                    |
      | price         | 16                      |
    Then I should get error that product is out of stock
    Then order "bo_order1" should contain 0 products "Mug Today is a good day"

  Scenario: Delete product from order
    Given there is a product in the catalog named "Test Added Product" with a price of 15.0 and 100 items in stock
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

  Scenario: Update product in order
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | 3                       |
      | price         | 11.90                   |
    Then order "bo_order1" should contain 3 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" should have no specific price
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 3      |
      | product_price               | 11.90  |
      | original_product_price      | 11.90  |
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

  Scenario: Update quantity of customized product
    When I create an empty cart "dummy_cart3" for customer "testCustomer"
    And I add 1 customized products with reference "demo_14" with all its customizations to the cart "dummy_cart3"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart3"
    And I add order "bo_order_custo" with the following details:
      | cart                | dummy_cart3         |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    Then order "bo_order_custo" should have 1 products in total
    When I edit product "Customizable Mug" to order "bo_order_custo" with following products details:
      | amount        | 3                       |
      | price         | 10                      |
    Then order "bo_order_custo" should have 3 products in total
    When I edit product "Customizable Mug" to order "bo_order_custo" with following products details:
      | amount        | 4                       |
      | price         | 10                      |
    Then order "bo_order_custo" should have 4 products in total

  Scenario: Update product in order with zero quantity is forbidden
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | 0                       |
      | price         | 12                      |
    Then I should get error that product quantity is invalid for order
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.9   |
      | original_product_price      | 11.90  |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.9   |
      | total_price_tax_incl        | 25.230000 |
      | total_price_tax_excl        | 23.8   |
    And order "bo_order1" should have following details:
      | total_products           | 23.80 |
      | total_products_wt        | 25.23 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 30.80 |
      | total_paid_tax_incl      | 32.65 |
      | total_paid               | 32.65 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |

  Scenario: Update product in order with negative quantity is forbidden
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | -1                      |
      | price         | 12                      |
    Then I should get error that product quantity is invalid for order
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.9   |
      | original_product_price      | 11.90  |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.9   |
      | total_price_tax_incl        | 25.23  |
      | total_price_tax_excl        | 23.8   |
    And order "bo_order1" should have following details:
      | total_products           | 23.80 |
      | total_products_wt        | 25.23 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 30.80 |
      | total_paid_tax_incl      | 32.65 |
      | total_paid               | 32.65 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |

  Scenario: Add different combinations of the same product
    Given there is a product in the catalog named "My Product" with a price of 10.00 and 200 items in stock
    And product "My Product" has combinations with following details:
      | reference    | quantity | attributes |
      | combination1 | 100      | Size:L     |
      | combination2 | 100      | Size:M     |
    Then the available stock for combination "combination1" of product "My Product" should be 100
    And the available stock for combination "combination2" of product "My Product" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | My Product    |
      | combination   | combination1  |
      | amount        | 1             |
      | price         | 10            |
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | My Product    |
      | combination   | combination2  |
      | amount        | 1             |
      | price         | 10            |
    Then order "bo_order1" should contain 1 combination "combination1" of product "My Product"
    And order "bo_order1" should contain 1 combination "combination2" of product "My Product"

  Scenario: Generating invoice for Order
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice

  Scenario: Add gift order from Back Office without free shipping
    And I create an empty cart "dummy_cart2" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart2"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart2"
    And I set Free shipping to the cart "dummy_cart2"
    And I declare cart "dummy_cart2" is a gift with message "Some Xmas love"
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart2         |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    Then order "bo_order2" should have 2 products in total
    And order "bo_order2" should have free shipping
    And order "bo_order2" should have "dummy_payment" payment method
    And order "bo_order2" should be a gift with message "Some Xmas love"

  Scenario: Update multiple orders statuses using Bulk actions
    And I create an empty cart "dummy_cart3" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart3"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart3"
    And I set Free shipping to the cart "dummy_cart3"
    And I add order "bo_order3" with the following details:
      | cart                | dummy_cart3         |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    When I update orders "bo_order1,bo_order3" statuses to "Delivered"
    Then order "bo_order1" has status "Delivered"
    Then order "bo_order2" has status "Payment accepted"
    And order "bo_order3" has status "Delivered"

  Scenario: Change order shipping address
    Given I create customer "testFirstName" with following details:
      | firstName        | testFirstName                      |
      | lastName         | testLastName                       |
      | email            | test@mailexample.eu                |
      | password         | secret                             |
    When I add new address to customer "testFirstName" with following details:
      | Address alias    | test-address                       |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    When I change order "bo_order1" shipping address to "test-address"
    Then order "bo_order1" shipping address should be "test-address"

  Scenario: Change order internal note
    When I change order "bo_order1" note to "Test note."
    Then order "bo_order1" note should be "Test note."
    When I change order "bo_order1" note to ""
    Then order "bo_order1" note should be ""

  Scenario: Edit a product that doesn't exist in catalogue anymore
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 10                      |
    And I delete product "Mug Today is a good day" from catalogue
    And I update deleted product "Mug Today is a good day" in order "bo_order1"
    Then I should get error that the product being edited was not found

  Scenario: Generate order then modify product price then add same product on another invoice and check the price
    Given order "bo_order1" does not have any invoices
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.90  |
      | original_product_price      | 11.90  |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.90  |
      | total_price_tax_incl        | 25.23  |
      | total_price_tax_excl        | 23.80  |
    # The price included and excluded don't match exactly so the price excluded is gonna be recomputed with more details
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount         | 1                       |
      | price          | 94.34                   |
      | price_tax_incl | 100                     |
    When I generate invoice for "bo_order1" order
    # product_price is computed for backward compatibility which is why it is rounded (database value is correct though)
    Then the product "Mug The best is yet to come" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1         |
      | product_price               | 94.34     |
      | original_product_price      | 11.90     |
      | unit_price_tax_incl         | 100.00    |
      | unit_price_tax_excl         | 94.339622 |
      | total_price_tax_incl        | 100       |
      | total_price_tax_excl        | 94.34     |
    And order "bo_order1" should have 1 products in total
    And order "bo_order1" should have following details:
      | total_products           | 94.34  |
      | total_products_wt        | 100.00 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 101.34 |
      | total_paid_tax_incl      | 107.42 |
      | total_paid               | 107.42 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name           | Mug The best is yet to come |
      | amount         | 1                           |
      | price          | 94.34                       |
      | price_tax_incl | 100                         |
    Then the product "Mug The best is yet to come" in the first invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1         |
      | product_price               | 94.34     |
      | original_product_price      | 11.90     |
      | unit_price_tax_incl         | 100.00    |
      | unit_price_tax_excl         | 94.339622 |
      | total_price_tax_incl        | 100       |
      | total_price_tax_excl        | 94.34     |
    And the product "Mug The best is yet to come" in the second invoice from the order "bo_order1" should have the following details:
      | product_quantity            | 1         |
      | product_price               | 94.34     |
      | original_product_price      | 11.90     |
      | unit_price_tax_incl         | 100.00    |
      | unit_price_tax_excl         | 94.339622 |
      | total_price_tax_incl        | 100       |
      | total_price_tax_excl        | 94.34     |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have following details:
      | total_products           | 188.68 |
      | total_products_wt        | 200.00 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 195.68 |
      | total_paid_tax_incl      | 207.42 |
      | total_paid               | 207.42 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Delete all products from order
    Given there is a product in the catalog named "Test Added Product" with a price of 15.0 and 100 items in stock
    And order with reference "bo_order1" does not contain product "Test Added Product"
    And the available stock for product "Test Added Product" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Added Product |
      | amount        | 2                  |
      | price         | 15                 |
    And I generate invoice for "bo_order1" order
    Then order "bo_order1" should contain 2 products "Test Added Product"
    And product "Test Added Product" in order "bo_order1" should have no specific price
    And the available stock for product "Test Added Product" should be 98
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 1 invoice
    And product "Test Added Product" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | product_price               | 15.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_excl         | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | total_price_tax_excl        | 30.00 |
      | total_price_tax_incl        | 31.80 |
    And order "bo_order1" should have following details:
      | total_products           | 53.80 |
      | total_products_wt        | 57.03 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 60.80 |
      | total_paid_tax_incl      | 64.45 |
      | total_paid               | 64.45 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 53.80 |
      | total_products_wt       | 57.03 |
      | total_discount_tax_excl | 0.0   |
      | total_discount_tax_incl | 0.0   |
      | total_paid_tax_excl     | 60.80 |
      | total_paid_tax_incl     | 64.45 |
      | total_shipping_tax_excl | 7.0   |
      | total_shipping_tax_incl | 7.42  |
    Then order "bo_order1" has status "Awaiting bank wire payment"
    And order "bo_order1" has 1 status in history
    When I remove product "Test Added Product" from order "bo_order1"
    And order "bo_order1" should contain 0 product "Test Added Product"
    And cart of order "bo_order1" should contain 0 product "Test Added Product"
    And order "bo_order1" should have following details:
      | total_products           | 23.80 |
      | total_products_wt        | 25.23 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 30.80 |
      | total_paid_tax_incl      | 32.65 |
      | total_paid               | 32.65 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.80 |
      | total_products_wt       | 25.23 |
      | total_discount_tax_excl | 0.0   |
      | total_discount_tax_incl | 0.0   |
      | total_paid_tax_excl     | 30.80 |
      | total_paid_tax_incl     | 32.65 |
      | total_shipping_tax_excl | 7.0   |
      | total_shipping_tax_incl | 7.42  |
    When I remove product "Mug The best is yet to come" from order "bo_order1"
    Then product "Test Added Product" in order "bo_order1" should have no specific price
    And order "bo_order1" should have 0 products in total
    And order "bo_order1" should contain 0 product "Mug The best is yet to come"
    And cart of order "bo_order1" should contain 0 product "Mug The best is yet to come"
    And the available stock for product "Test Added Product" should be 100
    And order "bo_order1" should have following details:
      | total_products           | 0.000 |
      | total_products_wt        | 0.000 |
      | total_discounts_tax_excl | 0.0   |
      | total_discounts_tax_incl | 0.0   |
      | total_paid_tax_excl      | 0.000 |
      | total_paid_tax_incl      | 0.000 |
      | total_paid               | 0.000 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 0.000 |
      | total_shipping_tax_incl  | 0.000 |
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 0.000 |
      | total_products_wt       | 0.000 |
      | total_discount_tax_excl | 0.0   |
      | total_discount_tax_incl | 0.0   |
      | total_paid_tax_excl     | 0.000 |
      | total_paid_tax_incl     | 0.000 |
      | total_shipping_tax_excl | 0.000 |
      | total_shipping_tax_incl | 0.000 |
    Then order "bo_order1" has status "Awaiting bank wire payment"
    And order "bo_order1" has 1 status in history

  Scenario: Delete all products from order then add product in the empty order
    Given there is a product in the catalog named "Test Added Product" with a price of 15.0 and 100 items in stock
    And order with reference "bo_order1" does not contain product "Test Added Product"
    And the available stock for product "Test Added Product" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Added Product |
      | amount        | 2                  |
      | price         | 15                 |
    # Remove products
    When I remove product "Test Added Product" from order "bo_order1"
    And I remove product "Mug The best is yet to come" from order "bo_order1"
    Then order "bo_order1" should have 0 products in total
    # Add again a product
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Added Product |
      | amount | 2                  |
      | price  | 15                 |
    And I generate invoice for "bo_order1" order
    ## Assert totals
    And product "Test Added Product" in order "bo_order1" has following details:
      | product_quantity       | 2     |
      | product_price          | 15.00 |
      | original_product_price | 15.00 |
      | unit_price_tax_excl    | 15.00 |
      | unit_price_tax_incl    | 15.90 |
      | total_price_tax_excl   | 30.00 |
      | total_price_tax_incl   | 31.80 |
    And order "bo_order1" should have following details:
      | total_products           | 30.00 |
      | total_products_wt        | 31.80 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 30.00 |
      | total_products_wt       | 31.80 |
      | total_discount_tax_excl | 0.0   |
      | total_discount_tax_incl | 0.0   |
      | total_shipping_tax_excl | 7.0   |
      | total_shipping_tax_incl | 7.42  |
    And order "bo_order1" should have following tax details:
      | unit_tax_base | total_tax_base | unit_amount | total_amount |
      | 15.00         | 30.0           | 0.9         | 1.8          |
    And the first invoice from order "bo_order1" should have following product tax details:
      | total_price_tax_excl | rate | total_amount |
      | 30.0                 | 6.00 | 1.8          |
    And the first invoice from order "bo_order1" should have following shipping tax details:
      | total_tax_excl | rate | total_amount |
      | 7              | 6.00 | 0.42         |

  Scenario: View order created on a currently deleted language
    Given language "Spanish" with locale "es-ES" exists
    And language with iso code "es" is the default one
    When I create an empty cart "dummy_cart-ES" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart-ES"
    And I add 1 product "Mug The best is yet to come" to the cart "dummy_cart-ES"
    And I add order "bo_order-ES" with the following details:
      | cart                | dummy_cart-ES               |
      | message             |                             |
      | payment module name | dummy_payment               |
      | status              | Awaiting bank wire payment  |
    And I change order "bo_order-ES" shipping address to "test-address"
    Then order "bo_order-ES" shipping address should be "test-address"
    And order "bo_order-ES" preview shipping address should have the following details:
      # country is not translated here but should be on the develop branch (https://github.com/PrestaShop/PrestaShop/pull/19818)
      | country | United States |
    When language with iso code "en" is the default one
    And I delete language "Spanish"
    Then order "bo_order-ES" shipping address should be "test-address"
    And order "bo_order-ES" preview shipping address should have the following details:
      | country | United States |

  Scenario: Add same customizable product to an order with new invoice
    When I create an empty cart "dummy_cart_custo" for customer "testCustomer"
    And I add 1 customized products with reference "demo_14" with all its customizations to the cart "dummy_cart_custo"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart_custo"
    And I add order "bo_order_custo" with the following details:
      | cart                | dummy_cart_custo         |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    Then order "bo_order_custo" should have 1 products in total
    When I add products to order "bo_order_custo" with new invoice and the following products details:
      | name          | Customizable Mug        |
      | amount        | 1                       |
      | price         | 10                      |
    Then order "bo_order_custo" should have 2 products in total
