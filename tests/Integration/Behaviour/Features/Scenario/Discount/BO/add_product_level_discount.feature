# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-product-level-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-product-level-discount
Feature: Add discount
  PrestaShop allows BO users to create discounts
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given attribute group "Size" named "Size" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists

  Scenario: Not really a test but create the products to use (not done in Background to avoid repeating it for each scenarios)
    Given I add product "beer_product" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And I add product "potato_chips_product" with following information:
      | name[en-US] | potato chips |
      | type        | standard     |
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

  Scenario: Create a product level discount with one product as the target
    When I create a "product_level" discount "product_discount_one_product" with following properties:
      | name[en-US]                | Promotion                    |
      | name[fr-FR]                | Promotion_fr                 |
      | active                     | true                         |
      | valid_from                 | 2019-01-01 11:05:00          |
      | valid_to                   | 2019-12-01 00:00:00          |
      | code                       | product_discount_one_product |
      | reduction_percent          | 10.0                         |
      | productConditionQuantity   | 42                           |
      | productCondition[products] | beer_product                 |
    And discount "product_discount_one_product" should have the following properties:
      | name[en-US]                | Promotion                    |
      | name[fr-FR]                | Promotion_fr                 |
      | type                       | product_level                |
      | active                     | true                         |
      | valid_from                 | 2019-01-01 11:05:00          |
      | valid_to                   | 2019-12-01 00:00:00          |
      | code                       | product_discount_one_product |
      | reduction_percent          | 10.0                         |
      | reduction_amount           |                              |
      | reduction_currency         |                              |
      | reduction_tax_included     |                              |
      | productConditionQuantity   | 42                           |
      | productCondition[products] | beer_product                 |

  Scenario: Create a product level discount with multiple products as the target
    When I create a "product_level" discount "product_discount_multiple_products" with following properties:
      | name[en-US]                | Promotion                          |
      | name[fr-FR]                | Promotion_fr                       |
      | active                     | true                               |
      | valid_from                 | 2019-01-01 11:05:00                |
      | valid_to                   | 2019-12-01 00:00:00                |
      | code                       | product_discount_multiple_products |
      | reduction_percent          | 10.0                               |
      | productConditionQuantity   | 42                                 |
      | productCondition[products] | potato_chips_product, metal_tshirt |
    And discount "product_discount_multiple_products" should have the following properties:
      | name[en-US]                | Promotion                          |
      | name[fr-FR]                | Promotion_fr                       |
      | type                       | product_level                      |
      | active                     | true                               |
      | valid_from                 | 2019-01-01 11:05:00                |
      | valid_to                   | 2019-12-01 00:00:00                |
      | code                       | product_discount_multiple_products |
      | reduction_percent          | 10.0                               |
      | productConditionQuantity   | 42                                 |
      | productCondition[products] | potato_chips_product, metal_tshirt |

  Scenario: Create a product level discount with products and combinations as targets
    When I create a "product_level" discount "product_discount_products_and_combinations" with following properties:
      | name[en-US]                    | Promotion                                  |
      | name[fr-FR]                    | Promotion_fr                               |
      | active                         | true                                       |
      | valid_from                     | 2019-01-01 11:05:00                        |
      | valid_to                       | 2019-12-01 00:00:00                        |
      | code                           | product_discount_products_and_combinations |
      | reduction_percent              | 10.0                                       |
      | productConditionQuantity       | 42                                         |
      | productCondition[products]     | potato_chips_product                       |
      | productCondition[combinations] | metalTshirtS, metalTshirtL                 |
    And discount "product_discount_products_and_combinations" should have the following properties:
      | name[en-US]                    | Promotion                                  |
      | name[fr-FR]                    | Promotion_fr                               |
      | type                           | product_level                              |
      | active                         | true                                       |
      | valid_from                     | 2019-01-01 11:05:00                        |
      | valid_to                       | 2019-12-01 00:00:00                        |
      | code                           | product_discount_products_and_combinations |
      | reduction_percent              | 10.0                                       |
      | productConditionQuantity       | 42                                         |
      | productCondition[products]     | potato_chips_product                       |
      | productCondition[combinations] | metalTshirtS, metalTshirtL                 |

  Scenario: Create a product level discount with product segment based on attributes
    When I create a "product_level" discount "product_discount_attributes" with following properties:
      | name[en-US]                  | Promotion                   |
      | name[fr-FR]                  | Promotion_fr                |
      | active                       | true                        |
      | valid_from                   | 2019-01-01 11:05:00         |
      | valid_to                     | 2019-12-01 00:00:00         |
      | code                         | product_discount_attributes |
      | reduction_percent            | 10.0                        |
      | productConditionQuantity     | 42                          |
      | productCondition[attributes] | S, L                        |
    And discount "product_discount_attributes" should have the following properties:
      | name[en-US]                  | Promotion                   |
      | name[fr-FR]                  | Promotion_fr                |
      | type                         | product_level               |
      | active                       | true                        |
      | valid_from                   | 2019-01-01 11:05:00         |
      | valid_to                     | 2019-12-01 00:00:00         |
      | code                         | product_discount_attributes |
      | reduction_percent            | 10.0                        |
      | productConditionQuantity     | 42                          |
      | productCondition[attributes] | S, L                        |

  Scenario: Create a product level discount targeting cheapest product
    When I create a "product_level" discount "product_discount_cheapest" with following properties:
      | name[en-US]       | Promotion                 |
      | name[fr-FR]       | Promotion_fr              |
      | active            | true                      |
      | valid_from        | 2019-01-01 11:05:00       |
      | valid_to          | 2019-12-01 00:00:00       |
      | code              | product_discount_cheapest |
      | reduction_percent | 10.0                      |
      | cheapest_product  | true                      |
    And discount "product_discount_cheapest" should have the following properties:
      | name[en-US]              | Promotion                 |
      | name[fr-FR]              | Promotion_fr              |
      | type                     | product_level             |
      | active                   | true                      |
      | valid_from               | 2019-01-01 11:05:00       |
      | valid_to                 | 2019-12-01 00:00:00       |
      | code                     | product_discount_cheapest |
      | reduction_percent        | 10.0                      |
      | cheapest_product         | true                      |
      # This represents an empty set of rules
      | productConditionQuantity |                           |
      | productCondition         |                           |

  Scenario: Create a product level discount targeting single product
    When I create a "product_level" discount "product_discount_single" with following properties:
      | name[en-US]       | Promotion               |
      | name[fr-FR]       | Promotion_fr            |
      | active            | true                    |
      | valid_from        | 2019-01-01 11:05:00     |
      | valid_to          | 2019-12-01 00:00:00     |
      | code              | product_discount_single |
      | reduction_percent | 10.0                    |
      | reduction_product | beer_product            |
    And discount "product_discount_single" should have the following properties:
      | name[en-US]              | Promotion               |
      | name[fr-FR]              | Promotion_fr            |
      | type                     | product_level           |
      | active                   | true                    |
      | valid_from               | 2019-01-01 11:05:00     |
      | valid_to                 | 2019-12-01 00:00:00     |
      | code                     | product_discount_single |
      | reduction_percent        | 10.0                    |
      | reduction_product        | beer_product            |
      | cheapest_product         | false                   |
      # This represents an empty set of rules
      | productConditionQuantity |                         |
      | productCondition         |                         |

  Scenario: Create a product level discount targeting cheapest product with amount
    When I create a "product_level" discount "product_discount_cheapest_amount" with following properties:
      | name[en-US]              | Promotion                        |
      | name[fr-FR]              | Promotion_fr                     |
      | active                   | true                             |
      | valid_from               | 2019-01-01 11:05:00              |
      | valid_to                 | 2019-12-01 00:00:00              |
      | code                     | product_discount_cheapest_amount |
      | reduction_amount         | 5.0                              |
      | reduction_currency       | usd                              |
      | reduction_tax_included   | true                             |
      | cheapest_product         | true                             |
      # This represents an empty set of rules
      | productConditionQuantity |                                  |
      | productCondition         |                                  |
    And discount "product_discount_cheapest_amount" should have the following properties:
      | name[en-US]            | Promotion                        |
      | name[fr-FR]            | Promotion_fr                     |
      | type                   | product_level                    |
      | active                 | true                             |
      | valid_from             | 2019-01-01 11:05:00              |
      | valid_to               | 2019-12-01 00:00:00              |
      | code                   | product_discount_cheapest_amount |
      | reduction_amount       | 5.0                              |
      | reduction_currency     | usd                              |
      | reduction_tax_included | true                             |
      | cheapest_product       | true                             |

  Scenario: Create a product level with invalid properties is forbidden
    # No target defined because default value is empty
    When I create a "product_level" discount "product_discount_no_target" with following properties:
      | name[en-US]       | Promotion                  |
      | name[fr-FR]       | Promotion_fr               |
      | active            | true                       |
      | valid_from        | 2019-01-01 11:05:00        |
      | valid_to          | 2019-12-01 00:00:00        |
      | code              | product_discount_no_target |
      | reduction_percent | 5.0                        |
    Then I should get an error that the discount target is missing
    # This time, no target defined because it is explicit
    When I create a "product_level" discount "product_discount_no_target_explicit" with following properties:
      | name[en-US]              | Promotion                           |
      | name[fr-FR]              | Promotion_fr                        |
      | active                   | true                                |
      | valid_from               | 2019-01-01 11:05:00                 |
      | valid_to                 | 2019-12-01 00:00:00                 |
      | code                     | product_discount_no_target_explicit |
      | reduction_percent        | 5.0                                 |
      | cheapest_product         | false                               |
      # This represents an empty set of rules
      | productConditionQuantity |                                     |
      | productCondition         |                                     |
    Then I should get an error that the discount target is missing
    # Try to set both cheapest and product segment
    When I create a "product_level" discount "product_discount_incompatible_target" with following properties:
      | name[en-US]                | Promotion                            |
      | name[fr-FR]                | Promotion_fr                         |
      | active                     | true                                 |
      | valid_from                 | 2019-01-01 11:05:00                  |
      | valid_to                   | 2019-12-01 00:00:00                  |
      | code                       | product_discount_incompatible_target |
      | reduction_percent          | 5.0                                  |
      | cheapest_product           | true                                 |
      | productConditionQuantity   | 42                                   |
      | productCondition[products] | potato_chips_product, metal_tshirt   |
    Then I should get an error that the discount targets are incompatible
    # Try to set single product and product segment
    When I create a "product_level" discount "product_discount_incompatible_target" with following properties:
      | name[en-US]                | Promotion                            |
      | name[fr-FR]                | Promotion_fr                         |
      | active                     | true                                 |
      | valid_from                 | 2019-01-01 11:05:00                  |
      | valid_to                   | 2019-12-01 00:00:00                  |
      | code                       | product_discount_incompatible_target |
      | reduction_percent          | 5.0                                  |
      | reduction_product          | potato_chips_product                 |
      | productConditionQuantity   | 42                                   |
      | productCondition[products] | potato_chips_product, metal_tshirt   |
    Then I should get an error that the discount targets are incompatible
    # Try to set single product and cheapest product is forbidden
    When I create a "product_level" discount "product_discount_incompatible_target" with following properties:
      | name[en-US]              | Promotion                            |
      | name[fr-FR]              | Promotion_fr                         |
      | active                   | true                                 |
      | valid_from               | 2019-01-01 11:05:00                  |
      | valid_to                 | 2019-12-01 00:00:00                  |
      | code                     | product_discount_incompatible_target |
      | reduction_percent        | 5.0                                  |
      | reduction_product        | potato_chips_product                 |
      | cheapest_product         | true                                 |
      | productConditionQuantity |                                      |
      | productCondition         |                                      |
    Then I should get an error that the discount targets are incompatible
    # Try to create a discount with no reduction (neither percent nor amount)
    When I create a "product_level" discount "product_discount_no_reduction" with following properties:
      | name[en-US]      | Promotion                     |
      | name[fr-FR]      | Promotion_fr                  |
      | active           | true                          |
      | valid_from       | 2019-01-01 11:05:00           |
      | valid_to         | 2019-12-01 00:00:00           |
      | code             | product_discount_no_reduction |
      | cheapest_product | true                          |
    Then I should get an error that the discount reduction is missing
    # Try to create a discount with both reduction (percent and amount)
    When I create a "product_level" discount "product_discount_both_reduction" with following properties:
      | name[en-US]            | Promotion                     |
      | name[fr-FR]            | Promotion_fr                  |
      | active                 | true                          |
      | valid_from             | 2019-01-01 11:05:00           |
      | valid_to               | 2019-12-01 00:00:00           |
      | code                   | product_discount_no_reduction |
      | cheapest_product       | true                          |
      # We set percetn AND amount at the same time
      | reduction_percent      | 5.0                           |
      | reduction_amount       | 5.0                           |
      | reduction_currency     | usd                           |
      | reduction_tax_included | true                          |
    Then I should get an error that the discount reductions are incompatible
