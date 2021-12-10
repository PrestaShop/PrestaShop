# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-shipping
@restore-all-tables-before-feature
@order-shipping
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

  Scenario: Use a carrier that depends on price, add product to change order total the shipping price should update as well
    Given I select carrier "price_carrier" for cart "dummy_cart"
    Then I should get error that carrier is invalid
    Given I enable carrier "price_carrier"
    And I select carrier "price_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "price_carrier" as a carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have "price_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 29.800 |
      | total_paid_tax_incl      | 31.590 |
      | total_paid               | 31.590 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 6.0    |
      | total_shipping_tax_incl  | 6.36   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 6.00  |
      | shipping_cost_tax_incl | 6.36  |
    Given there is a product in the catalog named "Shipping Product" with a price of 15.0 and 100 items in stock
    And product "Shipping Product" weight is 0.63 kg
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Shipping Product |
      | amount        | 2                |
      | price         | 15               |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 57.8   |
      | total_paid_tax_incl      | 61.270 |
      | total_paid               | 61.270 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 4.0    |
      | total_shipping_tax_incl  | 4.24   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 1.860 |
      | shipping_cost_tax_excl | 4.00  |
      | shipping_cost_tax_incl | 4.24  |
    When I edit product "Shipping Product" to order "bo_order1" with following products details:
      | amount        | 6                       |
      | price         | 15                      |
    Then order "bo_order1" should have 8 products in total
    And order "bo_order1" should have following details:
      | total_products           | 113.80 |
      | total_products_wt        | 120.63 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.12   |
      | total_paid_tax_excl      | 115.80 |
      | total_paid_tax_incl      | 122.75 |
      | total_paid               | 122.75 |
      | total_paid_real          | 0.0    |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 4.380 |
      | shipping_cost_tax_excl | 2.00  |
      | shipping_cost_tax_incl | 2.12  |
    When I edit product "Shipping Product" to order "bo_order1" with following products details:
      | amount        | 6                       |
      | price         | 5                       |
    Then order "bo_order1" should have 8 products in total
    And order "bo_order1" should have following details:
      | total_products           | 53.80  |
      | total_products_wt        | 57.03  |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_shipping_tax_excl  | 4.0    |
      | total_shipping_tax_incl  | 4.24   |
      | total_paid_tax_excl      | 57.80  |
      | total_paid_tax_incl      | 61.27  |
      | total_paid               | 61.27  |
      | total_paid_real          | 0.0    |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 4.380 |
      | shipping_cost_tax_excl | 4.00  |
      | shipping_cost_tax_incl | 4.24  |

  Scenario: Use a carrier that depends on weight, add product to change order total the shipping price should update as well
    Given I select carrier "weight_carrier" for cart "dummy_cart"
    Then I should get error that carrier is invalid
    Given I enable carrier "weight_carrier"
    And I select carrier "weight_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "weight_carrier" as a carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have "weight_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 25.800 |
      | total_paid_tax_incl      | 27.350 |
      | total_paid               | 27.350 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.12   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 2.00  |
      | shipping_cost_tax_incl | 2.12  |
    Given there is a product in the catalog named "Shipping Product" with a price of 15.0 and 100 items in stock
    And product "Shipping Product" weight is 0.63 kg
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Shipping Product |
      | amount        | 2                |
      | price         | 15               |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 58.8   |
      | total_paid_tax_incl      | 62.330 |
      | total_paid               | 62.330 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.30   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 1.860 |
      | shipping_cost_tax_excl | 5.00  |
      | shipping_cost_tax_incl | 5.30  |
    When I edit product "Shipping Product" to order "bo_order1" with following products details:
      | amount        | 6                       |
      | price         | 15                      |
    Then order "bo_order1" should have 8 products in total
    And order "bo_order1" should have following details:
      | total_products           | 113.80 |
      | total_products_wt        | 120.63 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_shipping_tax_excl  | 8.0    |
      | total_shipping_tax_incl  | 8.48   |
      | total_paid_tax_excl      | 121.80 |
      | total_paid_tax_incl      | 129.11 |
      | total_paid               | 129.11 |
      | total_paid_real          | 0.0    |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 4.380 |
      | shipping_cost_tax_excl | 8.00  |
      | shipping_cost_tax_incl | 8.48  |
    When I edit product "Shipping Product" to order "bo_order1" with following products details:
      | amount        | 6                       |
      | price         | 5                       |
    Then order "bo_order1" should have 8 products in total
    And order "bo_order1" should have following details:
      | total_products           | 53.80  |
      | total_products_wt        | 57.03  |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_shipping_tax_excl  | 8.0    |
      | total_shipping_tax_incl  | 8.48   |
      | total_paid_tax_excl      | 61.80  |
      | total_paid_tax_incl      | 65.51  |
      | total_paid               | 65.51  |
      | total_paid_real          | 0.0    |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 4.380 |
      | shipping_cost_tax_excl | 8.00  |
      | shipping_cost_tax_incl | 8.48  |

  Scenario: Use default carrier, then update the order carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 0 cart rule
    # Carrier less expensive is chosen by default
    And order "bo_order1" should have "weight_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 25.800 |
      | total_paid_tax_incl      | 27.350 |
      | total_paid               | 27.350 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.12   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 2.00  |
      | shipping_cost_tax_incl | 2.12  |
    Given there is a product in the catalog named "Shipping Product" with a price of 15.0 and 100 items in stock
    And product "Shipping Product" weight is 0.63 kg
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Shipping Product |
      | amount        | 2                |
      | price         | 15               |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 58.8   |
      | total_paid_tax_incl      | 62.330 |
      | total_paid               | 62.330 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.30   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 1.860 |
      | shipping_cost_tax_excl | 5.00  |
      | shipping_cost_tax_incl | 5.30  |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "default_carrier"
    Then cart "dummy_cart" should have "default_carrier" as a carrier
    And order "bo_order1" should have "default_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 60.8   |
      | total_paid_tax_incl      | 64.450 |
      | total_paid               | 64.450 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 1.860 |
      | shipping_cost_tax_excl | 7.00  |
      | shipping_cost_tax_incl | 7.42  |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "price_carrier"
    Then cart "dummy_cart" should have "price_carrier" as a carrier
    And order "bo_order1" should have "price_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 57.8   |
      | total_paid_tax_incl      | 61.270 |
      | total_paid               | 61.270 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 4.0    |
      | total_shipping_tax_incl  | 4.24   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 1.860 |
      | shipping_cost_tax_excl | 4.00  |
      | shipping_cost_tax_incl | 4.24  |

  Scenario: When shipping recalculate is disabled the shipping is not updated
    Given shop configuration for "PS_ORDER_RECALCULATE_SHIPPING" is set to 0
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I add discount to order "bo_order1" with following details:
      | name        | FreeShippingAndAmount |
      | type        | free_shipping         |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 1 cart rule
    # Carrier less expensive is chosen by default
    And order "bo_order1" should have "weight_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 2.0    |
      | total_discounts_tax_incl | 2.12   |
      | total_paid_tax_excl      | 23.800 |
      | total_paid_tax_incl      | 25.230 |
      | total_paid               | 25.230 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.12   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 2.00  |
      | shipping_cost_tax_incl | 2.12  |
    Given there is a product in the catalog named "Shipping Product" with a price of 15.0 and 100 items in stock
    And product "Shipping Product" weight is 0.63 kg
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Shipping Product |
      | amount        | 2                |
      | price         | 15               |
    Then order "bo_order1" should have 4 products in total
    And order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 2.0000 |
      | total_discounts_tax_incl | 2.1200 |
      | total_paid_tax_excl      | 53.8   |
      | total_paid_tax_incl      | 57.030 |
      | total_paid               | 57.030 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.12   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 1.860 |
      | shipping_cost_tax_excl | 2.00  |
      | shipping_cost_tax_incl | 2.12  |
    When I edit product "Shipping Product" to order "bo_order1" with following products details:
      | amount        | 6                       |
      | price         | 5                       |
    Then order "bo_order1" should have 8 products in total
    And order "bo_order1" should have following details:
      | total_products           | 53.80  |
      | total_products_wt        | 57.03  |
      | total_discounts_tax_excl | 2.0000 |
      | total_discounts_tax_incl | 2.1200 |
      | total_paid_tax_excl      | 53.80  |
      | total_paid_tax_incl      | 57.03  |
      | total_paid               | 57.03  |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.12   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 4.380 |
      | shipping_cost_tax_excl | 2.00  |
      | shipping_cost_tax_incl | 2.12  |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "default_carrier"
    Then order "bo_order1" should have "default_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 53.80  |
      | total_products_wt        | 57.03  |
      | total_discounts_tax_excl | 2.0000 |
      | total_discounts_tax_incl | 2.1200 |
      | total_paid_tax_excl      | 53.80  |
      | total_paid_tax_incl      | 57.03  |
      | total_paid               | 57.03  |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.12   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 4.380 |
      | shipping_cost_tax_excl | 2.00  |
      | shipping_cost_tax_incl | 2.12  |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "price_carrier"
    Then order "bo_order1" should have "price_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 53.80  |
      | total_products_wt        | 57.03  |
      | total_discounts_tax_excl | 2.0000 |
      | total_discounts_tax_incl | 2.1200 |
      | total_paid_tax_excl      | 53.80  |
      | total_paid_tax_incl      | 57.03  |
      | total_paid               | 57.03  |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.12   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 4.380 |
      | shipping_cost_tax_excl | 2.00  |
      | shipping_cost_tax_incl | 2.12  |

  Scenario: I change the customer invoice address to another zone and check that shipping fees have been updated
    Given shop configuration for "PS_TAX_ADDRESS_TYPE" is set to id_address_invoice
    Given I enable carrier "price_carrier"
    And I select carrier "price_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "price_carrier" as a carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I add discount to order "bo_order1" with following details:
      | name      | FreeShippingDiscount |
      | type      | free_shipping        |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "FreeShippingDiscount" with amount "$6.00"
    And order "bo_order1" should have "price_carrier" as a carrier
    # Shipping cost is 4 (for US zone) + 2 (general fee)
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 6.0    |
      | total_discounts_tax_incl | 6.36   |
      | total_paid_tax_excl      | 23.800 |
      | total_paid_tax_incl      | 25.230 |
      | total_paid               | 25.230 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 6.0    |
      | total_shipping_tax_incl  | 6.36   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 6.00  |
      | shipping_cost_tax_incl | 6.36  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 25.230 |
      | total_price_tax_excl        | 23.800 |
    When I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-france-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
    And I change order "bo_order1" shipping address to "test-customer-france-address"
    Then order "bo_order1" shipping address should be "test-customer-france-address"
    # Shipping cost changes because we are not in the same zone but the tax is still the one from invoice address
    # Shipping cost is 3 (for Europe zone) + 2 (general fee)
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 5.0    |
      | total_discounts_tax_incl | 5.30   |
      | total_paid_tax_excl      | 23.800 |
      | total_paid_tax_incl      | 25.230 |
      | total_paid               | 25.230 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.30   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 5.00  |
      | shipping_cost_tax_incl | 5.30  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 25.230 |
      | total_price_tax_excl        | 23.800 |
    And order "bo_order1" should have cart rule "FreeShippingDiscount" with amount "$5.00"
    When I change order "bo_order1" invoice address to "test-customer-france-address"
    Then order "bo_order1" invoice address should be "test-customer-france-address"
    # Shipping fees use invoice address so the shipping fees should be reduced now
    # (no tax applied because France tax rules are not installed)
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 23.800 |
      | total_discounts_tax_excl | 5.0    |
      | total_discounts_tax_incl | 5.0    |
      | total_paid_tax_excl      | 23.800 |
      | total_paid_tax_incl      | 23.800 |
      | total_paid               | 23.800 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.00   |
      | carrier_tax_rate         | 0.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 5.00  |
      | shipping_cost_tax_incl | 5.00  |
    And order "bo_order1" should have cart rule "FreeShippingDiscount" with amount "$5.00"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 11.900 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 23.800 |
      | total_price_tax_excl        | 23.800 |

  Scenario: I change the customer delivery address to another zone and check that shipping fees have been updated
    Given shop configuration for "PS_TAX_ADDRESS_TYPE" is set to id_address_delivery
    Given I enable carrier "price_carrier"
    And I select carrier "price_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "price_carrier" as a carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I add discount to order "bo_order1" with following details:
      | name      | FreeShippingDiscount |
      | type      | free_shipping        |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have cart rule "FreeShippingDiscount" with amount "$6.00"
    And order "bo_order1" should have "price_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 6.0    |
      | total_discounts_tax_incl | 6.36   |
      | total_paid_tax_excl      | 23.800 |
      | total_paid_tax_incl      | 25.230 |
      | total_paid               | 25.230 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 6.0    |
      | total_shipping_tax_incl  | 6.36   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 6.00  |
      | shipping_cost_tax_incl | 6.36  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 25.230 |
      | total_price_tax_excl        | 23.800 |
    When I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-france-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
    And I change order "bo_order1" invoice address to "test-customer-france-address"
    Then order "bo_order1" invoice address should be "test-customer-france-address"
    # Shipping fees use delivery address so changing the invoice address should not modify them nor the taxes
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 6.0    |
      | total_discounts_tax_incl | 6.36   |
      | total_paid_tax_excl      | 23.800 |
      | total_paid_tax_incl      | 25.230 |
      | total_paid               | 25.230 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 6.0    |
      | total_shipping_tax_incl  | 6.36   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 6.00  |
      | shipping_cost_tax_incl | 6.36  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 25.230 |
      | total_price_tax_excl        | 23.800 |
    And order "bo_order1" should have cart rule "FreeShippingDiscount" with amount "$6.00"
    When I change order "bo_order1" shipping address to "test-customer-france-address"
    Then order "bo_order1" shipping address should be "test-customer-france-address"
    # Shipping fees use delivery address so the shipping fees should be reduced now, the fee and tax changes
    # (no tax applied because France tax rules are not installed)
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 23.800 |
      | total_discounts_tax_excl | 5.0    |
      | total_discounts_tax_incl | 5.0    |
      | total_paid_tax_excl      | 23.800 |
      | total_paid_tax_incl      | 23.800 |
      | total_paid               | 23.800 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.00   |
      | carrier_tax_rate         | 0.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 5.00  |
      | shipping_cost_tax_incl | 5.00  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 11.900 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 23.800 |
      | total_price_tax_excl        | 23.800 |
    And order "bo_order1" should have cart rule "FreeShippingDiscount" with amount "$5.00"

  Scenario: I apply free discount shipping after it has been changed the discount should be correct
    Given shop configuration for "PS_TAX_ADDRESS_TYPE" is set to id_address_invoice
    Given I enable carrier "price_carrier"
    And I select carrier "default_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "default_carrier" as a carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
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
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 7.00  |
      | shipping_cost_tax_incl | 7.42  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 25.230 |
      | total_price_tax_excl        | 23.800 |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "price_carrier"
    Then cart "dummy_cart" should have "price_carrier" as a carrier
    And order "bo_order1" should have "price_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 29.800 |
      | total_paid_tax_incl      | 31.590 |
      | total_paid               | 31.590 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 6.0    |
      | total_shipping_tax_incl  | 6.36   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 6.00  |
      | shipping_cost_tax_incl | 6.36  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 25.230 |
      | total_price_tax_excl        | 23.800 |
    And I add discount to order "bo_order1" with following details:
      | name      | FreeShippingDiscount |
      | type      | free_shipping        |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "FreeShippingDiscount" with amount "$6.00"
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 6.0    |
      | total_discounts_tax_incl | 6.36   |
      | total_paid_tax_excl      | 23.800 |
      | total_paid_tax_incl      | 25.230 |
      | total_paid               | 25.230 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 6.0    |
      | total_shipping_tax_incl  | 6.36   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 6.00  |
      | shipping_cost_tax_incl | 6.36  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 25.230 |
      | total_price_tax_excl        | 23.800 |

  Scenario: I use and address without taxes (no order_detail_tax created), then I change to a country with taxes all is correctly computed
    Given shop configuration for "PS_TAX_ADDRESS_TYPE" is set to id_address_delivery
    And I enable carrier "price_carrier"
    And I select carrier "price_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "price_carrier" as a carrier
    Then I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-france-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I update order "bo_order1" status to "Payment accepted"
    Then order "bo_order1" should have 1 invoice
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have "price_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 23.800 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 28.800 |
      | total_paid_tax_incl      | 28.800 |
      | total_paid               | 28.800 |
      | total_paid_real          | 28.800 |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.0    |
      | carrier_tax_rate         | 0.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 5.00  |
      | shipping_cost_tax_incl | 5.00  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 11.900 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 23.800 |
      | total_price_tax_excl        | 23.800 |
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 23.800 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 28.800 |
      | total_paid_tax_incl     | 28.800 |
      | total_shipping_tax_excl | 5.0    |
      | total_shipping_tax_incl | 5.00   |
    And order "bo_order1" should have no tax details
    And the first invoice from order "bo_order1" should have following shipping tax details:
      | total_tax_excl | rate | total_amount |
      | 5.0            | 0.00 | 0.0          |
    Given I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-states-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Miami                        |
      | Country          | United States                |
      | State            | Florida                      |
      | Postal code      | 33133                        |
    And I change order "bo_order1" shipping address to "test-customer-states-address"
    Then order "bo_order1" shipping address should be "test-customer-states-address"
    # Shipping cost changes because we are not in the same zone but the tax is still the one from invoice address
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 29.800 |
      | total_paid_tax_incl      | 31.590 |
      | total_paid               | 31.590 |
      | total_paid_real          | 28.800 |
      | total_shipping_tax_excl  | 6.0    |
      | total_shipping_tax_incl  | 6.36   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 6.00  |
      | shipping_cost_tax_incl | 6.36  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 25.230 |
      | total_price_tax_excl        | 23.800 |
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.230 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 29.800 |
      | total_paid_tax_incl     | 31.590 |
      | total_shipping_tax_excl | 6.0    |
      | total_shipping_tax_incl | 6.36   |
    And order "bo_order1" should have following tax details:
      | unit_tax_base | total_tax_base | unit_amount | total_amount |
      | 11.900        | 23.800         | 0.714       | 1.430        |
    And the first invoice from order "bo_order1" should have following shipping tax details:
      | total_tax_excl | rate | total_amount |
      | 6.0            | 6.00 | 0.36         |
    # If I switch back the order_detail_tax are cleaned
    And I change order "bo_order1" shipping address to "test-customer-france-address"
    Then order "bo_order1" shipping address should be "test-customer-france-address"
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 23.800 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 28.800 |
      | total_paid_tax_incl      | 28.800 |
      | total_paid               | 28.800 |
      | total_paid_real          | 28.800 |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.0    |
      | carrier_tax_rate         | 0.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 5.00  |
      | shipping_cost_tax_incl | 5.00  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 11.900 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 23.800 |
      | total_price_tax_excl        | 23.800 |
    And order "bo_order1" should have no tax details
    And the first invoice from order "bo_order1" should have following shipping tax details:
      | total_tax_excl | rate | total_amount |
      | 5.0            | 0.00 | 0.00         |

  Scenario: In rounding per item mode, I use an address without taxes (no order_detail_tax created), then I change to a country with taxes all is correctly computed
    Given shop configuration for "PS_TAX_ADDRESS_TYPE" is set to id_address_delivery
    Given specific shop configuration for "rounding type" is set to round each article
    And I enable carrier "price_carrier"
    And I select carrier "price_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "price_carrier" as a carrier
    Then I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-france-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I update order "bo_order1" status to "Payment accepted"
    Then order "bo_order1" should have 1 invoice
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have "price_carrier" as a carrier
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 23.800 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 28.800 |
      | total_paid_tax_incl      | 28.800 |
      | total_paid               | 28.800 |
      | total_paid_real          | 28.800 |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.0    |
      | carrier_tax_rate         | 0.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 5.00  |
      | shipping_cost_tax_incl | 5.00  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 11.900 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 23.800 |
      | total_price_tax_excl        | 23.800 |
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 23.800 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 28.800 |
      | total_paid_tax_incl     | 28.800 |
      | total_shipping_tax_excl | 5.0    |
      | total_shipping_tax_incl | 5.00   |
    And order "bo_order1" should have no tax details
    And the first invoice from order "bo_order1" should have following shipping tax details:
      | total_tax_excl | rate | total_amount |
      | 5.0            | 0.00 | 0.0          |
    Given I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-states-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Miami                        |
      | Country          | United States                |
      | State            | Florida                      |
      | Postal code      | 33133                        |
    And I change order "bo_order1" shipping address to "test-customer-states-address"
    Then order "bo_order1" shipping address should be "test-customer-states-address"
    # Shipping cost changes because we are not in the same zone but the tax is still the one from invoice address
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.220000 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 29.800 |
      | total_paid_tax_incl      | 31.580000 |
      | total_paid               | 31.580000 |
      | total_paid_real          | 28.800 |
      | total_shipping_tax_excl  | 6.0    |
      | total_shipping_tax_incl  | 6.36   |
      | carrier_tax_rate         | 6.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 6.00  |
      | shipping_cost_tax_incl | 6.36  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 12.610000 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 25.220000 |
      | total_price_tax_excl        | 23.800 |
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 23.800 |
      | total_products_wt       | 25.220000 |
      | total_discount_tax_excl | 0.0    |
      | total_discount_tax_incl | 0.0    |
      | total_paid_tax_excl     | 29.800 |
      | total_paid_tax_incl     | 31.580000 |
      | total_shipping_tax_excl | 6.0    |
      | total_shipping_tax_incl | 6.36   |
    And order "bo_order1" should have following tax details:
      | unit_tax_base | total_tax_base | unit_amount | total_amount |
      | 11.900        | 23.800         | 0.714       | 1.42        |
    And the first invoice from order "bo_order1" should have following shipping tax details:
      | total_tax_excl | rate | total_amount |
      | 6.0            | 6.00 | 0.36         |
    # If I switch back the order_detail_tax are cleaned
    And I change order "bo_order1" shipping address to "test-customer-france-address"
    Then order "bo_order1" shipping address should be "test-customer-france-address"
    And order "bo_order1" should have following details:
      | total_products           | 23.800000 |
      | total_products_wt        | 23.800000 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 28.800 |
      | total_paid_tax_incl      | 28.800 |
      | total_paid               | 28.800 |
      | total_paid_real          | 28.800 |
      | total_shipping_tax_excl  | 5.0    |
      | total_shipping_tax_incl  | 5.0    |
      | carrier_tax_rate         | 0.0    |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 5.00  |
      | shipping_cost_tax_incl | 5.00  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | product_price               | 11.900 |
      | original_product_price      | 11.900 |
      | unit_price_tax_incl         | 11.900 |
      | unit_price_tax_excl         | 11.900 |
      | total_price_tax_incl        | 23.800000 |
      | total_price_tax_excl        | 23.800 |
    And order "bo_order1" should have no tax details
    And the first invoice from order "bo_order1" should have following shipping tax details:
      | total_tax_excl | rate | total_amount |
      | 5.0            | 0.00 | 0.00         |
