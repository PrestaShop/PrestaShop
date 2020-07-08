# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-from-bo
@reset-database-before-feature
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
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    #    todo: find a way to create country without legacy object
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Update order status
    When I update order "bo_order1" status to "Awaiting Cash On Delivery validation"
    Then order "bo_order1" has status "Awaiting Cash On Delivery validation"

  Scenario: Update order shipping details
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "2 - My carrier (Delivery next day!)"
    Then order "bo_order1" has Tracking number "TEST1234"
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

  Scenario: pay for order
    When I pay order "bo_order1" with the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | currency       | USD                 |
      | amount         | 6.00                |
    Then order "bo_order1" payments should have the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | amount         | $6.00               |

  Scenario: Change order state to Delivered to be able to add valid invoice to new Payment
    When order "bo_order1" has 0 payments
    And I update order "bo_order1" status to "Delivered"
    Then order "bo_order1" payments should have invoice

  Scenario: Duplicate order cart
    When I duplicate order "bo_order1" cart "dummy_cart" with reference "duplicated_dummy_cart"
    Then there is duplicated cart "duplicated_dummy_cart" for cart dummy_cart

  Scenario: Add product to an existing Order without invoice with free shipping and new invoice
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    Then order "bo_order1" should have 0 invoices
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 3 products "Mug Today is a good day"
    Then order "bo_order1" should have 0 invoices

  Scenario: Add product with specific price, add it again with different specific price The first price is kept
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
      | price         | 10                                |
      | free_shipping | true                              |
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
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 12                                |
      | free_shipping | true                              |
    Then order "bo_order1" should have 4 products in total
    Then order "bo_order1" should have following details:
      | total_products           | 43.800 |
      | total_products_wt        | 46.430 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 50.800 |
      | total_paid_tax_incl      | 53.850 |
      | total_paid               | 53.850 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product without specific price, add it again with different specific price The second specific price is used
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
      | free_shipping | true                              |
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
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 10                                |
      | free_shipping | true                              |
    Then order "bo_order1" should have 4 products in total
    Then order "bo_order1" should have following details:
      | total_products           | 43.800 |
      | total_products_wt        | 46.430 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 50.800 |
      | total_paid_tax_incl      | 53.850 |
      | total_paid               | 53.850 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

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
      | free_shipping | true                              |
    Then I should get error that product is out of stock
    Given shop configuration for "PS_ORDER_OUT_OF_STOCK" is set to 1
    # Use different price to be sure this one will be used
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Specific Price  |
      | amount        | 1                                 |
      | price         | 10                                |
      | free_shipping | true                              |
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

  Scenario: Add product to an existing Order with invoice with free shipping to new invoice
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoices
    And order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    Then order "bo_order1" should have 2 invoices

  Scenario: Add product to an existing Order with invoice with free shipping to last invoice
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoices
    And order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" to last invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    Then order "bo_order1" should have 1 invoices

  Scenario: Add product with negative quantity is forbidden
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | -1                      |
      | price         | 16                      |
      | free_shipping | true                    |
    Then I should get error that product quantity is invalid
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"

  Scenario: Add product with zero quantity is forbidden
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 2                       |
      | price         | 16                      |
      | free_shipping | true                    |
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | -1                      |
      | price         | 16                      |
      | free_shipping | true                    |
    Then I should get error that product quantity is invalid
    Then order "bo_order1" should contain 2 products "Mug Today is a good day"

  Scenario: Add product with quantity higher than stock is forbidden
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1500                    |
      | price         | 16                      |
      | free_shipping | true                    |
    Then I should get error that product is out of stock
    Then order "bo_order1" should contain 0 products "Mug Today is a good day"

  Scenario: Update product in order
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | 3                       |
      | price         | 12                      |
    Then order "bo_order1" should contain 3 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 3  |
      | product_price               | 12 |
      | unit_price_tax_incl         | 12 |
      | unit_price_tax_excl         | 12 |
      | total_price_tax_incl        | 36 |
      | total_price_tax_excl        | 36 |

  Scenario: Update product in order with zero quantity is forbidden
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | 0                       |
      | price         | 12                      |
    Then I should get error that product quantity is invalid
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.9   |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.9   |
      | total_price_tax_incl        | 25.230000 |
      | total_price_tax_excl        | 23.8   |

  Scenario: Update product in order with negative quantity is forbidden
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | -1                      |
      | price         | 12                      |
    Then I should get error that product quantity is invalid
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.9   |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.9   |
      | total_price_tax_incl        | 25.230000 |
      | total_price_tax_excl        | 23.8   |

  Scenario: Generating invoice for Order
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice

  Scenario: Add order from Back Office with free shipping
    And I create an empty cart "dummy_cart2" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart2"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart2"
    And I set Free shipping to the cart "dummy_cart2"
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart2         |
      | message             | test                |
      | payment module name | dummy_payment       |
      | status              | Payment accepted    |
    Then order "bo_order2" should have 2 products in total
    And order "bo_order2" should have free shipping
    And order "bo_order2" should have "dummy_payment" payment method

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
      | email            | test.davidsonas@invertus.eu        |
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

  @order-stock
  Scenario: Update product in order with the exact amount of stock
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Product Max Stock" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 80                      |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 20
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 products "Test Product Max Stock"
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 110                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    And the available stock for product "Test Product Max Stock" should be 20
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 products "Test Product Max Stock"
    # I can decrease the number in stock (note: 80 + 30 > 100 to check the available quantity considers the amount in the order)
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 30                     |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 70
    And order "bo_order1" should have 32 products in total
    And order "bo_order1" should contain 30 products "Test Product Max Stock"
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 100                     |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 0
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 products "Test Product Max Stock"
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 99                      |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 1
    And order "bo_order1" should have 101 products in total
    And order "bo_order1" should contain 99 products "Test Product Max Stock"
