# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-condition-restricted-products
@restore-all-tables-before-feature
@discount-condition-restricted-products
Feature: Full UX discount test
  PrestaShop allows discounts with restricted products as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  # Note: in this feature file scenarios are related because this initialisation must be done only once
  Scenario: First create products and the discount that will be used in following scenarios
    Given I add product "beer_product" with following information:
      | name[en-US] | bottle of beer     |
      | name[fr-FR] | bouteille de bière |
      | type        | standard           |
    And I update product "beer_product" stock with following information:
      | delta_quantity | 51 |
    And I enable product "beer_product"
    And I update product "beer_product" with following values:
      | price | 20.00 |
    And I add product "potato_chips_product" with following information:
      | name[en-US] | potato chips |
      | name[fr-FR] | chips        |
      | type        | standard     |
    And I update product "potato_chips_product" stock with following information:
      | delta_quantity | 123 |
    And I enable product "potato_chips_product"
    And I update product "potato_chips_product" with following values:
      | price | 5.00 |
    # This one is not part of the condition it is used to ensure the condition only works with matching products
    And I add product "nuggets_bucket_product" with following information:
      | name[en-US] | nuggets bucket |
      | name[fr-FR] | sot de nuggets |
      | type        | standard       |
    And I update product "nuggets_bucket_product" stock with following information:
      | delta_quantity | 123 |
    And I enable product "nuggets_bucket_product"
    And I update product "nuggets_bucket_product" with following values:
      | price | 12.00 |
    # Now create the discount
    When I create a "free_shipping" discount "free_shipping_discount_with_restricted_products" with following properties:
      | name[en-US] | Trois bières et/ou chips equal free shipping |
      | name[fr-FR] | Trois bière et/ou chips donne envoi gratuit  |
      | active      | true                                         |
      | valid_from  | 2025-01-01 11:05:00                          |
      | valid_to    | 2026-12-01 00:00:00                          |
      | code        | FREE_SHIPPING_THREE_PRODUCTS                 |
    And I update discount "free_shipping_discount_with_restricted_products" with following conditions matching at least 3 products:
      | condition_type | items                              |
      | products       | beer_product, potato_chips_product |
    Then discount "free_shipping_discount_with_restricted_products" should have the following properties:
      | name[en-US]              | Trois bières et/ou chips equal free shipping |
      | name[fr-FR]              | Trois bière et/ou chips donne envoi gratuit  |
      | active                   | true                                         |
      | valid_from               | 2025-01-01 11:05:00                          |
      | valid_to                 | 2026-12-01 00:00:00                          |
      | code                     | FREE_SHIPPING_THREE_PRODUCTS                 |
      | minimum_product_quantity | 0                                            |
    And discount "free_shipping_discount_with_restricted_products" should have the following product conditions matching at least 3 products:
      | condition_type | items                              |
      | products       | beer_product, potato_chips_product |

  Scenario: Free shipping with three bottle of beers
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 2 product "bottle of beer" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$47.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $47.00 |
    When I use a voucher "free_shipping_discount_with_restricted_products" on the cart "dummy_cart"
    Then I should get cart rule validation error
    # First try the condition is not met so the cart remains unchanged
    And cart "dummy_cart" total with tax included should be '$47.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $40.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $47.00 |
    # Now we add the missing third product
    When I add 1 product "bottle of beer" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$67.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $60.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $67.00 |
    When I use a voucher "free_shipping_discount_with_restricted_products" on the cart "dummy_cart"
    # Now the condition is met so the cart shipping is offered
    Then cart "dummy_cart" total with tax included should be '$60.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $60.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $60.00 |

  Scenario: Free shipping with 1 bottle of beer and 2 chips (apply on first try)
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 1 product "bottle of beer" to the cart "dummy_cart"
    And I add 2 products "potato chips" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$37.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $37.00 |
    When I use a voucher "free_shipping_discount_with_restricted_products" on the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$30.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $30.00 |

  Scenario: Three products but only two matching the rule
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 1 product "bottle of beer" to the cart "dummy_cart"
    And I add 1 product "potato chips" to the cart "dummy_cart"
    And I add 1 product "nuggets bucket" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$44.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $37.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $44.00 |
    When I use a voucher "free_shipping_discount_with_restricted_products" on the cart "dummy_cart"
    Then I should get cart rule validation error
    And cart "dummy_cart" total with tax included should be '$44.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $37.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $44.00 |
    # With one more product it matches the condition
    When I add 1 product "potato chips" to the cart "dummy_cart"
    And I use a voucher "free_shipping_discount_with_restricted_products" on the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$42.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $42.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $42.00 |
