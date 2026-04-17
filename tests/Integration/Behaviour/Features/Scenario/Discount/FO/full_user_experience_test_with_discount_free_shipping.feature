# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags full-ux-discount-test-free-shipping
@restore-all-tables-before-feature
@full-ux-discount-test-free-shipping

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

  Scenario: Create a complete discount with free shipping using new CQRS
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I create a "free_shipping" discount "complete_free_shipping_discount" with following properties:
      | name[en-US] | Promotion           |
      | name[fr-FR] | Promotion_fr        |
      | active      | true                |
      | valid_from  | 2025-01-01 11:05:00 |
      | valid_to    | 2026-12-01 00:00:00 |
      | code        | PROMO_2025          |
    And discount "complete_free_shipping_discount" should have the following properties:
      | name[en-US] | Promotion           |
      | name[fr-FR] | Promotion_fr        |
      | active      | true                |
      | valid_from  | 2025-01-01 11:05:00 |
      | valid_to    | 2026-12-01 00:00:00 |
      | code        | PROMO_2025          |
    And I add 1 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$26.81'
    And I use a voucher "complete_free_shipping_discount" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$19.81'
    Then my cart "dummy_cart" should have the following details:
      | total_products | $19.81 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $19.81 |
