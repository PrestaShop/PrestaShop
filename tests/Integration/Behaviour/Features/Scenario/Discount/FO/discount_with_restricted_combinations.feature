# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-condition-restricted-combinations
@restore-all-tables-before-feature
@discount-condition-restricted-combinations
Feature: Full UX discount test
  PrestaShop allows discounts with restricted combinations as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  # Note: in this feature file scenarios are related because this initialisation must be done only once
  Scenario: First create combinations that will be used in following scenarios
    Given attribute group "Size" named "Size" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    Given I add product "metal_tshirt" with following information:
      | name[en-US] | metal tshirt |
      | type        | combinations |
    When I generate combinations for product metal_tshirt using following attributes:
      | Size | [S,M,L] |
    Then product "metal_tshirt" should have following combinations:
      | id reference | combination name | reference | attributes | impact on price | quantity | is default |
      | metalTshirtS | Size - S         |           | [Size:S]   | 0               | 0        | true       |
      | metalTshirtM | Size - M         |           | [Size:M]   | 0               | 0        | false      |
      | metalTshirtL | Size - L         |           | [Size:L]   | 0               | 0        | false      |
    And I enable product "metal_tshirt"
    And I update product "metal_tshirt" with following values:
      | price | 12.00 |
    And I update combination "metalTshirtS" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalTshirtM" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalTshirtL" stock with following details:
      | delta quantity | 100 |
    Given I add product "metal_sweat" with following information:
      | name[en-US] | metal sweat  |
      | type        | combinations |
    When I generate combinations for product metal_sweat using following attributes:
      | Size | [S,M,L] |
    Then product "metal_sweat" should have following combinations:
      | id reference | combination name | reference | attributes | impact on price | quantity | is default |
      | metalSweatS  | Size - S         |           | [Size:S]   | 0               | 0        | true       |
      | metalSweatM  | Size - M         |           | [Size:M]   | 0               | 0        | false      |
      | metalSweatL  | Size - L         |           | [Size:L]   | 0               | 0        | false      |
    And I enable product "metal_sweat"
    And I update product "metal_sweat" with following values:
      | price | 18.00 |
    And I update combination "metalSweatS" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatM" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatL" stock with following details:
      | delta quantity | 100 |
    # Now create the discount
    When I create a "free_shipping" discount "free_shipping_discount_with_restricted_combinations" with following properties:
      | name[en-US] | Three Tshirt M or L size get free shipping        |
      | name[fr-FR] | Livraison offerte pour Trois tshirt taille M ou L |
      | active      | true                                              |
      | valid_from  | 2025-01-01 11:05:00                               |
      | valid_to    | 2026-12-01 00:00:00                               |
      | code        | FREE_SHIPPING_THREE_COMBINATIONS                  |
    And I update discount "free_shipping_discount_with_restricted_combinations" with following conditions matching at least 3 products:
      | condition_type | items                      |
      | combinations   | metalTshirtM, metalTshirtL |
    Then discount "free_shipping_discount_with_restricted_combinations" should have the following properties:
      | name[en-US]              | Three Tshirt M or L size get free shipping        |
      | name[fr-FR]              | Livraison offerte pour Trois tshirt taille M ou L |
      | active                   | true                                              |
      | valid_from               | 2025-01-01 11:05:00                               |
      | valid_to                 | 2026-12-01 00:00:00                               |
      | code                     | FREE_SHIPPING_THREE_COMBINATIONS                  |
      | minimum_product_quantity | 0                                                 |
    And discount "free_shipping_discount_with_restricted_combinations" should have the following product conditions matching at least 3 products:
      | condition_type | items                      |
      | combinations   | metalTshirtM, metalTshirtL |

  Scenario: Free shipping with three t shirts L
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 2 combinations "metalTshirtL" from product "metal_tshirt" to the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$31.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $24.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $31.00 |
    When I use a voucher "free_shipping_discount_with_restricted_combinations" on the cart "dummy_cart"
    Then I should get cart rule validation error
    # First try the condition is not met so the cart remains unchanged
    Then cart "dummy_cart" total with tax included should be '$31.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $24.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $31.00 |
    # Now we add the missing third combination
    When I add 1 combination "metalTshirtL" from product "metal_tshirt" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$43.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $43.00 |
    When I use a voucher "free_shipping_discount_with_restricted_combinations" on the cart "dummy_cart"
    # Now the condition is met so the cart shipping is offered
    Then cart "dummy_cart" total with tax included should be '$36.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $36.00 |

  Scenario: Free shipping with 2 tshirts M and 1 tshirt L (apply on first try)
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 2 combinations "metalTshirtM" from product "metal_tshirt" to the cart "dummy_cart"
    And I add 1 combination "metalTshirtL" from product "metal_tshirt" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$43.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $43.00 |
    When I use a voucher "free_shipping_discount_with_restricted_combinations" on the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$36.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $36.00 |

  Scenario: Three combinations but only two matching the rule
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 1 combination "metalTshirtM" from product "metal_tshirt" to the cart "dummy_cart"
    And I add 1 combination "metalTshirtL" from product "metal_tshirt" to the cart "dummy_cart"
    And I add 1 combination "metalSweatL" from product "metal_sweat" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$49.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $42.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $49.00 |
    When I use a voucher "free_shipping_discount_with_restricted_combinations" on the cart "dummy_cart"
    Then I should get cart rule validation error
    And cart "dummy_cart" total with tax included should be '$49.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $42.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $49.00 |
    # With one more combination it matches the condition
    When I add 1 combination "metalTshirtL" from product "metal_tshirt" to the cart "dummy_cart"
    And I use a voucher "free_shipping_discount_with_restricted_combinations" on the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$54.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $54.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $54.00 |
