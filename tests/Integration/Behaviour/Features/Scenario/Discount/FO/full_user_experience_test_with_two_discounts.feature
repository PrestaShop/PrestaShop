# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags full-ux-multiple-discount-test
@restore-all-tables-before-feature
@full-ux-multiple-discount-test
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Full UX discount test
  PrestaShop allows BO users to create discounts
  As a BO user
  I must be able to create discounts using the new discounts

  Background:
    Given there is a customer named "testCustomer" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And I enable feature flag "discount"

  Scenario: Apply two discounts to the cart and get an error
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And there is a product in the catalog named "product1" with a price of 30.0 and 1000 items in stock
    When I create a "cart_level" discount "complete_amount_cart_level_discount" with following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]        | Promotion_fr        |
      | active             | true                |
      | valid_from         | 2025-01-01 11:05:00 |
      | valid_to           | 2026-12-01 00:00:00 |
      | code               | PROMO_CART_2025     |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    And discount "complete_amount_cart_level_discount" should have the following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]        | Promotion_fr        |
      | active             | true                |
      | valid_from         | 2025-01-01 11:05:00 |
      | valid_to           | 2026-12-01 00:00:00 |
      | code               | PROMO_CART_2025     |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    When I create a "cart_level" discount "complete_amount_cart_level_discount_2" with following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]        | Promotion_fr        |
      | active             | true                |
      | valid_from         | 2025-01-01 11:05:00 |
      | valid_to           | 2026-12-01 00:00:00 |
      | code               | PROMO_CART_2025_2   |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    And discount "complete_amount_cart_level_discount_2" should have the following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]        | Promotion_fr        |
      | active             | true                |
      | valid_from         | 2025-01-01 11:05:00 |
      | valid_to           | 2026-12-01 00:00:00 |
      | code               | PROMO_CART_2025_2   |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    And I add 1 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$37.00'
    And I use a voucher "complete_amount_cart_level_discount" on the cart "dummy_cart"
    And I use a voucher "complete_amount_cart_level_discount_2" on the cart "dummy_cart"
    Then I should get an error that the discount is invalid
    Then I disable feature flag "discount"
