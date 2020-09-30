# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-french-tax
@reset-database-before-feature
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
    And I add tax rule group "french-tax-group" for tax "french-tax" with following conditions:
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
    And I associate tax rule group "french-tax-group" to carrier "price_carrier"
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
      | unit_price_tax_incl         | 14.28 |
      | unit_price_tax_excl         | 11.90 |
      | total_price_tax_incl        | 28.56 |
      | total_price_tax_excl        | 23.80 |
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount         | 1                       |
      | price          | 83.33                   |
      | price_tax_incl | 100                     |
    When I generate invoice for "bo_order1" order
    Then product "Mug The best is yet to come" in first invoice from order "bo_order1" should have following details:
      | product_quantity            | 1     |
      | product_price               | 83.33 |
      | original_product_price      | 11.90 |
      | unit_price_tax_incl         | 100   |
      | unit_price_tax_excl         | 83.33 |
      | total_price_tax_incl        | 100   |
      | total_price_tax_excl        | 83.33 |
    And order "bo_order1" should have 1 products in total
    And order "bo_order1" should have following details:
      | total_products           | 83.33  |
      | total_products_wt        | 100.00 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 85.33  |
      | total_paid_tax_incl      | 102.40 |
      | total_paid               | 102.40 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.40   |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name           | Mug The best is yet to come |
      | amount         | 1                           |
      | price          | 83.33                       |
      | price_tax_incl | 100                         |
    Then product "Mug The best is yet to come" in first invoice from order "bo_order1" should have following details:
      | product_quantity            | 1     |
      | product_price               | 83.33 |
      | original_product_price      | 11.90 |
      | unit_price_tax_incl         | 100   |
      | unit_price_tax_excl         | 83.33 |
      | total_price_tax_incl        | 100   |
      | total_price_tax_excl        | 83.33 |
    And product "Mug The best is yet to come" in second invoice from order "bo_order1" should have following details:
      | product_quantity            | 1     |
      | product_price               | 83.33 |
      | original_product_price      | 11.90 |
      | unit_price_tax_incl         | 100   |
      | unit_price_tax_excl         | 83.33 |
      | total_price_tax_incl        | 100   |
      | total_price_tax_excl        | 83.33 |
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have following details:
      | total_products           | 166.66 |
      | total_products_wt        | 200.00 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 168.66 |
      | total_paid_tax_incl      | 202.40 |
      | total_paid               | 202.40 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 2.0    |
      | total_shipping_tax_incl  | 2.40   |
