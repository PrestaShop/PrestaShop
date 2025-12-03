# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-customer-groups
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-customer-groups
Feature: Add discount with customer groups eligibility
  PrestaShop allows BO users to create discounts limited to specific customer groups
  As a BO user
  I must be able to create discounts that are only available to specific customer groups

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    And I create a customer group "group1" with the following details:
      | name[en-US]             | VIP Members       |
      | name[fr-FR]             | Membres VIP       |
      | reduction               | 0.0               |
      | displayPriceTaxExcluded | false             |
      | showPrice               | true              |
      | shopIds                 | shop1             |
    And I create a customer group "group2" with the following details:
      | name[en-US]             | Premium Members   |
      | name[fr-FR]             | Membres Premium   |
      | reduction               | 0.0               |
      | displayPriceTaxExcluded | false             |
      | showPrice               | true              |
      | shopIds                 | shop1             |

  Scenario: Create a discount limited to a single customer group
    When I create a "cart_level" discount "discount_for_vip" with following properties:
      | name[en-US]              | VIP discount        |
      | name[fr-FR]              | Réduction VIP       |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2025-12-31 23:59:59 |
      | code                     | VIP_GROUP_2025      |
      | reduction_percent        | 20.0                |
      | customer_groups          | group1              |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    Then discount "discount_for_vip" should have the following properties:
      | name[en-US]       | VIP discount        |
      | name[fr-FR]       | Réduction VIP       |
      | type              | cart_level          |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | VIP_GROUP_2025      |
      | reduction_percent | 20.0                |
      | customer_groups   | group1              |

  Scenario: Create a discount limited to multiple customer groups
    When I create a "cart_level" discount "discount_for_vip_and_premium" with following properties:
      | name[en-US]              | VIP and Premium discount |
      | name[fr-FR]              | Réduction VIP et Premium |
      | active                   | true                     |
      | valid_from               | 2025-01-01 00:00:00      |
      | valid_to                 | 2025-12-31 23:59:59      |
      | code                     | VIP_PREMIUM_2025         |
      | reduction_percent        | 15.0                     |
      | customer_groups          | group1,group2            |
      | total_quantity           | 100                      |
      | quantity_per_user        | 1                        |
      | minimum_product_quantity | 0                        |
    Then discount "discount_for_vip_and_premium" should have the following properties:
      | name[en-US]       | VIP and Premium discount |
      | name[fr-FR]       | Réduction VIP et Premium |
      | type              | cart_level               |
      | active            | true                     |
      | valid_from        | 2025-01-01 00:00:00      |
      | valid_to          | 2025-12-31 23:59:59      |
      | code              | VIP_PREMIUM_2025         |
      | reduction_percent | 15.0                     |
      | customer_groups   | group1,group2            |

  Scenario: Create a discount without customer group restriction (all customers)
    When I create a "cart_level" discount "discount_for_all" with following properties:
      | name[en-US]              | Public discount     |
      | name[fr-FR]              | Réduction publique  |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2025-12-31 23:59:59 |
      | code                     | PUBLIC_2025         |
      | reduction_percent        | 10.0                |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    Then discount "discount_for_all" should have the following properties:
      | name[en-US]       | Public discount     |
      | name[fr-FR]       | Réduction publique  |
      | type              | cart_level          |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:59 |
      | code              | PUBLIC_2025         |
      | reduction_percent | 10.0                |
      | customer_groups   |                     |

  Scenario: Update a discount to limit it to specific customer groups
    When I create a "cart_level" discount "discount_public_to_groups" with following properties:
      | name[en-US]              | General discount    |
      | name[fr-FR]              | Réduction générale  |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2025-12-31 23:59:59 |
      | code                     | GENERAL_2025        |
      | reduction_percent        | 15.0                |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "discount_public_to_groups" should have the following properties:
      | name[en-US]       | General discount   |
      | name[fr-FR]       | Réduction générale |
    When I update discount "discount_public_to_groups" with the following properties:
      | name[en-US]       | VIP only            |
      | name[fr-FR]       | Réservé VIP         |
      | customer_groups   | group1              |
    Then discount "discount_public_to_groups" should have the following properties:
      | name[en-US]       | VIP only     |
      | name[fr-FR]       | Réservé VIP  |
      | customer_groups   | group1       |

  Scenario: Update a discount to change customer groups
    When I create a "cart_level" discount "discount_change_groups" with following properties:
      | name[en-US]              | VIP discount        |
      | name[fr-FR]              | Réduction VIP       |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2025-12-31 23:59:59 |
      | code                     | VIP_ONLY_2025       |
      | reduction_percent        | 25.0                |
      | customer_groups          | group1              |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "discount_change_groups" should have the following properties:
      | customer_groups   | group1           |
    When I update discount "discount_change_groups" with the following properties:
      | name[en-US]       | Premium discount  |
      | name[fr-FR]       | Réduction Premium |
      | customer_groups   | group2            |
    Then discount "discount_change_groups" should have the following properties:
      | name[en-US]       | Premium discount  |
      | name[fr-FR]       | Réduction Premium |
      | customer_groups   | group2            |

  Scenario: Update a discount to remove all customer groups (switch to all customers)
    When I create a "cart_level" discount "discount_remove_groups" with following properties:
      | name[en-US]              | VIP discount        |
      | name[fr-FR]              | Réduction VIP       |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2025-12-31 23:59:59 |
      | code                     | VIP_TEMP_2025       |
      | reduction_percent        | 20.0                |
      | customer_groups          | group1              |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "discount_remove_groups" should have the following properties:
      | customer_groups   | group1           |
    When I update discount "discount_remove_groups" with the following properties:
      | name[en-US]       | Public discount    |
      | name[fr-FR]       | Réduction publique |
      | customer_groups   |                    |
    Then discount "discount_remove_groups" should have the following properties:
      | name[en-US]       | Public discount    |
      | name[fr-FR]       | Réduction publique |
      | customer_groups   |                    |

  Scenario: Create a free shipping discount limited to customer groups
    When I create a "free_shipping" discount "free_shipping_for_vip" with following properties:
      | name[en-US]              | Free shipping VIP      |
      | name[fr-FR]              | Livraison gratuite VIP |
      | active                   | true                   |
      | valid_from               | 2025-01-01 00:00:00    |
      | valid_to                 | 2025-12-31 23:59:59    |
      | code                     | FREESHIP_VIP           |
      | customer_groups          | group1                 |
      | total_quantity           | 100                    |
      | quantity_per_user        | 1                      |
      | minimum_product_quantity | 0                      |
    Then discount "free_shipping_for_vip" should have the following properties:
      | name[en-US]       | Free shipping VIP      |
      | name[fr-FR]       | Livraison gratuite VIP |
      | type              | free_shipping          |
      | customer_groups   | group1                 |

