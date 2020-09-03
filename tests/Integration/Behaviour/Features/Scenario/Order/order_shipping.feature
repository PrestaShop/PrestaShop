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
    And there is a carrier named "carrier1"
    And carrier "carrier1" applies shipping fees of 9.0 in zone "zone1" for price between 0 and 50
    And carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 50 and 100
    And carrier "carrier1" applies shipping fees of 0.0 in zone "zone1" for price between 100 and 10000
    And carrier "carrier1" applies shipping fees of 7.0 in zone "zone2" for price between 0 and 50
    And carrier "carrier1" applies shipping fees of 4.0 in zone "zone2" for price between 50 and 100
    And carrier "carrier1" applies shipping fees of 0.0 in zone "zone2" for price between 100 and 10000
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    # It is important to have products in the cart before setting shipping information, because carrier options depend
    # on the products settings
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I select carrier "carrier1" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Add product to change order total the shipping price should update as well
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
    Given there is a product in the catalog named "Shipping Product" with a price of 15.0 and 100 items in stock
    And product "Shipping Product" weight is 0.63 kg
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Shipping Product |
      | amount        | 2                |
      | price         | 15               |
    Then order "bo_order1" should have 4 products in total
    Then order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
#      | total_paid_tax_excl      | 57.8   |
#      | total_paid_tax_incl      | 61.270 |
#      | total_paid               | 61.270 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 4.0    |
      | total_shipping_tax_incl  | 4.24   |
    And order "bo_order1" carrier should have following details:
      | weight                 | 1.260 |
      | shipping_cost_tax_excl | 4.00  |
      | shipping_cost_tax_incl | 4.24  |
