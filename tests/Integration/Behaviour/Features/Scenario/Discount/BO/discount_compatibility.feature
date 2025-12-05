# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-compatibility-bo
@restore-all-tables-before-feature
@restore-languages-after-feature
@discount-compatibility-bo
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Discount compatibility management
  PrestaShop allows BO users to configure which discount types are compatible with each other
  As a BO user
  I must be able to set compatible discount types when creating or updating discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    And I enable feature flag "discount"

  Scenario: Create a cart level discount with compatible types
    When I create a "cart_level" discount "cart_discount_1" with following properties:
      | name[en-US]       | Cart Discount 10%   |
      | name[fr-FR]       | Remise Panier 10%   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | CART10              |
      | reduction_percent | 10.0                |
    Then discount "cart_discount_1" should have the following properties:
      | name[en-US]       | Cart Discount 10%   |
      | name[fr-FR]       | Remise Panier 10%   |
      | type              | cart_level          |
      | active            | true                |
      | code              | CART10              |
      | reduction_percent | 10.0                |
    When I set compatible types for discount "cart_discount_1" to:
      | free_shipping |
      | catalog_level |
    Then discount "cart_discount_1" should be compatible with types:
      | free_shipping |
      | catalog_level |

  Scenario: Update discount compatible types
    When I create a "cart_level" discount "cart_discount_2" with following properties:
      | name[en-US]       | Cart Discount 15%   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | CART15              |
      | reduction_percent | 15.0                |
    When I set compatible types for discount "cart_discount_2" to:
      | free_shipping |
    Then discount "cart_discount_2" should be compatible with types:
      | free_shipping |
    When I set compatible types for discount "cart_discount_2" to:
      | free_shipping |
      | catalog_level |
      | order_level   |
    Then discount "cart_discount_2" should be compatible with types:
      | free_shipping |
      | catalog_level |
      | order_level   |

  Scenario: Create multiple discounts with different compatible types
    When I create a "cart_level" discount "cart_discount_3" with following properties:
      | name[en-US]       | Cart Discount 20%   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | CART20              |
      | reduction_percent | 20.0                |
    When I set compatible types for discount "cart_discount_3" to:
      | free_shipping |
      | free_gift     |
    When I create a "free_shipping" discount "free_ship_discount" with following properties:
      | name[en-US] | Free Shipping       |
      | active      | true                |
      | valid_from  | 2025-01-01 00:00:00 |
      | valid_to    | 2025-12-31 23:59:59 |
      | code        | FREESHIP            |
    When I set compatible types for discount "free_ship_discount" to:
      | cart_level    |
      | catalog_level |
      | order_level   |
    Then discount "cart_discount_3" should be compatible with types:
      | free_shipping |
      | free_gift     |
    And discount "free_ship_discount" should be compatible with types:
      | cart_level    |
      | catalog_level |
      | order_level   |

  Scenario: Create discount with no compatible types (exclusive discount)
    When I create a "cart_level" discount "exclusive_discount" with following properties:
      | name[en-US]       | Exclusive 50% Off   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | EXCLUSIVE50         |
      | reduction_percent | 50.0                |
    When I set compatible types for discount "exclusive_discount" to:
      ||
    Then discount "exclusive_discount" should be compatible with types:
      ||

  Scenario: Create product level discount with compatible types
    Given there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock
    When I create a "catalog_level" discount "product_discount_1" with following properties:
      | name[en-US]       | Product 25% Off     |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | PROD25              |
      | reduction_percent | 25.0                |
      | reduction_product | product1            |
    When I set compatible types for discount "product_discount_1" to:
      | cart_level    |
      | free_shipping |
    Then discount "product_discount_1" should be compatible with types:
      | cart_level    |
      | free_shipping |

  Scenario: Create order level discount with compatible types
    When I create a "order_level" discount "order_discount_1" with following properties:
      | name[en-US]       | Order 10% Off       |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | ORDER10             |
      | reduction_percent | 10.0                |
    When I set compatible types for discount "order_discount_1" to:
      | free_shipping |
      | free_gift     |
    Then discount "order_discount_1" should be compatible with types:
      | free_shipping |
      | free_gift     |

  Scenario: Create free gift discount with compatible types
    Given there is a product in the catalog named "gift_product" with a price of 50.0 and 1000 items in stock
    When I create a "free_gift" discount "free_gift_discount_1" with following properties:
      | name[en-US]  | Free Gift Promo     |
      | active       | true                |
      | valid_from   | 2025-01-01 00:00:00 |
      | valid_to     | 2025-12-31 23:59:59 |
      | code         | FREEGIFT            |
      | gift_product | gift_product        |
    When I set compatible types for discount "free_gift_discount_1" to:
      | cart_level  |
      | order_level |
    Then discount "free_gift_discount_1" should be compatible with types:
      | cart_level  |
      | order_level |

  Scenario: Clear all compatible types from discount
    When I create a "cart_level" discount "cart_discount_4" with following properties:
      | name[en-US]       | Cart Discount       |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | CLEARTEST           |
      | reduction_percent | 10.0                |
    When I set compatible types for discount "cart_discount_4" to:
      | free_shipping |
      | catalog_level |
      | order_level   |
    Then discount "cart_discount_4" should be compatible with types:
      | free_shipping |
      | catalog_level |
      | order_level   |
    When I set compatible types for discount "cart_discount_4" to:
      ||
    Then discount "cart_discount_4" should be compatible with types:
      ||

  Scenario: Create discount with all types compatible
    When I create a "cart_level" discount "universal_discount" with following properties:
      | name[en-US]       | Universal Discount  |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | UNIVERSAL           |
      | reduction_percent | 5.0                 |
    When I set compatible types for discount "universal_discount" to:
      | cart_level    |
      | order_level   |
      | catalog_level |
      | free_shipping |
      | free_gift     |
    Then discount "universal_discount" should be compatible with types:
      | cart_level    |
      | order_level   |
      | catalog_level |
      | free_shipping |
      | free_gift     |