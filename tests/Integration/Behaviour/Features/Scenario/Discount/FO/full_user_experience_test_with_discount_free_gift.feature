# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags full-ux-discount-test-free-gift
@restore-all-tables-before-feature
@full-ux-discount-test-free-gift

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

  Scenario: Create a complete discount with free gift using new CQRS
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    Given there is a product "hummingbird-tshirt-simple" with name "Hummingbird printed t-shirt"
    And there is a product in the catalog named "product1" with a price of 20.0 and 1000 items in stock
    When I create a "free_gift" discount "complete_free_gift_discount" with following properties:
      | name[en-US]  | Promotion                 |
      | name[fr-FR]  | Promotion_fr              |
      | active       | true                      |
      | valid_from   | 2025-01-01 11:05:00       |
      | valid_to     | 2026-12-01 00:00:00       |
      | code         | FREE_GIFT_2025            |
      | gift_product | hummingbird-tshirt-simple |
    And discount "complete_free_gift_discount" should have the following properties:
      | name[en-US]  | Promotion                 |
      | name[fr-FR]  | Promotion_fr              |
      | active       | true                      |
      | valid_from   | 2025-01-01 11:05:00       |
      | valid_to     | 2026-12-01 00:00:00       |
      | code         | FREE_GIFT_2025            |
      | gift_product | hummingbird-tshirt-simple |
    And I add 1 product "product1" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$27.00'
    And I use a voucher "complete_free_gift_discount" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$27.00'
    Then my cart "dummy_cart" should have the following details:
      | total_products | $20.00 |
      | total_discount | $0.00  |
      | shipping       | $7.00  |
      | total          | $27.00 |
    Then gifted product "Hummingbird printed t-shirt" quantity in cart "dummy_cart" should be 1

  Scenario: Create a complete discount with free gift with combination using new CQRS
    Given I create an empty cart "dummy_cart_combination" for customer "testCustomer"
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And I add product "hummingbird-tshirt" with following information:
      | name[en-US] | Hummingbird printed t-shirt new |
      | type        | combinations                    |
    And product "hummingbird-tshirt" type should be combinations
    And product "hummingbird-tshirt" does not have a default combination
    And I enable product "hummingbird-tshirt"
    And I generate combinations for product "hummingbird-tshirt" using following attributes:
      | Size  | [S,M]        |
      | Color | [White,Blue] |
    And product "hummingbird-tshirt" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "hummingbird-tshirt" default combination should be "product1SWhite"
    And product "hummingbird-tshirt" should have the following combination ids:
      | id reference   |
      | product1SWhite |
      | product1SBlue  |
      | product1MWhite |
      | product1MBlue  |
    When I update combination "product1MWhite" with following values:
      | minimal quantity           | 1          |
      | low stock threshold        | 1          |
      | low stock alert is enabled | true       |
      | available date             | 2021-10-10 |
    And I update combination "product1MWhite" stock with following details:
      | delta quantity | 1           |
      | location       | Storage nr1 |
    Then combination "product1MWhite" should have following stock details:
      | combination stock detail   | value       |
      | quantity                   | 1           |
      | minimal quantity           | 1           |
      | low stock threshold        | 1           |
      | low stock alert is enabled | true        |
      | location                   | Storage nr1 |
      | available date             | 2021-10-10  |
    And there is a product in the catalog named "product1" with a price of 20.0 and 1000 items in stock
    When I create a "free_gift" discount "complete_free_combination_discount" with following properties:
      | name[en-US]      | Promotion 2         |
      | name[fr-FR]      | Promotion_2_fr      |
      | active           | true                |
      | valid_from       | 2025-01-01 11:05:00 |
      | valid_to         | 2026-12-01 00:00:00 |
      | code             | FREE_COMBI_2025     |
      | gift_product     | hummingbird-tshirt  |
      | gift_combination | product1MWhite      |
    And discount "complete_free_combination_discount" should have the following properties:
      | name[en-US]      | Promotion 2         |
      | name[fr-FR]      | Promotion_2_fr      |
      | active           | true                |
      | valid_from       | 2025-01-01 11:05:00 |
      | valid_to         | 2026-12-01 00:00:00 |
      | code             | FREE_COMBI_2025     |
      | gift_product     | hummingbird-tshirt  |
      | gift_combination | product1MWhite      |
    And I add 1 product "product1" to the cart "dummy_cart_combination"
    And cart "dummy_cart_combination" total with tax included should be '$27.00'
    And I use a voucher "complete_free_combination_discount" on the cart "dummy_cart_combination"
    And cart "dummy_cart_combination" total with tax included should be '$27.00'
    Then my cart "dummy_cart_combination" should have the following details:
      | total_products | $20.00 |
      | total_discount | $0.00  |
      | shipping       | $7.00  |
      | total          | $27.00 |
    Then gifted product "Hummingbird printed t-shirt new" quantity in cart "dummy_cart_combination" should be 1
