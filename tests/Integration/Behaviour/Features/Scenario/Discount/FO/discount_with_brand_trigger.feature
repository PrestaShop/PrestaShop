# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-with-brand-trigger
@restore-all-tables-before-feature
@discount-with-brand-trigger
Feature: Add discount with brand trigger on FO
  PrestaShop allows discounts with restricted products as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  Scenario: First create products and the discount that will be used in following scenarios
    Given I add new manufacturer "rocket" with following properties:
      | name                     | Rocket                             |
      | short_description[en-US] | Cigarettes manufacturer            |
      | description[en-US]       | Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.|
      | meta_title[en-US]        | You smoke - you die!               |
      | meta_description[en-US]  | The sun is shining and the weather is sweet|
      | enabled                  | true                               |
    And I add product "beer_product" with following information:
      | name[en-US] | bottle of beer     |
      | name[fr-FR] | bouteille de bière |
      | type        | standard           |
    And I update product "beer_product" with following values:
      | manufacturer | rocket            |
    And I update product "beer_product" stock with following information:
      | delta_quantity | 51 |
    And I enable product "beer_product"
    And I update product "beer_product" with following values:
      | price | 20.00 |
    And I add product "potato_chips_product" with following information:
      | name[en-US] | potato chips |
      | name[fr-FR] | chips        |
      | type        | standard     |
    And I update product "potato_chips_product" with following values:
      | manufacturer | rocket            |
    And I update product "potato_chips_product" stock with following information:
      | delta_quantity | 123 |
    And I enable product "potato_chips_product"
    And I update product "potato_chips_product" with following values:
      | price | 5.00 |
    When I create a "cart_level" discount "discount_with_brand_trigger" with following properties:
      | name[en-US] | Promotion |
      | name[fr-FR] | Promotion |
      | code        | CART_LEVEL_BRAND_TRIGGER  |
      | active             | true                |
      | reduction_amount   | 5.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    Then discount "discount_with_brand_trigger" should have the following properties:
      | name[en-US] | Promotion     |
      | name[fr-FR] | Promotion     |
      | type        | cart_level |
      | code               | CART_LEVEL_BRAND_TRIGGER     |
      | active             | true                |
      | reduction_amount   | 5.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    When I update discount "discount_with_brand_trigger" with following conditions matching at least 10 products:
      | condition_type | items  |
      | manufacturers  | rocket |
    Then discount "discount_with_brand_trigger" should have the following properties:
      | minimum_product_quantity | 0             |
    And discount "discount_with_brand_trigger" should have the following product conditions matching at least 10 products:
      | condition_type | items  |
      | manufacturers  | rocket |

  Scenario: Reduction with brand trigger on FO
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 5 product "bottle of beer" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$107.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $107.00 |
    # We try to apply the discount with only 5 products, it should not work
    When I use a voucher "discount_with_brand_trigger" on the cart "dummy_cart"
    Then I should get cart rule validation error
    And cart "dummy_cart" total with tax included should be '$107.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $107.00 |
    When I add 4 product "potato chips" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$127.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $120.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $127.00 |
    # Now we try to apply the discount with 5 beers and 4 chips, it should not work (quantity should be 10)
    When I use a voucher "discount_with_brand_trigger" on the cart "dummy_cart"
    Then I should get cart rule validation error
    And cart "dummy_cart" total with tax included should be '$127.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $120.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $127.00 |
    # Now we add the missing quantity to apply the discount, it should be ok
    When I add 1 product "potato chips" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$132.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $125.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $132.00 |
    When I use a voucher "discount_with_brand_trigger" on the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$127.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $125.00 |
      | shipping       | $7.00  |
      | total_discount | -$5.00 |
      | total          | $127.00 |
