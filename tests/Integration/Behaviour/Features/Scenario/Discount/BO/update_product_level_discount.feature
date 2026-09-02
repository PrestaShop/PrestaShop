# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags update-product-level-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@update-product-level-discount
Feature: Update discount
  PrestaShop allows BO users to update discounts
  As a BO user
  I must be able to update discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 39.812 and 1000 items in stock
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists

  Scenario: Create a complete product level discount
    When I create a "product_level" discount "updated_product_level_discount" with following properties:
      | name[en-US]       | Promotion                      |
      | name[fr-FR]       | Promotion_fr                   |
      | active            | true                           |
      | valid_from        | 2019-01-01 11:05:00            |
      | valid_to          | 2019-12-01 00:00:00            |
      | code              | updated_product_level_discount |
      | reduction_percent | 10.0                           |
      | cheapest_product  | true                           |
    And discount "updated_product_level_discount" should have the following properties:
      | name[en-US]            | Promotion                      |
      | name[fr-FR]            | Promotion_fr                   |
      | type                   | product_level                  |
      | active                 | true                           |
      | valid_from             | 2019-01-01 11:05:00            |
      | valid_to               | 2019-12-01 00:00:00            |
      | code                   | updated_product_level_discount |
      | reduction_percent      | 10.0                           |
      | reduction_amount       |                                |
      | reduction_currency     |                                |
      | reduction_tax_included |                                |
      | cheapest_product       | true                           |
    # Switch to a product segment target instead, cheapest_product should now be false (partial update)
    Then I update discount "updated_product_level_discount" with the following properties:
      | reduction_percent          | 15.0     |
      | productConditionQuantity   | 42       |
      | productCondition[products] | product1 |
    Then discount "updated_product_level_discount" should have the following properties:
      | name[en-US]                | Promotion                      |
      | name[fr-FR]                | Promotion_fr                   |
      | type                       | product_level                  |
      | active                     | true                           |
      | valid_from                 | 2019-01-01 11:05:00            |
      | valid_to                   | 2019-12-01 00:00:00            |
      | code                       | updated_product_level_discount |
      | reduction_percent          | 15.0                           |
      | cheapest_product           | false                          |
      | reduction_product          |                                |
      | productConditionQuantity   | 42                             |
      | productCondition[products] | product1                       |
    # Switch to a single product target instead, no more product segment (partial update)
    Then I update discount "updated_product_level_discount" with the following properties:
      | reduction_product | product1 |
    Then discount "updated_product_level_discount" should have the following properties:
      | name[en-US]              | Promotion                      |
      | name[fr-FR]              | Promotion_fr                   |
      | type                     | product_level                  |
      | active                   | true                           |
      | valid_from               | 2019-01-01 11:05:00            |
      | valid_to                 | 2019-12-01 00:00:00            |
      | code                     | updated_product_level_discount |
      | reduction_percent        | 15.0                           |
      | cheapest_product         | false                          |
      | reduction_product        | product1                       |
      | productConditionQuantity |                                |
      | productCondition         |                                |
    # Switch back to a product segment target instead, reduction_product should now be null (partial update)
    Then I update discount "updated_product_level_discount" with the following properties:
      | reduction_percent          | 15.0     |
      | productConditionQuantity   | 42       |
      | productCondition[products] | product1 |
    Then discount "updated_product_level_discount" should have the following properties:
      | name[en-US]                | Promotion                      |
      | name[fr-FR]                | Promotion_fr                   |
      | type                       | product_level                  |
      | active                     | true                           |
      | valid_from                 | 2019-01-01 11:05:00            |
      | valid_to                   | 2019-12-01 00:00:00            |
      | code                       | updated_product_level_discount |
      | reduction_percent          | 15.0                           |
      | cheapest_product           | false                          |
      | reduction_product          |                                |
      | productConditionQuantity   | 42                             |
      | productCondition[products] | product1                       |
    # Switch back to cheapest product, no more product segment, nor single product (partial update)
    When I update discount "updated_product_level_discount" with the following properties:
      | cheapest_product | true |
    Then discount "updated_product_level_discount" should have the following properties:
      | name[en-US]              | Promotion                      |
      | name[fr-FR]              | Promotion_fr                   |
      | type                     | product_level                  |
      | active                   | true                           |
      | valid_from               | 2019-01-01 11:05:00            |
      | valid_to                 | 2019-12-01 00:00:00            |
      | code                     | updated_product_level_discount |
      | reduction_percent        | 15.0                           |
      | cheapest_product         | true                           |
      | reduction_product        |                                |
      # Assert product conditions are empty
      | productConditionQuantity |                                |
      | productCondition         |                                |
    # Switch to a product segment target and cheapest product false (explicit for both)
    When I update discount "updated_product_level_discount" with the following properties:
      | cheapest_product           | false    |
      | productConditionQuantity   | 42       |
      | productCondition[products] | product1 |
    Then discount "updated_product_level_discount" should have the following properties:
      | cheapest_product           | false    |
      | productConditionQuantity   | 42       |
      | productCondition[products] | product1 |
    # Now update the percent discount to a fixed amount discount, the product conditions should not be removed
    When I update discount "updated_product_level_discount" with the following properties:
      | reduction_amount       | 5.0  |
      | reduction_currency     | usd  |
      | reduction_tax_included | true |
    Then discount "updated_product_level_discount" should have the following properties:
      | name[en-US]                | Promotion                      |
      | name[fr-FR]                | Promotion_fr                   |
      | type                       | product_level                  |
      | active                     | true                           |
      | valid_from                 | 2019-01-01 11:05:00            |
      | valid_to                   | 2019-12-01 00:00:00            |
      | code                       | updated_product_level_discount |
      # No more reduction percent
      | reduction_percent          |                                |
      | reduction_amount           | 5.0                            |
      | reduction_currency         | usd                            |
      | reduction_tax_included     | true                           |
      # Target is left untouched
      | cheapest_product           | false                          |
      | productConditionQuantity   | 42                             |
      | productCondition[products] | product1                       |
    # Switch cheapest product without a product segment (explicit for both)
    When I update discount "updated_product_level_discount" with the following properties:
      | cheapest_product         | true |
      | productConditionQuantity |      |
      | productCondition         |      |
    Then discount "updated_product_level_discount" should have the following properties:
      | reduction_amount         | 5.0  |
      | reduction_currency       | usd  |
      | reduction_tax_included   | true |
      | cheapest_product         | true |
      | productConditionQuantity |      |
      | productCondition         |      |
    # Now update the percent discount to a fixed amount discount
    When I update discount "updated_product_level_discount" with the following properties:
      | reduction_amount       | 5.0  |
      | reduction_currency     | usd  |
      | reduction_tax_included | true |
    # Finally check that we can update back to reduction percent
    When I update discount "updated_product_level_discount" with the following properties:
      | reduction_percent | 10.0 |
    Then discount "updated_product_level_discount" should have the following properties:
      # No more reduction percent
      | reduction_percent      | 10.0 |
      | reduction_amount       |      |
      | reduction_currency     |      |
      | reduction_tax_included |      |

  Scenario: Check that updating product level to an invalid state is forbidden
    # First create a valid product level discount
    When I create a "product_level" discount "valid_product_level" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | valid_product_level |
      | reduction_percent | 10.0                |
      | cheapest_product  | true                |
    Then discount "valid_product_level" should have the following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | type              | product_level       |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | valid_product_level |
      | reduction_percent | 10.0                |
      | cheapest_product  | true                |
    # Try to remove the cheapest product condition, without a product segment
    When I update discount "valid_product_level" with the following properties:
      | cheapest_product | false |
    Then I should get an error that the discount target is missing
    And discount "valid_product_level" should have the following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | type              | product_level       |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | valid_product_level |
      | reduction_percent | 10.0                |
      | cheapest_product  | true                |
    # Now replace with single product, cheapest product becomes false
    When I update discount "valid_product_level" with the following properties:
      | reduction_product | product1 |
    Then discount "valid_product_level" should have the following properties:
      | name[en-US]              | Promotion           |
      | name[fr-FR]              | Promotion_fr        |
      | type                     | product_level       |
      | active                   | true                |
      | valid_from               | 2019-01-01 11:05:00 |
      | valid_to                 | 2019-12-01 00:00:00 |
      | code                     | valid_product_level |
      | reduction_percent        | 10.0                |
      | reduction_product        | product1            |
      | cheapest_product         | false               |
      | productConditionQuantity |                     |
      | productCondition         |                     |
    When I update discount "valid_product_level" with the following properties:
      | reduction_product |  |
    Then I should get an error that the discount target is missing
    # Now replace with a product segment, this is valid
    When I update discount "valid_product_level" with the following properties:
      | cheapest_product           | false    |
      | productConditionQuantity   | 42       |
      | productCondition[products] | product1 |
    Then discount "valid_product_level" should have the following properties:
      | name[en-US]                | Promotion           |
      | name[fr-FR]                | Promotion_fr        |
      | type                       | product_level       |
      | active                     | true                |
      | valid_from                 | 2019-01-01 11:05:00 |
      | valid_to                   | 2019-12-01 00:00:00 |
      | code                       | valid_product_level |
      | reduction_percent          | 10.0                |
      | cheapest_product           | false               |
      | productConditionQuantity   | 42                  |
      | productCondition[products] | product1            |
    # Now replace with an empty product segment, this is invalid
    When I update discount "valid_product_level" with the following properties:
      | productConditionQuantity |  |
      | productCondition         |  |
    Then I should get an error that the discount target is missing
    And discount "valid_product_level" should have the following properties:
      | name[en-US]                | Promotion           |
      | name[fr-FR]                | Promotion_fr        |
      | type                       | product_level       |
      | active                     | true                |
      | valid_from                 | 2019-01-01 11:05:00 |
      | valid_to                   | 2019-12-01 00:00:00 |
      | code                       | valid_product_level |
      | reduction_percent          | 10.0                |
      | reduction_amount           |                     |
      | reduction_currency         |                     |
      | reduction_tax_included     |                     |
      | cheapest_product           | false               |
      | productConditionQuantity   | 42                  |
      | productCondition[products] | product1            |
    # We force both cheapest product and a segment product -> it is invalid (if only one was provided it's a valid partial update)
    When I update discount "valid_product_level" with the following properties:
      | cheapest_product           | true     |
      | productConditionQuantity   | 42       |
      | productCondition[products] | product1 |
    Then I should get an error that the discount targets are incompatible
    # Now replace with a a percent AND a fixed amount discount, this is invalid
    When I update discount "valid_product_level" with the following properties:
      | reduction_percent      | 10.0 |
      | reduction_amount       | 5.0  |
      | reduction_currency     | usd  |
      | reduction_tax_included | true |
    Then I should get an error that the discount reductions are incompatible
    And discount "valid_product_level" should have the following properties:
      | name[en-US]                | Promotion           |
      | name[fr-FR]                | Promotion_fr        |
      | type                       | product_level       |
      | active                     | true                |
      | valid_from                 | 2019-01-01 11:05:00 |
      | valid_to                   | 2019-12-01 00:00:00 |
      | code                       | valid_product_level |
      | reduction_percent          | 10.0                |
      | reduction_amount           |                     |
      | reduction_currency         |                     |
      | reduction_tax_included     |                     |
      | cheapest_product           | false               |
      | productConditionQuantity   | 42                  |
      | productCondition[products] | product1            |
