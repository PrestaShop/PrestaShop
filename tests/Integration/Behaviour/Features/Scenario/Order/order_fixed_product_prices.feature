# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-fixed-product-prices
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-fixed-product-prices
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to product prices to stay fixed

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And a carrier "price_carrier" with name "My cheap carrier" exists
    And I enable carrier "price_carrier"
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "Test Changing Product" with a price of 10.0 and 100 items in stock
    And the available stock for product "Test Changing Product" should be 100
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add 2 products "Test Changing Product" to the cart "dummy_cart"

    Scenario: I order a product which price is changed after I order Then I update carrier, the product price must not change
      Given I add order "bo_order1" with the following details:
        | cart                | dummy_cart                 |
        | message             | test                       |
        | payment module name | dummy_payment              |
        | status              | Awaiting bank wire payment |
      Then order "bo_order1" should have 4 products in total
      And order "bo_order1" should have 0 invoices
      And order "bo_order1" should have 0 cart rule
      And order "bo_order1" should have "price_carrier" as a carrier
      And product "Test Changing Product" in order "bo_order1" has following details:
        | product_quantity            | 2     |
        | product_price               | 10.00 |
        | original_product_price      | 10.00 |
        | unit_price_tax_incl         | 10.60 |
        | unit_price_tax_excl         | 10.00 |
        | total_price_tax_incl        | 21.20 |
        | total_price_tax_excl        | 20.00 |
      And order "bo_order1" should have following details:
        | total_products           | 43.800 |
        | total_products_wt        | 46.430 |
        | total_discounts_tax_excl | 0.0    |
        | total_discounts_tax_incl | 0.0    |
        | total_paid_tax_excl      | 49.800 |
        | total_paid_tax_incl      | 52.790 |
        | total_paid               | 52.790 |
        | total_paid_real          | 0.0    |
        | total_shipping_tax_excl  | 6.0    |
        | total_shipping_tax_incl  | 6.36   |
        | carrier_tax_rate         | 6.0    |
      And order "bo_order1" carrier should have following details:
        | weight                 | 0.600 |
        | shipping_cost_tax_excl | 6.00  |
        | shipping_cost_tax_incl | 6.36  |
      Given product "Test Changing Product" price is 15.00
      When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "default_carrier"
      Then order "bo_order1" should have "default_carrier" as a carrier
      And product "Test Changing Product" in order "bo_order1" has following details:
        | product_quantity            | 2     |
        | product_price               | 10.00 |
        | original_product_price      | 10.00 |
        | unit_price_tax_incl         | 10.60 |
        | unit_price_tax_excl         | 10.00 |
        | total_price_tax_incl        | 21.20 |
        | total_price_tax_excl        | 20.00 |
      And order "bo_order1" should have following details:
        | total_products           | 43.800 |
        | total_products_wt        | 46.430 |
        | total_discounts_tax_excl | 0.0    |
        | total_discounts_tax_incl | 0.0    |
        | total_paid_tax_excl      | 50.800 |
        | total_paid_tax_incl      | 53.850 |
        | total_paid               | 53.850 |
        | total_paid_real          | 0.0    |
        | total_shipping_tax_excl  | 7.0    |
        | total_shipping_tax_incl  | 7.42   |
        | carrier_tax_rate         | 6.0    |
      And order "bo_order1" carrier should have following details:
        | weight                 | 0.600 |
        | shipping_cost_tax_excl | 7.00  |
        | shipping_cost_tax_incl | 7.42  |
