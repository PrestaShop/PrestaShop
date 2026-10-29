# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-currency-precision
@restore-all-tables-before-feature
@clear-cache-before-feature
@reboot-kernel-before-feature
@restore-currencies-after-feature
@reboot-kernel-after-feature
@restore-currencies-before-scenario
@order-currency-precision
Feature: Order in a currency with a different precision than the default currency in Back Office (BO)
  In order to edit orders placed in a currency which has more decimals than the default currency
  As a BO user
  I need the order amounts to be computed with the precision of the order currency

  Background:
    Given email sending is disabled
    And shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 1.0
    And currency "usd" is default in "shop1" shop
    And the current currency is "USD"
    And country "US" is enabled
    And language "French" with locale "fr-FR" exists
    And I add new currency "currency2" with following properties:
      | iso_code         | EUR        |
      | exchange_rate    | 10.00      |
      | name             | My Euros   |
      | symbols[en-US]   | €          |
      | symbols[fr-FR]   | €          |
      | patterns[en-US]  | ¤#,##0.00  |
      | patterns[fr-FR]  | #,##0.00 ¤ |
      | is_enabled       | 1          |
      | is_unofficial    | 0          |
      | shop_association | shop1      |
    And the module "dummy_payment" is installed
    # The order is created while the default currency still has 2 decimals
    And the context currency is the default one and its computing precision is cached
    And the context computing precision should be 2
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I update the cart "dummy_cart" currency to "currency2"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And the default category of product "Mug The best is yet to come" has no group reduction
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 308.00 |
      | total_paid_tax_incl      | 326.48 |
      | total_paid               | 326.48 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |
    # The default currency has no decimals, while the order currency keeps 2 decimals
    And I edit currency "usd" with following properties:
      | precision  | 0 |
      | is_enabled | 1 |
    And currency "currency2" precision should be 2
    # Like in a BO request, the precision of the default currency is computed before the command is handled
    And the context currency is the default one and its computing precision is cached
    And the context computing precision should be 0

  Scenario: Add a product with a price of 0 to an order in a currency with more decimals than the default currency
    When I add products to order "bo_order1" without invoice and the following products details:
      | name   | Mug Today is a good day |
      | amount | 1                       |
      | price  | 0                       |
    # The context is restored with the precision of the default currency
    Then the context computing precision should be 0
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 308.00 |
      | total_paid_tax_incl      | 326.48 |
      | total_paid               | 326.48 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity       | 2      |
      | original_product_price | 119.00 |
      | product_price          | 119.00 |
      | unit_price_tax_incl    | 126.14 |
      | unit_price_tax_excl    | 119.00 |
      | total_price_tax_incl   | 252.28 |
      | total_price_tax_excl   | 238.00 |

  Scenario: Update product quantity in an order in a currency with more decimals than the default currency
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount | 3      |
      | price  | 119.00 |
    # The context is restored with the precision of the default currency
    Then the context computing precision should be 0
    And order "bo_order1" should contain 3 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity       | 3      |
      | original_product_price | 119.00 |
      | product_price          | 119.00 |
      | unit_price_tax_incl    | 126.14 |
      | unit_price_tax_excl    | 119.00 |
      | total_price_tax_incl   | 378.42 |
      | total_price_tax_excl   | 357.00 |
    And order "bo_order1" should have following details:
      | total_products           | 357.00 |
      | total_products_wt        | 378.42 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 427.00 |
      | total_paid_tax_incl      | 452.62 |
      | total_paid               | 452.62 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |

  Scenario: Add a product to an order when the context currency was set by the context state manager, like in a BO request
    Given the default currency is set in a context without currency by the context state manager
    And the context computing precision should be 0
    When I add products to order "bo_order1" without invoice and the following products details:
      | name   | Mug Today is a good day |
      | amount | 1                       |
      | price  | 0                       |
    # The context currency is null again, the precision of the order currency is kept
    Then the context computing precision should be 2
    # Like the BO controller after the command, the order is read again while the context has no currency anymore
    And order "bo_order1" has status "Awaiting bank wire payment"
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 308.00 |
      | total_paid_tax_incl      | 326.48 |
      | total_paid               | 326.48 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |
    # Put the default currency back in the context for the next scenarios
    And the context currency is the default one and its computing precision is cached

  Scenario: Change the currency of an order to the default currency and back, the amounts keep the precision of each currency
    # A rate that does not give round amounts when converting back from the default currency
    Given I edit currency "currency2" with following properties:
      | exchange_rate | 0.9 |
    When I change for the order "bo_order1" the currency "EUR" to "USD"
    # The context is restored with the precision of the default currency
    Then the context computing precision should be 0
    # EUR => USD : / 0.9, rounded with the precision of USD (0)
    And order "bo_order1" should have following details:
      | total_products           | 264.00 |
      | total_products_wt        | 280.00 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 342.00 |
      | total_paid_tax_incl      | 363.00 |
      | total_paid               | 363.00 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 78.00  |
      | total_shipping_tax_incl  | 82.00  |
    When I change for the order "bo_order1" the currency "USD" to "EUR"
    Then the context computing precision should be 0
    # USD => EUR : * 0.9, rounded with the precision of EUR (2), not with the precision of the default currency (0)
    And order "bo_order1" should have following details:
      | total_products           | 237.60 |
      | total_products_wt        | 252.00 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 307.80 |
      | total_paid_tax_incl      | 326.70 |
      | total_paid               | 326.70 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.20  |
      | total_shipping_tax_incl  | 73.80  |
    # product_price is not checked: Order::getProducts() rounds it again with the context computing precision when reading
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity       | 2      |
      | original_product_price | 118.80 |
      | unit_price_tax_incl    | 126.00 |
      | unit_price_tax_excl    | 118.80 |
      | total_price_tax_incl   | 252.00 |
      | total_price_tax_excl   | 237.60 |
