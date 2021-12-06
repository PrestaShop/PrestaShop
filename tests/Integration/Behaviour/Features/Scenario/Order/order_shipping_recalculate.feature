# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-shipping-recalculate
@restore-all-tables-before-feature
@order-shipping-recalculate
@clear-cache-before-feature
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And country "FR" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And a carrier "price_carrier" with name "My cheap carrier" exists
    And a carrier "weight_carrier" with name "My light carrier" exists
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"

  Scenario: With PS_ORDER_RECALCULATE_SHIPPING = 1 & status = Awaiting bank wire payment, check the total price is recalculated
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 1
    And I enable carrier "weight_carrier"
    When I select carrier "default_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "default_carrier" as a carrier
    When I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 0 cart rule
    # Carrier less expensive is chosen by default
    And order "bo_order1" should have "default_carrier" as a carrier
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
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "weight_carrier"
    Then order "bo_order1" should have "weight_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 25.800 |
      | total_paid_tax_incl      | 27.350 |
      | total_paid               | 27.350 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.00   |
      | total_shipping_tax_incl  | 2.12   |
    Then order "bo_order1" has 0 payments

  Scenario: With PS_ORDER_RECALCULATE_SHIPPING = 1 & status = Processing in progress, check the total price is recalculated
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 1
    And I enable carrier "weight_carrier"
    And there is a product in the catalog named "Shipping Product" with a price of 15.0 and 100 items in stock
    And product "Shipping Product" weight is 0.63 kg
    And I add 1 products "Shipping Product" to the cart "dummy_cart"
    And I select carrier "weight_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "weight_carrier" as a carrier
    When I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 1 invoice
    And order "bo_order1" should have 0 cart rule
    # Carrier less expensive is chosen by default
    And order "bo_order1" should have "weight_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 43.800 |
      | total_paid_tax_incl      | 46.430 |
      | total_paid               | 46.430 |
      | total_paid_real          | 46.430 |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.30   |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "default_carrier"
    Then order "bo_order1" should have "default_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 46.430 |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Then order "bo_order1" has 1 payments
    And order "bo_order1" payment in first position should have the following details:
      | paymentMethod  | Dummy payment   |
      | transactionId  |                 |
      | amount         | $46.43          |
      | invoiceNumber  | #IN000001       |
    ## Change to paid order status
    When I update orders "bo_order1" statuses to "Shipped"
    Then order "bo_order1" has 2 payments
    And order "bo_order1" payment in first position should have the following details:
      | paymentMethod  | Dummy payment   |
      | transactionId  |                 |
      | amount         | $46.43          |
      | invoiceNumber  | #IN000001       |
    And order "bo_order1" payment in second position should have the following details:
      | paymentMethod  | Dummy payment   |
      | transactionId  |                 |
      | amount         | $2.12           |
      | invoiceNumber  | #IN000001       |

  Scenario: With PS_ORDER_RECALCULATE_SHIPPING = 0 & status = Awaiting bank wire payment, check the total price is not recalculated
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 0
    And I select carrier "default_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 0 cart rule
    # Carrier less expensive is chosen by default
    And order "bo_order1" should have "default_carrier" as a carrier
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
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "weight_carrier"
    Then order "bo_order1" should have "weight_carrier" as a carrier
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
    Then order "bo_order1" has 0 payments

  Scenario: With PS_ORDER_RECALCULATE_SHIPPING = 0 & status = Processing in progress, check the total price is recalculated
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 0
    And I enable carrier "weight_carrier"
    And there is a product in the catalog named "Shipping Product" with a price of 15.0 and 100 items in stock
    And product "Shipping Product" weight is 0.63 kg
    And I add 1 products "Shipping Product" to the cart "dummy_cart"
    And I select carrier "weight_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "weight_carrier" as a carrier
    When I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress     |
    Then order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 1 invoice
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 38.800 |
      | total_products_wt       | 41.130 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 43.800 |
      | total_paid_tax_incl     | 46.430 |
      | total_shipping_tax_excl | 5.0    |
      | total_shipping_tax_incl | 5.30   |
    And order "bo_order1" should have 0 cart rule
    # Carrier less expensive is chosen by default
    And order "bo_order1" should have "weight_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 43.800 |
      | total_paid_tax_incl      | 46.430 |
      | total_paid               | 46.430 |
      | total_paid_real          | 46.430 |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.30   |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "default_carrier"
    Then order "bo_order1" should have "default_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 43.800 |
      | total_paid_tax_incl      | 46.430 |
      | total_paid               | 46.430 |
      | total_paid_real          | 46.430 |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.30   |
    And order "bo_order1" should have 1 invoice
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 38.800 |
      | total_products_wt       | 41.130 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 43.800 |
      | total_paid_tax_incl     | 46.430 |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.30   |
    And order "bo_order1" has 1 payments
    And order "bo_order1" payment in first position should have the following details:
      | paymentMethod  | Dummy payment   |
      | transactionId  |                 |
      | amount         | $46.43          |
      | invoiceNumber  | #IN000002       |
    ## Change to paid order status
    When I update orders "bo_order1" statuses to "Shipped"
    Then order "bo_order1" should have 1 invoice
    And order "bo_order1" has 1 payments
    And order "bo_order1" payment in first position should have the following details:
      | paymentMethod  | Dummy payment   |
      | transactionId  |                 |
      | amount         | $46.43          |
      | invoiceNumber  | #IN000002       |
