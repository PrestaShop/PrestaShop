# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags full-ux-discount-test-product-level
@full-ux-discount-test-product-level
@restore-all-tables-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
Feature: Full UX discount test
  PrestaShop allows BO users to create discounts
  As a BO user
  I must be able to create discounts using the new discounts

  Background:
    # The new rules for product level are only computed when the feature flag is enabled
    Given I enable feature flag "discount"
    Given there is a customer named "testCustomer" whose email is "pub3@prestashop.com"
    Given there is a customer named "testCustomer2" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a product in the catalog named "cheap_product" with a price of 10.0 and 1000 items in stock
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 20.0 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 19.9 and 1000 items in stock
    And there is a product in the catalog named "product4" with a price of 7.99 and 1000 items in stock

  Scenario: Create product level with 50% discount on the cheapest product
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I create a "product_level" discount "cheapest_product_half_price_discount" with following properties:
      | name[en-US]       | Promotion                            |
      | name[fr-FR]       | Promotion_fr                         |
      | active            | true                                 |
      | valid_from        | 2025-01-01 11:05:00                  |
      | valid_to          | 2026-12-01 00:00:00                  |
      | code              | cheapest_product_half_price_discount |
      | reduction_percent | 50.0                                 |
      | cheapest_product  | true                                 |
    And discount "cheapest_product_half_price_discount" should have the following properties:
      | name[en-US]       | Promotion                            |
      | name[fr-FR]       | Promotion_fr                         |
      | active            | true                                 |
      | valid_from        | 2025-01-01 11:05:00                  |
      | valid_to          | 2026-12-01 00:00:00                  |
      | code              | cheapest_product_half_price_discount |
      | reduction_percent | 50.0                                 |
      | cheapest_product  | true                                 |
    And I add 1 product "product1" to the cart "dummy_cart"
    And I add 1 product "product2" to the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$46.81'
    When I use a voucher "cheapest_product_half_price_discount" on the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$36.90'
    And my cart "dummy_cart" should have the following details:
      | total_products | $39.81 |
      | total_discount | -$9.91 |
      | shipping       | $7.00  |
      | total          | $36.90 |
    And the cart "dummy_cart" should have the following reductions:
      | cheapest_product_half_price_discount | 9.906 |
    # Add another cheaper product the discount should adapt
    And I add 1 product "cheap_product" to the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$51.81'
    And my cart "dummy_cart" should have the following details:
      | total_products | $49.81 |
      | total_discount | -$5.00 |
      | shipping       | $7.00  |
      | total          | $51.81 |
    And the cart "dummy_cart" should have the following reductions:
      | cheapest_product_half_price_discount | 5.0 |
    # Add another cheaper product the discount is applied twice (once for each product targeted)
    And I add 1 product "cheap_product" to the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$56.81'
    And my cart "dummy_cart" should have the following details:
      | total_products | $59.81  |
      | total_discount | -$10.00 |
      | shipping       | $7.00   |
      | total          | $56.81  |
    And the cart "dummy_cart" should have the following reductions:
      | cheapest_product_half_price_discount | 10.00 |
    # Now remove the cheapest product, the new cheapest product is product3 again
    When I delete product "cheap_product" from cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$36.90'
    And my cart "dummy_cart" should have the following details:
      | total_products | $39.81 |
      | total_discount | -$9.91 |
      | shipping       | $7.00  |
      | total          | $36.90 |
    And the cart "dummy_cart" should have the following reductions:
      | cheapest_product_half_price_discount | 9.906 |

  Scenario: Create product level with 50% discount on a specific product
    Given I create an empty cart "dummy_cart2" for customer "testCustomer2"
    When I create a "product_level" discount "specific_product_half_price_discount" with following properties:
      | name[en-US]              | Promotion2                           |
      | name[fr-FR]              | Promotion_2_fr                       |
      | active                   | true                                 |
      | valid_from               | 2025-01-01 11:05:00                  |
      | valid_to                 | 2026-12-01 00:00:00                  |
      | code                     | specific_product_half_price_discount |
      | reduction_percent        | 50.0                                 |
      | reduction_product        | product3                             |
      | productConditionQuantity |                                      |
      | productCondition         |                                      |
    And discount "specific_product_half_price_discount" should have the following properties:
      | name[en-US]              | Promotion2                           |
      | name[fr-FR]              | Promotion_2_fr                       |
      | active                   | true                                 |
      | valid_from               | 2025-01-01 11:05:00                  |
      | valid_to                 | 2026-12-01 00:00:00                  |
      | code                     | specific_product_half_price_discount |
      | reduction_percent        | 50.0                                 |
      | reduction_product        | product3                             |
      | productConditionQuantity |                                      |
      | productCondition         |                                      |
    # Despite adding a cheaper product the discount is applied on product3
    When I add 1 product "cheap_product" to the cart "dummy_cart2"
    And I add 1 product "product3" to the cart "dummy_cart2"
    Then cart "dummy_cart2" total with tax included should be '$36.90'
    And my cart "dummy_cart2" should have the following details:
      | total_products | $29.90 |
      | total_discount | $0.00  |
      | shipping       | $7.00  |
      | total          | $36.90 |
    When I use a voucher "specific_product_half_price_discount" on the cart "dummy_cart2"
    Then cart "dummy_cart2" total with tax included should be '$26.95'
    And my cart "dummy_cart2" should have the following details:
      | total_products | $29.90 |
      | total_discount | -$9.95 |
      | shipping       | $7.00  |
      | total          | $26.95 |
    And the cart "dummy_cart2" should have the following reductions:
      | specific_product_half_price_discount | 9.95 |
    # Add another product3, the discount is updated because it applies on all quantities
    When I add 1 product "product3" to the cart "dummy_cart2"
    Then cart "dummy_cart2" total with tax included should be '$36.90'
    And my cart "dummy_cart2" should have the following details:
      | total_products | $49.80  |
      | total_discount | -$19.90 |
      | shipping       | $7.00   |
      | total          | $36.90  |
    And the cart "dummy_cart2" should have the following reductions:
      | specific_product_half_price_discount | 19.90 |

  Scenario: Create product level with 6$ discount on a specific product
    Given I create an empty cart "dummy_cart3" for customer "testCustomer2"
    When I create a "product_level" discount "specific_product_fixed_price_discount" with following properties:
      | name[en-US]            | Promotion3                            |
      | name[fr-FR]            | Promotion_3_fr                        |
      | active                 | true                                  |
      | valid_from             | 2025-01-01 11:05:00                   |
      | valid_to               | 2026-12-01 00:00:00                   |
      | code                   | specific_product_fixed_price_discount |
      | reduction_amount       | 6.0                                   |
      | reduction_currency     | usd                                   |
      | reduction_tax_included | true                                  |
      | reduction_product      | product3                              |
    And discount "specific_product_fixed_price_discount" should have the following properties:
      | name[en-US]              | Promotion3                            |
      | name[fr-FR]              | Promotion_3_fr                        |
      | active                   | true                                  |
      | valid_from               | 2025-01-01 11:05:00                   |
      | valid_to                 | 2026-12-01 00:00:00                   |
      | code                     | specific_product_fixed_price_discount |
      | reduction_percent        |                                       |
      | reduction_amount         | 6.0                                   |
      | reduction_currency       | usd                                   |
      | reduction_tax_included   | true                                  |
      | reduction_product        | product3                              |
      | productConditionQuantity |                                       |
      | productCondition         |                                       |
    # Despite adding a cheaper product the discount is applied on product3
    And I add 1 product "cheap_product" to the cart "dummy_cart3"
    And I add 1 product "product3" to the cart "dummy_cart3"
    Then cart "dummy_cart3" total with tax included should be '$36.90'
    When I use a voucher "specific_product_fixed_price_discount" on the cart "dummy_cart3"
    Then cart "dummy_cart3" total with tax included should be '$30.90'
    And my cart "dummy_cart3" should have the following details:
      | total_products | $29.90 |
      | total_discount | -$6.00 |
      | shipping       | $7.00  |
      | total          | $30.90 |
    And the cart "dummy_cart3" should have the following reductions:
      | specific_product_fixed_price_discount | 6.00 |

  Scenario: Create product level with 4$ discount on the cheapest product
    Given I create an empty cart "dummy_cart4" for customer "testCustomer"
    When I create a "product_level" discount "cheapest_product_fixed_price_discount" with following properties:
      | name[en-US]            | Promotion4                            |
      | name[fr-FR]            | Promotion_4_fr                        |
      | active                 | true                                  |
      | valid_from             | 2025-01-01 11:05:00                   |
      | valid_to               | 2026-12-01 00:00:00                   |
      | code                   | cheapest_product_fixed_price_discount |
      | reduction_amount       | 4.0                                   |
      | reduction_currency     | usd                                   |
      | reduction_tax_included | true                                  |
      | cheapest_product       | true                                  |
    And discount "cheapest_product_fixed_price_discount" should have the following properties:
      | name[en-US]            | Promotion4                            |
      | name[fr-FR]            | Promotion_4_fr                        |
      | active                 | true                                  |
      | valid_from             | 2025-01-01 11:05:00                   |
      | valid_to               | 2026-12-01 00:00:00                   |
      | code                   | cheapest_product_fixed_price_discount |
      | reduction_percent      |                                       |
      | reduction_amount       | 4.0                                   |
      | reduction_currency     | usd                                   |
      | reduction_tax_included | true                                  |
      | cheapest_product       | true                                  |
    And I add 1 product "product1" to the cart "dummy_cart4"
    Then cart "dummy_cart4" total with tax included should be '$26.81'
    When I use a voucher "cheapest_product_fixed_price_discount" on the cart "dummy_cart4"
    Then cart "dummy_cart4" total with tax included should be '$22.81'
    And my cart "dummy_cart4" should have the following details:
      | total_products | $19.81 |
      | total_discount | -$4.00 |
      | shipping       | $7.00  |
      | total          | $22.81 |
    And the cart "dummy_cart4" should have the following reductions:
      | cheapest_product_fixed_price_discount | 4.00 |
    # Add another same product still the cheapest
    And I add 1 product "product1" to the cart "dummy_cart4"
    Then cart "dummy_cart4" total with tax included should be '$38.62'
    And my cart "dummy_cart4" should have the following details:
      | total_products | $39.62 |
      | total_discount | -$8.00 |
      | shipping       | $7.00  |
      | total          | $38.62 |
    And the cart "dummy_cart4" should have the following reductions:
      | cheapest_product_fixed_price_discount | 8.00 |
    # Now add a cheaper product the discount is back to 4 because there is only 1 quantity
    And I add 1 product "cheap_product" to the cart "dummy_cart4"
    Then cart "dummy_cart4" total with tax included should be '$52.62'
    And my cart "dummy_cart4" should have the following details:
      | total_products | $49.62 |
      | total_discount | -$4.00 |
      | shipping       | $7.00  |
      | total          | $52.62 |
    And the cart "dummy_cart4" should have the following reductions:
      | cheapest_product_fixed_price_discount | 4.00 |
    # Now add two more cheaper products
    And I add 2 product "cheap_product" to the cart "dummy_cart4"
    Then cart "dummy_cart4" total with tax included should be '$64.62'
    And my cart "dummy_cart4" should have the following details:
      | total_products | $69.62  |
      | total_discount | -$12.00 |
      | shipping       | $7.00   |
      | total          | $64.62  |
    And the cart "dummy_cart4" should have the following reductions:
      | cheapest_product_fixed_price_discount | 12.00 |

  Scenario: Create product level with 50% discount on a selection of products
    Given I create an empty cart "dummy_cart5" for customer "testCustomer"
    When I create a "product_level" discount "selected_products_half_price_discount" with following properties:
      | name[en-US]                | Promotion                             |
      | name[fr-FR]                | Promotion_fr                          |
      | active                     | true                                  |
      | valid_from                 | 2025-01-01 11:05:00                   |
      | valid_to                   | 2026-12-01 00:00:00                   |
      | code                       | selected_products_half_price_discount |
      | reduction_percent          | 50.0                                  |
      | productConditionQuantity   | 1                                     |
      | productCondition[products] | product1, product3                    |
    And discount "selected_products_half_price_discount" should have the following properties:
      | name[en-US]                | Promotion                             |
      | name[fr-FR]                | Promotion_fr                          |
      | active                     | true                                  |
      | valid_from                 | 2025-01-01 11:05:00                   |
      | valid_to                   | 2026-12-01 00:00:00                   |
      | code                       | selected_products_half_price_discount |
      | reduction_percent          | 50.0                                  |
      | cheapest_product           | false                                 |
      | productConditionQuantity   | 1                                     |
      | productCondition[products] | product1, product3                    |
    And I add 1 product "product1" to the cart "dummy_cart5"
    And I add 1 product "product2" to the cart "dummy_cart5"
    And I add 2 product "product3" to the cart "dummy_cart5"
    Then cart "dummy_cart5" total with tax included should be '$86.61'
    And my cart "dummy_cart5" should have the following details:
      | total_products | $79.61 |
      | total_discount | $0.00  |
      | shipping       | $7.00  |
      | total          | $86.61 |
    When I use a voucher "selected_products_half_price_discount" on the cart "dummy_cart5"
    Then cart "dummy_cart5" total with tax included should be '$56.80'
    And my cart "dummy_cart5" should have the following details:
      | total_products | $79.61  |
      | total_discount | -$29.81 |
      | shipping       | $7.00   |
      | total          | $56.80  |
    And the cart "dummy_cart5" should have the following reductions:
      | selected_products_half_price_discount | 29.806 |

  Scenario: Create product level with 9$ discount on a selection of products
    Given I create an empty cart "dummy_cart6" for customer "testCustomer"
    When I create a "product_level" discount "selected_products_fixed_price_discount" with following properties:
      | name[en-US]                | Promotion                              |
      | name[fr-FR]                | Promotion_fr                           |
      | active                     | true                                   |
      | valid_from                 | 2025-01-01 11:05:00                    |
      | valid_to                   | 2026-12-01 00:00:00                    |
      | code                       | selected_products_fixed_price_discount |
      | reduction_amount           | 9.0                                    |
      | reduction_currency         | usd                                    |
      | reduction_tax_included     | true                                   |
      | productConditionQuantity   | 1                                      |
      | productCondition[products] | product1, product3, product4           |
    And discount "selected_products_fixed_price_discount" should have the following properties:
      | name[en-US]                | Promotion                              |
      | name[fr-FR]                | Promotion_fr                           |
      | active                     | true                                   |
      | valid_from                 | 2025-01-01 11:05:00                    |
      | valid_to                   | 2026-12-01 00:00:00                    |
      | code                       | selected_products_fixed_price_discount |
      | reduction_percent          |                                        |
      | reduction_amount           | 9.0                                    |
      | reduction_currency         | usd                                    |
      | reduction_tax_included     | true                                   |
      | cheapest_product           | false                                  |
      | productConditionQuantity   | 1                                      |
      | productCondition[products] | product1, product3, product4           |
    And I add 1 product "product1" to the cart "dummy_cart6"
    And I add 1 product "product2" to the cart "dummy_cart6"
    And I add 2 product "product3" to the cart "dummy_cart6"
    And I add 1 product "product4" to the cart "dummy_cart6"
    Then cart "dummy_cart6" total with tax included should be '$94.60'
    And my cart "dummy_cart6" should have the following details:
      | total_products | $87.60 |
      | total_discount | $0.00  |
      | shipping       | $7.00  |
      | total          | $94.60 |
    When I use a voucher "selected_products_fixed_price_discount" on the cart "dummy_cart6"
    # All targeted products get a 9$, except for product4 that cannot be negative so it is capped to 0:
    #   product1: 10.812
    #   product2: 20.00
    #   product3: 10.90
    #   product4: 0.00
    Then cart "dummy_cart6" total with tax included should be '$59.61'
    And my cart "dummy_cart6" should have the following details:
      | total_products | $87.60  |
      | total_discount | -$34.99 |
      | shipping       | $7.00   |
      | total          | $59.61  |
    And the cart "dummy_cart6" should have the following reductions:
      | selected_products_fixed_price_discount | 34.99 |
