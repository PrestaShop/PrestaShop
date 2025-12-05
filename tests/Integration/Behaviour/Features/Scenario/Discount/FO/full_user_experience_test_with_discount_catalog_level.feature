# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags full-ux-discount-test-catalog-level
@restore-all-tables-before-feature
@full-ux-discount-test-catalog-level

Feature: Full UX discount test
  PrestaShop allows BO users to create catalog discounts
  As a BO user
  I must be able to create catalog discounts that apply per product unit

  Background:
    Given there is a customer named "testCustomer" whose email is "pub3@prestashop.com"
    Given there is a customer named "testCustomer2" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  Scenario: Catalog discount percentage on cheapest product
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 20.0 and 1000 items in stock
    When I create a "catalog_level" discount "complete_percent_catalog_level_discount" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2025-01-01 11:05:00 |
      | valid_to          | 2026-12-01 00:00:00 |
      | code              | PROMO_CART_2025_2   |
      | reduction_percent | 50.0                |
      | reduction_product | -1                  |
    And discount "complete_percent_catalog_level_discount" should have the following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2025-01-01 11:05:00 |
      | valid_to          | 2026-12-01 00:00:00 |
      | code              | PROMO_CART_2025_2   |
      | reduction_percent | 50.0                |
      | reduction_product | -1                  |
    And I add 1 product "product1" to the cart "dummy_cart"
    And I add 1 product "product2" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$46.81'
    And I use a voucher "complete_percent_catalog_level_discount" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$36.90'
    Then my cart "dummy_cart" should have the following details:
      | total_products | $39.81 |
      | total_discount | -$9.91 |
      | shipping       | $7.00  |
      | total          | $36.90 |

  Scenario: Catalog discount percentage on specific product
    Given I create an empty cart "dummy_cart_2" for customer "testCustomer2"
    And there is a product in the catalog named "product3" with a price of 19.9 and 1000 items in stock
    When I create a "catalog_level" discount "complete_percent_catalog_level_discount" with following properties:
      | name[en-US]       | Promotion2          |
      | name[fr-FR]       | Promotion_2_fr      |
      | active            | true                |
      | valid_from        | 2025-01-01 11:05:00 |
      | valid_to          | 2026-12-01 00:00:00 |
      | code              | PROMO_CART_2025_3   |
      | reduction_percent | 50.0                |
      | reduction_product | product3            |
    And discount "complete_percent_catalog_level_discount" should have the following properties:
      | name[en-US]       | Promotion2          |
      | name[fr-FR]       | Promotion_2_fr      |
      | active            | true                |
      | valid_from        | 2025-01-01 11:05:00 |
      | valid_to          | 2026-12-01 00:00:00 |
      | code              | PROMO_CART_2025_3   |
      | reduction_percent | 50.0                |
      | reduction_product | product3            |
    And I add 1 product "product3" to the cart "dummy_cart_2"
    And cart "dummy_cart_2" total with tax included should be '$26.90'
    And I use a voucher "complete_percent_catalog_level_discount" on the cart "dummy_cart_2"
    And cart "dummy_cart_2" total with tax included should be '$16.95'
    Then my cart "dummy_cart_2" should have the following details:
      | total_products | $19.90 |
      | total_discount | -$9.95 |
      | shipping       | $7.00  |
      | total          | $16.95 |

  Scenario: Catalog discount flat amount - KEY TEST - discount multiplies by quantity
    Given I create an empty cart "cart_flat_multiply" for customer "testCustomer"
    And there is a product in the catalog named "product4" with a price of 10.0 and 1000 items in stock
    When I create a "catalog_level" discount "flat_multiply_discount" with following properties:
      | name[en-US]        | Flat $2 off per unit |
      | name[fr-FR]        | 2$ par unité         |
      | active             | true                 |
      | valid_from         | 2025-01-01 00:00:00  |
      | valid_to           | 2026-12-31 23:59:59  |
      | code               | FLAT2                |
      | reduction_amount   | 2.0                  |
      | reduction_currency | usd                  |
      | taxIncluded        | false                |
      | reduction_product  | product4             |
    And I add 3 products "product4" to the cart "cart_flat_multiply"
    And I use a voucher "flat_multiply_discount" on the cart "cart_flat_multiply"
    # KEY ASSERTION: 3 products × $2 discount = -$6 total (not -$2)
    Then my cart "cart_flat_multiply" should have the following details:
      | total_products | $30.00 |
      | total_discount | -$6.00 |
      | shipping       | $7.00  |
      | total          | $31.00 |
