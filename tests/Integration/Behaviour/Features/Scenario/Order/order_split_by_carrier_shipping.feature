# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-split-by-carrier-shipping
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-split-by-carrier-shipping
Feature: Each order split by carrier stores the shipping of its own package
  As a merchant
  I want each order created from a split cart to store the shipping of its own carrier
  So that its shipping, invoice and refunds match what the customer paid for it

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And there is a zone "north_america" named "North America"
    And I add new tax "us-tax" with following properties:
      | name       | US Tax (6%) |
      | rate       | 6           |
      | is_enabled | true        |
    And I add the tax rule group "us-tax-group" for the tax "us-tax" with the following conditions:
      | name    | US Tax (6%) |
      | country | US          |
    And I create carrier "first_carrier" with specified properties:
      | name             | First carrier |
      | active           | true          |
      | shippingMethod   | price         |
      | zones            | north_america |
      | shippingHandling | false         |
    And I set ranges for carrier "first_carrier" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 1000     | 5           |
    And I set tax rule "us-tax-group" for carrier "first_carrier"
    And I create carrier "second_carrier" with specified properties:
      | name             | Second carrier |
      | active           | true           |
      | shippingMethod   | price          |
      | zones            | north_america  |
      | shippingHandling | false          |
    And I set ranges for carrier "second_carrier" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 1000     | 10          |
    And I set tax rule "us-tax-group" for carrier "second_carrier"
    And I add product "first_product" with following information:
      | name[en-US] | first product |
      | type        | standard      |
    And I update product "first_product" with following values:
      | price           | 10.25       |
      | tax rules group | US Tax (6%) |
      | active          | true        |
    And I update product "first_product" stock with following information:
      | delta_quantity | 50 |
    And I assign product first_product with following carriers:
      | first_carrier |
    And I add product "second_product" with following information:
      | name[en-US] | second product |
      | type        | standard       |
    And I update product "second_product" with following values:
      | price           | 10.25       |
      | tax rules group | US Tax (6%) |
      | active          | true        |
    And I update product "second_product" stock with following information:
      | delta_quantity | 50 |
    And I assign product second_product with following carriers:
      | second_carrier |
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 1 products "first product" to the cart "dummy_cart"
    And I add 1 products "second product" to the cart "dummy_cart"

  Scenario: Each split order stores the shipping of its own carrier
    When I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    Then order "bo_order1" should be split into 2 orders with following summed details:
      | total_shipping_tax_excl | 15.00 |
      | total_shipping_tax_incl | 15.90 |
      | total_paid_tax_excl     | 35.50 |
      | total_paid_tax_incl     | 37.64 |
    When I reference the orders split with order "bo_order1" as "first_order,second_order"
    Then order "first_order" should have "first_carrier" as a carrier
    And order "first_order" should have following details:
      | total_products          | 10.25 |
      | total_products_wt       | 10.87 |
      | total_shipping_tax_excl | 5.00  |
      | total_shipping_tax_incl | 5.30  |
      | total_paid_tax_excl     | 15.25 |
      | total_paid_tax_incl     | 16.17 |
    And order "first_order" carrier should have following details:
      | shipping_cost_tax_excl | 5.00 |
      | shipping_cost_tax_incl | 5.30 |
    And the first invoice from order "first_order" should have following details:
      | total_shipping_tax_excl | 5.00 |
      | total_shipping_tax_incl | 5.30 |
    And the totals of order "first_order" should add up to its total paid
    And order "second_order" should have "second_carrier" as a carrier
    And order "second_order" should have following details:
      | total_products          | 10.25 |
      | total_products_wt       | 10.87 |
      | total_shipping_tax_excl | 10.00 |
      | total_shipping_tax_incl | 10.60 |
      | total_paid_tax_excl     | 20.25 |
      | total_paid_tax_incl     | 21.47 |
    And order "second_order" carrier should have following details:
      | shipping_cost_tax_excl | 10.00 |
      | shipping_cost_tax_incl | 10.60 |
    And the first invoice from order "second_order" should have following details:
      | total_shipping_tax_excl | 10.00 |
      | total_shipping_tax_incl | 10.60 |
    And the totals of order "second_order" should add up to its total paid

  Scenario: A free shipping cart rule cancels the shipping of each split order
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a cart rule "free_shipping" with following properties:
      | name[en-US]   | Free shipping |
      | priority      | 1             |
      | free_shipping | true          |
    When I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    Then order "bo_order1" should be split into 2 orders with following summed details:
      | total_shipping_tax_excl  | 15.00 |
      | total_shipping_tax_incl  | 15.90 |
      | total_discounts_tax_excl | 15.00 |
      | total_discounts_tax_incl | 15.90 |
      | total_paid_tax_excl      | 20.50 |
      | total_paid_tax_incl      | 21.74 |
    When I reference the orders split with order "bo_order1" as "first_order,second_order"
    Then order "first_order" should have following details:
      | total_shipping_tax_excl  | 5.00  |
      | total_shipping_tax_incl  | 5.30  |
      | total_discounts_tax_excl | 5.00  |
      | total_discounts_tax_incl | 5.30  |
      | total_paid_tax_excl      | 10.25 |
      | total_paid_tax_incl      | 10.87 |
    And the totals of order "first_order" should add up to its total paid
    And order "second_order" should have following details:
      | total_shipping_tax_excl  | 10.00 |
      | total_shipping_tax_incl  | 10.60 |
      | total_discounts_tax_excl | 10.00 |
      | total_discounts_tax_incl | 10.60 |
      | total_paid_tax_excl      | 10.25 |
      | total_paid_tax_incl      | 10.87 |
    And the totals of order "second_order" should add up to its total paid
