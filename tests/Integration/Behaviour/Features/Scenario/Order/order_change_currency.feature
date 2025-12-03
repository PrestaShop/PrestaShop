# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-change-currency
@restore-all-tables-before-feature
@order-change-currency
@clear-cache-before-feature
@reboot-kernel-before-feature
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to change currency from orders from the BO

  Background:
    Given email sending is disabled
    And shop "shop1" with name "test_shop" exists
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And I add new currency "eur" with following properties:
      | iso_code         | EUR        |
      | exchange_rate    | 0.88       |
      | name             | My Euros   |
      | symbols[en-US]   | €          |
      | patterns[en-US]  | ¤#,##0.00  |
      | is_enabled       | 1          |
      | is_unofficial    | 0          |
      | shop_association | shop1      |
    And the current currency is "USD"
    And country "US" is enabled
    And language with iso code "en" is the default one
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  @restore-cart-rules-before-scenario
  Scenario: Add discount to all orders, when a product is added the discount is applied, when a product is removed the discount should still be present
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
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
    And there is a product in the catalog named "Test Product Cart Rule On Order" with a price of 15.0 and 100 items in stock
    And there is a cart rule "CartRuleAmountOnEveryOrder" with following properties:
      | name[en-US]               | CartRuleAmountOnEveryOrder |
      | priority                  | 1                          |
      | free_shipping             | false                      |
      | discount_percentage       | 50                         |
      | discount_application_type | order_without_shipping     |
      | total_quantity            | 1000                       |
      | quantity_per_user         | 1000                       |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product Cart Rule On Order |
      | amount | 1                               |
      | price  | 15                              |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product Cart Rule On Order"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnEveryOrder" with amount "$19.40"
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 19.400 |
      | total_discounts_tax_incl | 20.570 |
      | total_paid_tax_excl      | 26.4   |
      | total_paid_tax_incl      | 27.980 |
      | total_paid               | 27.980 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I change for the order "bo_order1" the currency "usd" to "eur"
    # USD => EUR : * 0.88
    Then order "bo_order1" should have following details:
      | total_products           | 34.14 |
      | total_products_wt        | 36.19 |
      | total_discounts_tax_excl | 17.07 |
      | total_discounts_tax_incl | 18.10 |
      | total_paid_tax_excl      | 23.23 |
      | total_paid_tax_incl      | 24.62 |
      | total_paid               | 24.62 |
      | total_paid_real          | 0.0   |
      | total_shipping_tax_excl  | 6.16  |
      | total_shipping_tax_incl  | 6.53  |
    Then order "bo_order1" should have cart rule "CartRuleAmountOnEveryOrder" with amount "€17.07"