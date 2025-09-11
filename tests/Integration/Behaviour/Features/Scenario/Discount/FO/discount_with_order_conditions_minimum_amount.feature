# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-order-condition-minimum-amount
@restore-all-tables-before-feature
@discount-order-condition-minimum-amount
Feature: Order-based discount conditions
  PrestaShop allows discounts with order-based minimum amount conditions
  As a customer
  I should be able to benefit from discounts based on order total amount

  Background:
    Given there is a customer named "testCustomer" whose email is "pub@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  Scenario: Create discount with order-based minimum amount condition
    And there is a product in the catalog named "product1" with a price of 10.00 and 1000 items in stock
    When I create a "order_level" discount "order_discount_with_minimum_amount" with following properties:
      | name[en-US] | Order discount with minimum amount  |
      | name[fr-FR] | Remise commande avec montant minimum |
      | active      | true                                |
      | valid_from  | 2025-01-01 11:05:00                 |
      | valid_to    | 2025-12-01 00:00:00                 |
      | code        | ORDER_DISCOUNT_MIN_AMOUNT            |
      | reduction_amount | 7.00                            |
      | reduction_currency | usd                           |
      | taxIncluded | true                                 |
    When I update discount "order_discount_with_minimum_amount" with the condition of a minimum amount:
      | minimum_amount                   | 50.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | true  |
    And discount "order_discount_with_minimum_amount" should have the following properties:
      | name[en-US]                      | Order discount with minimum amount  |
      | name[fr-FR]                      | Remise commande avec montant minimum |
      | active                           | true                                |
      | valid_from                       | 2025-01-01 11:05:00                 |
      | valid_to                         | 2025-12-01 00:00:00                 |
      | code                             | ORDER_DISCOUNT_MIN_AMOUNT            |
      | minimum_amount                   | 50.00                               |
      | minimum_amount_currency          | usd                                 |
      | minimum_amount_tax_included      | true                                |
      | minimum_amount_shipping_included | true                                |
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 4 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$47.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $47.00 |
    When I use a voucher "order_discount_with_minimum_amount" on the cart "dummy_cart"
    Then I should get cart rule validation error
    # First try the condition is not met so the cart remains unchanged
    And cart "dummy_cart" total with tax included should be '$47.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $47.00 |
    # Now we add another product to increase the cart amount
    When I add 1 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$57.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $57.00 |
    When I use a voucher "order_discount_with_minimum_amount" on the cart "dummy_cart"
    # Now the condition is met so the discount is applied
    And cart "dummy_cart" total with tax included should be '$50.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $50.00 |

  Scenario: Test order condition with tax excluded
    And there is a product in the catalog named "product2" with a price of 15.00 and 1000 items in stock
    When I create a "order_level" discount "order_discount_tax_excluded" with following properties:
      | name[en-US] | Order discount tax excluded |
      | active      | true                        |
      | valid_from  | 2025-01-01 11:05:00         |
      | valid_to    | 2025-12-01 00:00:00         |
      | code        | ORDER_DISCOUNT_TAX_EXCLUDED |
      | reduction_amount | 7.00                    |
      | reduction_currency | usd                   |
      | taxIncluded | true                         |
    When I update discount "order_discount_tax_excluded" with the condition of a minimum amount:
      | minimum_amount                   | 30.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | false |
      | minimum_amount_shipping_included | true  |
    And discount "order_discount_tax_excluded" should have the following properties:
      | minimum_amount                   | 30.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | false |
      | minimum_amount_shipping_included | true  |
    Given I create an empty cart "test_cart" for customer "testCustomer"
    When I add 2 product "product2" to the cart "test_cart"
    And cart "test_cart" total with tax included should be '$37.00'
    And my cart "test_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $37.00 |
    When I use a voucher "order_discount_tax_excluded" on the cart "test_cart"
    # The condition is met (30.00 tax excluded) so the discount is applied
    And cart "test_cart" total with tax included should be '$30.00'
    And my cart "test_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $30.00 |

  Scenario: Test order condition without shipping included
    And there is a product in the catalog named "product3" with a price of 20.00 and 1000 items in stock
    When I create a "order_level" discount "order_discount_no_shipping" with following properties:
      | name[en-US] | Order discount without shipping |
      | active      | true                            |
      | valid_from  | 2025-01-01 11:05:00             |
      | valid_to    | 2025-12-01 00:00:00             |
      | code        | ORDER_DISCOUNT_NO_SHIPPING      |
      | reduction_amount | 7.00                        |
      | reduction_currency | usd                        |
      | taxIncluded | true                              |
    When I update discount "order_discount_no_shipping" with the condition of a minimum amount:
      | minimum_amount                   | 40.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | false |
    And discount "order_discount_no_shipping" should have the following properties:
      | minimum_amount                   | 40.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | false |
    Given I create an empty cart "shipping_cart" for customer "testCustomer"
    When I add 2 product "product3" to the cart "shipping_cart"
    And cart "shipping_cart" total with tax included should be '$47.00'
    And my cart "shipping_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $47.00 |
    When I use a voucher "order_discount_no_shipping" on the cart "shipping_cart"
    # The condition is met (40.00 products only, shipping not included) so the discount is applied
    And cart "shipping_cart" total with tax included should be '$40.00'
    And my cart "shipping_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $40.00 |
