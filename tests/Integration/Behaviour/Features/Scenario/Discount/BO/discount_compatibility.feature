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
      | name[en-US]       | Cart Discount 10%            |
      | name[fr-FR]       | Remise Panier 10%            |
      | active            | true                         |
      | valid_from        | 2025-01-01 00:00:00          |
      | valid_to          | 2025-12-31 23:59:59          |
      | code              | CART10                       |
      | reduction_percent | 10.0                         |
      | compatible_types  | free_shipping, product_level |
    Then discount "cart_discount_1" should have the following properties:
      | name[en-US]       | Cart Discount 10%            |
      | name[fr-FR]       | Remise Panier 10%            |
      | type              | cart_level                   |
      | active            | true                         |
      | code              | CART10                       |
      | reduction_percent | 10.0                         |
      | compatible_types  | free_shipping, product_level |

  Scenario: Update discount compatible types
    When I create a "cart_level" discount "cart_discount_2" with following properties:
      | name[en-US]       | Cart Discount 15%   |
      | name[fr-FR]       | Remise Panier 15%   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | CART15              |
      | reduction_percent | 15.0                |
      | compatible_types  | free_shipping       |
    Then discount "cart_discount_2" should have the following properties:
      | name[en-US]       | Cart Discount 15%   |
      | name[fr-FR]       | Remise Panier 15%   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | CART15              |
      | reduction_percent | 15.0                |
      | compatible_types  | free_shipping       |
    When I update discount "cart_discount_2" with the following properties:
      | compatible_types | free_shipping, product_level, order_level |
    Then discount "cart_discount_2" should have the following properties:
      | name[en-US]       | Cart Discount 15%                         |
      | name[fr-FR]       | Remise Panier 15%                         |
      | active            | true                                      |
      | valid_from        | 2025-01-01 00:00:00                       |
      | valid_to          | 2025-12-31 23:59:59                       |
      | code              | CART15                                    |
      | reduction_percent | 15.0                                      |
      | compatible_types  | free_shipping, product_level, order_level |

  Scenario: Create multiple discounts with different compatible types
    When I create a "cart_level" discount "cart_discount_3" with following properties:
      | name[en-US]       | Cart Discount 20%        |
      | active            | true                     |
      | valid_from        | 2025-01-01 00:00:00      |
      | valid_to          | 2025-12-31 23:59:59      |
      | code              | CART20                   |
      | reduction_percent | 20.0                     |
      | compatible_types  | free_shipping, free_gift |
    When I create a "free_shipping" discount "free_ship_discount" with following properties:
      | name[en-US]      | Free Shipping                          |
      | active           | true                                   |
      | valid_from       | 2025-01-01 00:00:00                    |
      | valid_to         | 2025-12-31 23:59:59                    |
      | code             | FREESHIP                               |
      | compatible_types | cart_level, product_level, order_level |
    Then discount "cart_discount_3" should have the following properties:
      | compatible_types | free_shipping, free_gift |
    And discount "free_ship_discount" should have the following properties:
      | compatible_types | cart_level, product_level, order_level |

  Scenario: Create discount with no compatible types (exclusive discount)
    When I create a "cart_level" discount "exclusive_discount" with following properties:
      | name[en-US]       | Exclusive 50% Off   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | EXCLUSIVE50         |
      | reduction_percent | 50.0                |
      | compatible_types  |                     |
    Then discount "exclusive_discount" should have the following properties:
      | compatible_types |  |

  Scenario: Create discount with no compatible types by default (exclusive discount)
    When I create a "cart_level" discount "default_exclusive_discount" with following properties:
      | name[en-US]       | Exclusive 50% Off   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | DEFAULTEXCLUSIVE50  |
      | reduction_percent | 50.0                |
    Then discount "default_exclusive_discount" should have the following properties:
      | compatible_types |  |

  Scenario: Clear all compatible types from discount
    When I create a "cart_level" discount "cart_discount_4" with following properties:
      | name[en-US]       | Cart Discount                             |
      | active            | true                                      |
      | valid_from        | 2025-01-01 00:00:00                       |
      | valid_to          | 2025-12-31 23:59:59                       |
      | code              | CLEARTEST                                 |
      | reduction_percent | 10.0                                      |
      | compatible_types  | free_shipping, product_level, order_level |
    Then discount "cart_discount_4" should have the following properties:
      | compatible_types | free_shipping, product_level, order_level |
    When I update discount "cart_discount_4" with the following properties:
      | compatible_types |  |
    Then discount "cart_discount_4" should have the following properties:
      | compatible_types |  |

  Scenario: Create discount with all types compatible
    When I create a "cart_level" discount "universal_discount" with following properties:
      | name[en-US]       | Universal Discount                                               |
      | active            | true                                                             |
      | valid_from        | 2025-01-01 00:00:00                                              |
      | valid_to          | 2025-12-31 23:59:59                                              |
      | code              | UNIVERSAL                                                        |
      | reduction_percent | 5.0                                                              |
      | compatible_types  | cart_level, order_level, product_level, free_shipping, free_gift |
    Then discount "universal_discount" should have the following properties:
      | compatible_types | cart_level, order_level, product_level, free_shipping, free_gift |
