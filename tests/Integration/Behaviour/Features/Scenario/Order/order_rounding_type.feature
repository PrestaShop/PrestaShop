# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-rounding-type
@reset-database-before-feature
@clear-cache-before-feature
@order-rounding-type

Feature: In BO, get right display prices for products and totals, depending on the order's rounding type
  As a BO user, I need to see the right prices depending on rounding type with odd tax

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
    And price display method for the group of the customer having email "pub@prestashop.com" is "tax included"
    And customer "testCustomer" has address in "FR" country

  Scenario: Check prices of an order's product as seen in BO when rounding type is per line
    Given specific shop configuration for "rounding type" is set to round each line
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 80 products "Test Product With Odd Tax" to the cart "dummy_cart"
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
    Then product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80     |
      | product_price               | 7.80   |
      | original_product_price      | 7.80   |
      | unit_price_tax_incl         | 9.438  |
      | unit_price_tax_excl         | 7.80   |
      | total_price_tax_excl        | 624.00 |
      | total_price_tax_incl        | 755.04 |
    Then product "Test Product With Odd Tax" in order "bo_order1" has following prices for viewing in BO:
      | unit_price_tax_excl_raw     | 7.8    |
      | unit_price_tax_incl_raw     | 9.44   |
      | unit_price                  | $9.44  |
      | total_price                 | $755.04 |

  Scenario: Check prices of an order's product as seen in BO when rounding type is per item
    Given specific shop configuration for "rounding type" is set to round each article
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 80 products "Test Product With Odd Tax" to the cart "dummy_cart"
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
    Then product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80     |
      | product_price               | 7.80   |
      | original_product_price      | 7.80   |
      | unit_price_tax_incl         | 9.438  |
      | unit_price_tax_excl         | 7.80   |
      | total_price_tax_excl        | 624.00 |
      | total_price_tax_incl        | 755.20 |
    Then product "Test Product With Odd Tax" in order "bo_order1" has following prices for viewing in BO:
      | unit_price_tax_excl_raw     | 7.8    |
      | unit_price_tax_incl_raw     | 9.44   |
      | unit_price                  | $9.44  |
      | total_price                 | $755.20|

  Scenario: Check prices of an order's product as seen in BO when rounding type is per cart total
    Given specific shop configuration for "rounding type" is set to round cart total
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 80 products "Test Product With Odd Tax" to the cart "dummy_cart"
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
    Then product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 80     |
      | product_price               | 7.80   |
      | original_product_price      | 7.80   |
      | unit_price_tax_incl         | 9.438  |
      | unit_price_tax_excl         | 7.80   |
      | total_price_tax_excl        | 624.00 |
      | total_price_tax_incl        | 755.04 |
    Then product "Test Product With Odd Tax" in order "bo_order1" has following prices for viewing in BO:
      | unit_price_tax_excl_raw     | 7.8    |
      | unit_price_tax_incl_raw     | 9.44   |
      | unit_price                  | $9.44  |
      | total_price                 | $755.04 |
