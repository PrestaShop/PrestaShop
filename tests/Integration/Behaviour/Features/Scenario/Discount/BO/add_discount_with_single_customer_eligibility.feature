# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-single-customer
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-single-customer
Feature: Add discount with single customer eligibility
  PrestaShop allows BO users to create discounts limited to a single customer
  As a BO user
  I must be able to create discounts that are only available to a specific customer

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given there is a customer named "john_doe" whose email is "john.doe@example.com"
    And there is a customer named "jane_smith" whose email is "jane.smith@example.com"

  Scenario: Create a discount limited to a single customer
    When I create a "cart_level" discount "discount_for_john" with following properties:
      | name[en-US]              | VIP Customer Discount |
      | name[fr-FR]              | Réduction VIP         |
      | active                   | true                  |
      | valid_from               | 2025-01-01 00:00:00   |
      | valid_to                 | 2025-12-31 23:59:59   |
      | code                     | VIP_JOHN_2025         |
      | reduction_percent        | 20.0                  |
      | customer                 | john_doe              |
      | total_quantity           | 100                   |
      | quantity_per_user        | 1                     |
      | minimum_product_quantity | 0                     |
    Then discount "discount_for_john" should have the following properties:
      | name[en-US]       | VIP Customer Discount |
      | name[fr-FR]       | Réduction VIP         |
      | type              | cart_level            |
      | active            | true                  |
      | valid_from        | 2025-01-01 00:00:00   |
      | valid_to          | 2025-12-31 23:59:59   |
      | code              | VIP_JOHN_2025         |
      | reduction_percent | 20.0                  |
      | customer          | john_doe              |

  Scenario: Create a discount without customer restriction (all customers)
    When I create a "cart_level" discount "discount_for_all" with following properties:
      | name[en-US]              | Public Discount     |
      | name[fr-FR]              | Réduction           |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2025-12-31 23:59:59 |
      | code                     | PUBLIC_2025         |
      | reduction_percent        | 10.0                |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    Then discount "discount_for_all" should have the following properties:
      | name[en-US]       | Public Discount |
      | name[fr-FR]       | Réduction       |
      | type              | cart_level      |
      | active            | true            |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | PUBLIC_2025     |
      | reduction_percent | 10.0            |

  Scenario: Update a discount to limit it to a single customer
    When I create a "cart_level" discount "discount_public_to_vip" with following properties:
      | name[en-US]              | General Discount    |
      | name[fr-FR]              | Réduction           |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2025-12-31 23:59:59 |
      | code                     | GENERAL_2025        |
      | reduction_percent        | 15.0                |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "discount_public_to_vip" should have the following properties:
      | name[en-US]       | General Discount |
      | name[fr-FR]       | Réduction        |
    When I update discount "discount_public_to_vip" with the following properties:
      | name[en-US]       | VIP Discount     |
      | name[fr-FR]       | Réduction VIP    |
      | customer          | jane_smith       |
    Then discount "discount_public_to_vip" should have the following properties:
      | name[en-US]       | VIP Discount     |
      | name[fr-FR]       | Réduction VIP    |
      | customer          | jane_smith       |

  Scenario: Update a discount to change customer restriction
    When I create a "cart_level" discount "discount_vip_to_public" with following properties:
      | name[en-US]              | VIP Discount        |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2025-12-31 23:59:59 |
      | code                     | VIP_2025            |
      | reduction_percent        | 25.0                |
      | customer                 | john_doe            |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "discount_vip_to_public" should have the following properties:
      | customer          | john_doe        |
    When I update discount "discount_vip_to_public" with the following properties:
      | name[en-US]       | Public Discount |
      | name[fr-FR]       | Réduction       |
      | customer          | jane_smith      |
    Then discount "discount_vip_to_public" should have the following properties:
      | name[en-US]       | Public Discount |
      | name[fr-FR]       | Réduction       |
      | customer          | jane_smith      |

  Scenario: Create a free shipping discount limited to a single customer
    When I create a "free_shipping" discount "free_shipping_for_jane" with following properties:
      | name[en-US]              | Free Shipping for Jane |
      | active                   | true                   |
      | valid_from               | 2025-01-01 00:00:00    |
      | valid_to                 | 2025-12-31 23:59:59    |
      | code                     | FREESHIP_JANE          |
      | customer                 | jane_smith             |
      | total_quantity           | 100                    |
      | quantity_per_user        | 1                      |
      | minimum_product_quantity | 0                      |
    Then discount "free_shipping_for_jane" should have the following properties:
      | name[en-US] | Free Shipping for Jane |
      | name[fr-FR] |                        |
      | type        | free_shipping          |
      | customer    | jane_smith             |

