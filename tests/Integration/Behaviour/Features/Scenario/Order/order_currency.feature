# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags multiple-currencies-to-order
@reset-database-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@multiple-currencies-to-order
Feature: Multiple currencies for Order in Back Office (BO)
  In order to manage multiple currencies for orders in BO
  As a BO user
  I need to be able to change order informations and have correct results

  Background:
    Given email sending is disabled
    Given shop "shop1" with name "test_shop" exists
    And the current currency is "USD"
    And country "US" is enabled
    And country "FR" is enabled
    And language "French" with locale "fr-FR" exists
    And I add new currency "currency2" with following properties:
      | iso_code         | EUR                              |
      | exchange_rate    | 10.00                            |
      | name             | My Euros                         |
      | symbols          | en-US:€;fr-FR:€                  |
      | patterns         | en-US:¤#,##0.00;fr-FR:#,##0.00 ¤ |
      | is_enabled       | 1                                |
      | is_unofficial    | 0                                |
      | shop_association | shop1                            |
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I update the cart "dummy_cart" currency to "currency2"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" has 0 payments
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
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

  Scenario: Add cart rule of type 'amount' to an order with secondary currency
    Given I add discount to order "bo_order1" with following details:
      | name      | discount ten-euros    |
      | type      | amount                |
      | value     | 10                    |
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "discount ten-euros" with amount "€9.43"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 9.43   |
      | total_discounts_tax_incl | 10.0   |
      | total_paid_tax_excl      | 298.57 |
      | total_paid_tax_incl      | 316.48 |
      | total_paid               | 316.48 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |

  Scenario: Add cart rule of type 'percent' to an order with secondary currency
    Given I add discount to order "bo_order1" with following details:
      | name      | discount ten-percents |
      | type      | percent               |
      | value     | 10                    |
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "discount ten-percents" with amount "€23.80"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 23.80  |
      | total_discounts_tax_incl | 25.23  |
      | total_paid_tax_excl      | 284.20 |
      | total_paid_tax_incl      | 301.25 |
      | total_paid               | 301.25 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |

  Scenario: Add cart rule of type 'free-shipping' to an order with secondary currency
    Given I add discount to order "bo_order1" with following details:
      | name      | discount free-shipping |
      | type      | free_shipping          |
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "discount free-shipping" with amount "€70.00"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 70.00  |
      | total_discounts_tax_incl | 74.20  |
      | total_paid_tax_excl      | 238.00 |
      | total_paid_tax_incl      | 252.28 |
      | total_paid               | 252.28 |
      | total_paid_real          | 0.00   |
      | total_shipping_tax_excl  | 70.00   |
      | total_shipping_tax_incl  | 74.20   |

  Scenario: Add product to an order with secondary currency
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Mug Today is a good day  |
      | amount        | 5                      |
      | price         | 15                      |
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 313.00 |
      | total_products_wt        | 331.78 |
      | total_discounts_tax_excl | 0.00   |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 383.00 |
      | total_paid_tax_incl      | 405.98 |
      | total_paid               | 405.98 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |

  Scenario: Update product in order with secondary currency
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | 3                       |
      | price         | 12.00                   |
    Then order "bo_order1" should contain 3 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 3     |
      | product_price               | 12.00 |
      | unit_price_tax_incl         | 12.72 |
      | unit_price_tax_excl         | 12.00 |
      | total_price_tax_incl        | 38.16 |
      | total_price_tax_excl        | 36.00 |
    And product "Mug The best is yet to come" in order "bo_order1" should have specific price 12.00

  Scenario: Check invoice for an order with secondary currency and discount
    Given I add discount to order "bo_order1" with following details:
      | name      | discount ten-euros    |
      | type      | amount                |
      | value     | 10                    |
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoice
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 238.00   |
      | total_products_wt       | 252.28   |
      | total_discount_tax_excl | 9.43     |
      | total_discount_tax_incl | 10.0     |
      | total_paid_tax_excl     | 298.57   |
      | total_paid_tax_incl     | 316.48   |
      | total_shipping_tax_excl | 70.00    |
      | total_shipping_tax_incl | 74.20    |

  Scenario: Carrier change for an order with secondary currency
    Given a carrier "default_carrier" with name "My carrier" exists
    And a carrier "price_carrier" with name "My cheap carrier" exists
    And I enable carrier "price_carrier"
    And order "bo_order1" should have "default_carrier" as a carrier
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 70.00  |
      | shipping_cost_tax_incl | 74.20  |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "price_carrier"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 298.00 |
      | total_paid_tax_incl      | 315.88 |
      | total_paid               | 315.88 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 60.00  |
      | total_shipping_tax_incl  | 63.60  |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 60.00  |
      | shipping_cost_tax_incl | 63.60  |
