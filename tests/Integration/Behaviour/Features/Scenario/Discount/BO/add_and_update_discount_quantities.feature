# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-and-update-discount-quantities
@restore-all-tables-before-feature
@add-and-update-discount-quantities
@clear-cache-before-feature
@clear-cache-after-feature
Feature: Add discount
  PrestaShop allows BO users to create discounts
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language with iso code "en" is the default one
    # The new total_quantity is only used when discount feature flag is enabled
    And I enable feature flag "discount"

  Scenario: Create a discount with explicit quantity limits
    When I create a "cart_level" discount "discount_with_quantity_limits" with following properties:
      | name[en-US]       | Promotion |
      | active            | true      |
      | total_quantity    | 42        |
      | quantity_per_user | 51        |
    Then discount "discount_with_quantity_limits" should have the following properties:
      | name[en-US]        | Promotion |
      | active             | true      |
      | total_quantity     | 42        |
      | remaining_quantity | 42        |
      | quantity_per_user  | 51        |

  Scenario: Create a discount without explicit quantity limits, they are null by default (so unlimited)
    When I create a "cart_level" discount "discount_without_quantity_limits" with following properties:
      | name[en-US] | Promotion |
      | active      | true      |
    Then discount "discount_without_quantity_limits" should have the following properties:
      | name[en-US]        | Promotion |
      | active             | true      |
      | total_quantity     | null      |
      | remaining_quantity | null      |
      | quantity_per_user  | null      |

  Scenario: Update a discount quantity limits
    When I create a "cart_level" discount "created_discount" with following properties:
      | name[en-US] | Promotion |
      | active      | true      |
    Then discount "created_discount" should have the following properties:
      | name[en-US]        | Promotion |
      | active             | true      |
      | total_quantity     | null      |
      | remaining_quantity | null      |
      | quantity_per_user  | null      |
    When I update discount "created_discount" with the following properties:
      | total_quantity    | 42 |
      | quantity_per_user | 51 |
    Then discount "created_discount" should have the following properties:
      | name[en-US]        | Promotion |
      | active             | true      |
      | total_quantity     | 42        |
      | remaining_quantity | 42        |
      | quantity_per_user  | 51        |
    # I can force and update no limits
    When I update discount "created_discount" with the following properties:
      | total_quantity    | null |
      | quantity_per_user | null |
    Then discount "created_discount" should have the following properties:
      | name[en-US]        | Promotion |
      | active             | true      |
      | total_quantity     | null      |
      | remaining_quantity | null      |
      | quantity_per_user  | null      |
