# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-with-code
@restore-all-tables-before-feature
@discount-with-code
Feature: Full UX discount test
  PrestaShop allows discounts with promo code or not

  Background:
    Given there is a customer named "testCustomer" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  Scenario: Create a discount with free shipping with promo code
    And there is a product in the catalog named "product1" with a price of 10.00 and 1000 items in stock
    When I create a "free_shipping" discount "free_shipping_discount_with_minimum_amount_code" with following properties:
      | name[en-US] | Minimum amount gives free shipping  |
      | name[fr-FR] | Montant minimum donne envoi gratuit |
      | active      | true                                |
      | valid_from  | 2025-01-01 11:05:00                 |
      | valid_to    | 2026-12-01 00:00:00                 |
      | code        | FREE_SHIPPING_MIN_AMOUNT            |
    When I update discount "free_shipping_discount_with_minimum_amount_code" with the condition of a minimum amount:
      | minimum_amount                   | 40.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | true  |
    And discount "free_shipping_discount_with_minimum_amount_code" should have the following properties:
      | name[en-US]                      | Minimum amount gives free shipping  |
      | name[fr-FR]                      | Montant minimum donne envoi gratuit |
      | active                           | true                                |
      | valid_from                       | 2025-01-01 11:05:00                 |
      | valid_to                         | 2026-12-01 00:00:00                 |
      | code                             | FREE_SHIPPING_MIN_AMOUNT            |
      | minimum_amount                   | 40.00                               |
      | minimum_amount_currency          | usd                                 |
      | minimum_amount_tax_included      | true                                |
      | minimum_amount_shipping_included | true                                |
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 3 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$37.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $37.00 |
    When I use a voucher "free_shipping_discount_with_minimum_amount_code" on the cart "dummy_cart"
    Then I should get cart rule validation error
    # First try the condition is not met so the cart remains unchanged
    And cart "dummy_cart" total with tax included should be '$37.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $37.00 |
    # Now we add another product to increase the cart amount
    When I add 1 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$47.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $47.00 |
    When I use a voucher "free_shipping_discount_with_minimum_amount_code" on the cart "dummy_cart"
    # Now the condition is met so the cart shipping is offered
    And cart "dummy_cart" total with tax included should be '$40.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $40.00 |

  Scenario: Create a discount with free shipping without a promo code
    And there is a product in the catalog named "product1" with a price of 10.00 and 1000 items in stock
    When I create a "free_shipping" discount "free_shipping_discount_with_minimum_amount_auto" with following properties:
      | name[en-US] | Minimum amount gives free shipping  |
      | name[fr-FR] | Montant minimum donne envoi gratuit |
      | active      | true                                |
      | valid_from  | 2025-01-01 11:05:00                 |
      | valid_to    | 2026-12-01 00:00:00                 |
      | code        |                                     |
    When I update discount "free_shipping_discount_with_minimum_amount_auto" with the condition of a minimum amount:
      | minimum_amount                   | 40.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | true  |
    And discount "free_shipping_discount_with_minimum_amount_auto" should have the following properties:
      | name[en-US]                      | Minimum amount gives free shipping  |
      | name[fr-FR]                      | Montant minimum donne envoi gratuit |
      | active                           | true                                |
      | valid_from                       | 2025-01-01 11:05:00                 |
      | valid_to                         | 2026-12-01 00:00:00                 |
      | code                             |                                     |
      | minimum_amount                   | 40.00                               |
      | minimum_amount_currency          | usd                                 |
      | minimum_amount_tax_included      | true                                |
      | minimum_amount_shipping_included | true                                |
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 3 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$37.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $37.00 |
    When I add 1 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$40.00'
    # the cart rule for auto discounts is applied automatically, but is not visible in the cart if we hide discounts
    And my cart "dummy_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $0.00  |
      | total_discount | $0.00  |
      | total          | $40.00 |
    And my cart "dummy_cart" should have the following details, without hiding auto discounts:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00  |
      | total          | $40.00 |
