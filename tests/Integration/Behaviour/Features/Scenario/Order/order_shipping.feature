# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-shipping
@reset-database-before-feature
@order-shipping
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And there is a zone named "zone1"
    And there is a zone named "zone2"
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a country named "country2" and iso code "US" in zone "zone2"
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
    And order "bo_order1" carrier should have following details:
      | weight                 | 4.380 |
      | shipping_cost_tax_excl | 2.00  |
      | shipping_cost_tax_incl | 2.12  |
