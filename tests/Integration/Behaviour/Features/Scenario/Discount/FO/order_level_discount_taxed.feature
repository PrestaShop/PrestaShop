# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags order-level-discount-taxed
@restore-all-tables-before-feature
@order-level-discount-taxed
@clear-cache-before-feature
@clear-cache-after-feature
Feature: Order level percentage discount on taxed products and shipping
  As a customer
  I must get an order level percentage discount computed on the products and the shipping, tax included and tax excluded

  # The Florida address of the customer applies a 6% tax: the product costs 10.00 tax excluded (10.60 tax included)
  # and the shipping 7.00 tax excluded (7.42 tax included).
  # The cart summary shows the products and the discount tax excluded, the shipping and the total tax included.
  Background:
    Given language with iso code "en" is the default one
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And there is a product in the catalog named "taxed_product" with a price of 10.00 and 1000 items in stock
    And I enable feature flag "discount"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 1 product "taxed_product" to the cart "dummy_cart"

  Scenario: 50% order level discount
    When I create a "order_level" discount "order_level_50" with following properties:
      | name[en-US]       | Order level 50      |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2099-12-31 23:59:59 |
      | code              | ORDER_LEVEL_50      |
      | reduction_percent | 50.0                |
    And I use a voucher "order_level_50" on the cart "dummy_cart"
    Then my cart "dummy_cart" should have the following details:
      | total_products | $10.00 |
      | shipping       | $7.42  |
      | total_discount | -$8.50 |
      | total          | $9.01  |
    And cart "dummy_cart" total with tax included should be '$9.01'
    When I add order "order_50" from cart "dummy_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then order "order_50" should have the following details:
      | total_discounts_tax_incl | 9.010000 |
      | total_discounts_tax_excl | 8.500000 |
      | total_shipping_tax_incl  | 7.420000 |
      | total_paid_tax_incl      | 9.010000 |
      | total_paid_tax_excl      | 8.500000 |
    And I disable feature flag "discount"

  Scenario: 100% order level discount makes the whole order free, shipping included
    When I create a "order_level" discount "order_level_100" with following properties:
      | name[en-US]       | Order level 100     |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2099-12-31 23:59:59 |
      | code              | ORDER_LEVEL_100     |
      | reduction_percent | 100.0               |
    And I use a voucher "order_level_100" on the cart "dummy_cart"
    Then my cart "dummy_cart" should have the following details:
      | total_products | $10.00  |
      | shipping       | $7.42   |
      | total_discount | -$17.00 |
      | total          | $0.00   |
    And cart "dummy_cart" total with tax included should be '$0.00'
    And I disable feature flag "discount"

  Scenario: Order level amount discount
    When I create a "order_level" discount "order_level_amount" with following properties:
      | name[en-US]            | Order level amount  |
      | active                 | true                |
      | valid_from             | 2025-01-01 00:00:00 |
      | valid_to               | 2099-12-31 23:59:59 |
      | code                   | ORDER_LEVEL_AMOUNT  |
      | reduction_amount       | 10.0                |
      | reduction_currency     | usd                 |
      | reduction_tax_included | true                |
    And I use a voucher "order_level_amount" on the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$8.02'
    And I disable feature flag "discount"

  Scenario: Order level amount discount tax excluded above the products total also applies to the shipping
    When I create a "order_level" discount "order_level_amount_excl" with following properties:
      | name[en-US]            | Order level amount excl |
      | active                 | true                    |
      | valid_from             | 2025-01-01 00:00:00     |
      | valid_to               | 2099-12-31 23:59:59     |
      | code                   | ORDER_LEVEL_AMOUNT_EXCL |
      | reduction_amount       | 15.0                    |
      | reduction_currency     | usd                     |
      | reduction_tax_included | false                   |
    And I use a voucher "order_level_amount_excl" on the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$2.12'
    When I add order "order_amount_excl" from cart "dummy_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then order "order_amount_excl" should have the following details:
      | total_discounts_tax_incl | 15.900000 |
      | total_discounts_tax_excl | 15.000000 |
      | total_paid_tax_incl      | 2.120000  |
      | total_paid_tax_excl      | 2.000000  |
    And I disable feature flag "discount"
