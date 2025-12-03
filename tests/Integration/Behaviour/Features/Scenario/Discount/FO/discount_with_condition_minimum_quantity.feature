# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-condition-minimum-quantity
@restore-all-tables-before-feature
@discount-condition-minimum-quantity
Feature: Full UX discount test
  PrestaShop allows discounts with minimum quantity as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  Scenario: Create a complete discount with free shipping using new CQRS
    And there is a product in the catalog named "product1" with a price of 10.00 and 1000 items in stock
    When I create a "free_shipping" discount "free_shipping_discount_with_minimum_quantity" with following properties:
      | name[en-US] | Four products equal free shipping  |
      | name[fr-FR] | Quatre produit donne envoi gratuit |
      | active      | true                               |
      | valid_from  | 2025-01-01 11:05:00                |
      | valid_to    | 2026-12-01 00:00:00                |
      | code        | FREE_SHIPPING_4                    |
    When I update discount "free_shipping_discount_with_minimum_quantity" with the condition it requires at least 4 products
    And discount "free_shipping_discount_with_minimum_quantity" should have the following properties:
      | name[en-US]              | Four products equal free shipping  |
      | name[fr-FR]              | Quatre produit donne envoi gratuit |
      | active                   | true                               |
      | valid_from               | 2025-01-01 11:05:00                |
      | valid_to                 | 2026-12-01 00:00:00                |
      | code                     | FREE_SHIPPING_4                    |
      | minimum_product_quantity | 4                                  |
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 3 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$37.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $37.00 |
    When I use a voucher "free_shipping_discount_with_minimum_quantity" on the cart "dummy_cart"
    Then I should get cart rule validation error
    # First try the condition is not met so the cart remains unchanged
    And cart "dummy_cart" total with tax included should be '$37.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $37.00 |
    # Now we add the missing fourth product
    When I add 1 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$47.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $47.00 |
    When I use a voucher "free_shipping_discount_with_minimum_quantity" on the cart "dummy_cart"
    # Now the condition is met so the cart shipping is offered
    And cart "dummy_cart" total with tax included should be '$40.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $40.00 |
