# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags full-ux-discount-test-order-level
@restore-all-tables-before-feature
@full-ux-discount-test-order-level
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Full UX discount test
  PrestaShop allows BO users to create discounts
  As a BO user
  I must be able to create discounts using the new discounts

  Background:
    Given there is a customer named "testCustomer" whose email is "pub3@prestashop.com"
    Given there is a customer named "testCustomer2" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_order_RULE_FEATURE_ACTIVE" is set to 1

  Scenario: Create a complete order level flat discount using new CQRS
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And I enable feature flag "discount"
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 20.0 and 1000 items in stock
    When I create a "order_level" discount "complete_amount_order_level_discount" with following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]        | Promotion_fr        |
      | active             | true                |
      | valid_from         | 2025-01-01 11:05:00 |
      | valid_to           | 2026-12-01 00:00:00 |
      | code               | PROMO_order_2025    |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    And discount "complete_amount_order_level_discount" should have the following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]        | Promotion_fr        |
      | active             | true                |
      | valid_from         | 2025-01-01 11:05:00 |
      | valid_to           | 2026-12-01 00:00:00 |
      | code               | PROMO_order_2025    |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    And I add 1 product "product1" to the cart "dummy_cart"
    And I add 1 product "product2" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$46.81'
    And I use a voucher "complete_amount_order_level_discount" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$36.81'
    Then my cart "dummy_cart" should have the following details:
      | total_products | $39.81  |
      | shipping       | $7.00   |
      | total_discount | -$10.00 |
      | total          | $36.81  |
    Then I disable feature flag "discount"

  Scenario: Create a complete order level flat discount greater that product total using new CQRS
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And I enable feature flag "discount"
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 20.0 and 1000 items in stock
    When I create a "order_level" discount "complete_amount_order_level_discount_greater" with following properties:
      | name[en-US]        | Promotion                |
      | name[fr-FR]        | Promotion_fr             |
      | active             | true                     |
      | valid_from         | 2025-01-01 11:05:00      |
      | valid_to           | 2026-12-01 00:00:00      |
      | code               | PROMO_order_greater_2025 |
      | reduction_amount   | 45.0                     |
      | reduction_currency | usd                      |
      | taxIncluded        | true                     |
    And discount "complete_amount_order_level_discount_greater" should have the following properties:
      | name[en-US]        | Promotion                |
      | name[fr-FR]        | Promotion_fr             |
      | active             | true                     |
      | valid_from         | 2025-01-01 11:05:00      |
      | valid_to           | 2026-12-01 00:00:00      |
      | code               | PROMO_order_greater_2025 |
      | reduction_amount   | 45.0                     |
      | reduction_currency | usd                      |
      | taxIncluded        | true                     |
    And I add 1 product "product1" to the cart "dummy_cart"
    And I add 1 product "product2" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$46.81'
    And I use a voucher "complete_amount_order_level_discount_greater" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$1.81'
    Then my cart "dummy_cart" should have the following details:
      | total_products | $39.81  |
      | shipping       | $7.00   |
      | total_discount | -$45.00 |
      | total          | $1.81   |
    Then I disable feature flag "discount"

  Scenario: Create a complete order level flat discount greater that product total using new CQRS
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And I enable feature flag "discount"
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 20.0 and 1000 items in stock
    When I create a "order_level" discount "complete_amount_order_level_discount_too_generous" with following properties:
      | name[en-US]        | Promotion                 |
      | name[fr-FR]        | Promotion_fr              |
      | active             | true                      |
      | valid_from         | 2025-01-01 11:05:00       |
      | valid_to           | 2026-12-01 00:00:00       |
      | code               | PROMO_order_generous_2025 |
      | reduction_amount   | 55.0                      |
      | reduction_currency | usd                       |
      | taxIncluded        | true                      |
    And discount "complete_amount_order_level_discount_too_generous" should have the following properties:
      | name[en-US]        | Promotion                 |
      | name[fr-FR]        | Promotion_fr              |
      | active             | true                      |
      | valid_from         | 2025-01-01 11:05:00       |
      | valid_to           | 2026-12-01 00:00:00       |
      | code               | PROMO_order_generous_2025 |
      | reduction_amount   | 55.0                      |
      | reduction_currency | usd                       |
      | taxIncluded        | true                      |
    And I add 1 product "product1" to the cart "dummy_cart"
    And I add 1 product "product2" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$46.81'
    And I use a voucher "complete_amount_order_level_discount_too_generous" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$0.00'
    Then my cart "dummy_cart" should have the following details:
      | total_products | $39.81  |
      | shipping       | $7.00   |
      | total_discount | -$46.81 |
      | total          | $0.00   |
    Then I disable feature flag "discount"

  Scenario: Create a complete order level percent discount using new CQRS
    Given I create an empty cart "dummy_cart_2" for customer "testCustomer2"
    And I enable feature flag "discount"
    And there is a product in the catalog named "product3" with a price of 19.9 and 1000 items in stock
    When I create a "order_level" discount "complete_percent_order_level_discount" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2025-01-01 11:05:00 |
      | valid_to          | 2026-12-01 00:00:00 |
      | code              | PROMO_order_2025_2  |
      | reduction_percent | 50.0                |
    And discount "complete_percent_order_level_discount" should have the following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2025-01-01 11:05:00 |
      | valid_to          | 2026-12-01 00:00:00 |
      | code              | PROMO_order_2025_2  |
      | reduction_percent | 50.0                |
    And I add 1 product "product3" to the cart "dummy_cart_2"
    And cart "dummy_cart_2" total with tax included should be '$26.90'
    And I use a voucher "complete_percent_order_level_discount" on the cart "dummy_cart_2"
    And cart "dummy_cart_2" total with tax included should be '$13.45'
    Then my cart "dummy_cart_2" should have the following details:
      | total_products | $19.90  |
      | shipping       | $7.00   |
      | total_discount | -$13.45 |
      | total          | $13.45  |
    Then I disable feature flag "discount"
