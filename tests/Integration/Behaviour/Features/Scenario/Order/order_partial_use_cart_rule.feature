# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-partial-use-cart-rule
@restore-all-tables-before-feature
@order-partial-use-cart-rule
@clear-cache-before-feature
Feature: Order with a partial use cart rule
  In order to keep order discounts consistent
  As a merchant
  I need the partial use cart rule line to match the amount actually deducted

  Background:
    Given email sending is disabled
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"

  @restore-cart-rules-after-scenario
  Scenario: Partial use cart rule applied after a free shipping cart rule
    Given there is a cart rule "FreeShipping" with following properties:
      | name[en-US]       | FreeShipping |
      | priority          | 1            |
      | free_shipping     | true         |
      | allow_partial_use | false        |
      | code              | FreeShipping |
    And there is a cart rule "Credit" with following properties:
      | name[en-US]           | Credit |
      | priority              | 2      |
      | discount_amount       | 40     |
      | discount_currency     | usd    |
      | discount_includes_tax | true   |
      | code                  | Credit |
    And I use a voucher "FreeShipping" on the cart "dummy_cart"
    And I use a voucher "Credit" on the cart "dummy_cart"
    When I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 2 cart rules
    And order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 30.800 |
      | total_discounts_tax_incl | 32.650 |
      | total_paid_tax_excl      | 0.0    |
      | total_paid_tax_incl      | 0.0    |
      | total_paid               | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And order "bo_order1" should have cart rule "FreeShipping" with amount "$7.00"
    And order "bo_order1" should have cart rule "Credit" with amount "$23.80"
