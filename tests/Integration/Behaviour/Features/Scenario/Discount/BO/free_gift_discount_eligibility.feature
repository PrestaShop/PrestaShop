# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags free-gift-discount-eligibility
@restore-all-tables-before-feature
@restore-languages-after-feature
@free-gift-discount-eligibility
Feature: Free gift discount eligibility
  PrestaShop validates gift product eligibility when creating or enabling a free gift discount
  As a BO user
  I must not be able to use an ineligible product as a free gift

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    And I enable feature flag "discount"

  Scenario: Creating a discount with a product not available for order succeeds
    Given there is a product in the catalog named "unavailable-product" with a price of 10.0 and 100 items in stock
    And product "unavailable-product" is not available for order
    When I create a "free_gift" discount "gift_unavailable" with following properties:
      | name[en-US]  | Gift unavailable    |
      | active       | true                |
      | valid_from   | 2025-01-01 00:00:00 |
      | valid_to     | 2025-12-31 00:00:00 |
      | gift_product | unavailable-product |
    Then discount "gift_unavailable" is enabled

  Scenario: Creating a discount with a product whose minimum quantity is greater than 1 fails
    Given there is a product in the catalog named "min-qty-product" with a price of 10.0 and 100 items in stock
    And the product "min-qty-product" minimal quantity is 2
    When I create a "free_gift" discount "gift_min_qty" with following properties:
      | name[en-US]  | Gift min qty        |
      | active       | true                |
      | valid_from   | 2025-01-01 00:00:00 |
      | valid_to     | 2025-12-31 00:00:00 |
      | gift_product | min-qty-product     |
    Then I should get an error that the gift product has a minimum quantity greater than 1

  Scenario: Creating a discount with an out-of-stock product (orders denied) succeeds
    Given there is a product in the catalog named "out-of-stock-product" with a price of 10.0 and 100 items in stock
    And product "out-of-stock-product" is out of stock
    And the product "out-of-stock-product" denies order if out of stock
    When I create a "free_gift" discount "gift_out_of_stock" with following properties:
      | name[en-US]  | Gift out of stock    |
      | active       | true                 |
      | valid_from   | 2025-01-01 00:00:00  |
      | valid_to     | 2025-12-31 00:00:00  |
      | gift_product | out-of-stock-product |
    Then discount "gift_out_of_stock" is enabled

  Scenario: Enabling a discount whose gift product became out of stock succeeds
    Given there is a product in the catalog named "bulk-gift-product" with a price of 10.0 and 100 items in stock
    When I create a "free_gift" discount "bulk_gift_discount" with following properties:
      | name[en-US]  | Bulk gift discount  |
      | active       | false               |
      | valid_from   | 2025-01-01 00:00:00 |
      | valid_to     | 2025-12-31 00:00:00 |
      | gift_product | bulk-gift-product   |
    Then discount "bulk_gift_discount" is disabled
    When product "bulk-gift-product" is out of stock
    And the product "bulk-gift-product" denies order if out of stock
    When I enable discount "bulk_gift_discount"
    Then discount "bulk_gift_discount" is enabled

  Scenario: Enabling a discount whose gift product became unavailable for order succeeds
    Given there is a product in the catalog named "bulk-unavailable-product" with a price of 10.0 and 100 items in stock
    When I create a "free_gift" discount "bulk_gift_unavailable" with following properties:
      | name[en-US]  | Bulk gift unavailable    |
      | active       | false                    |
      | valid_from   | 2025-01-01 00:00:00      |
      | valid_to     | 2025-12-31 00:00:00      |
      | gift_product | bulk-unavailable-product |
    Then discount "bulk_gift_unavailable" is disabled
    When product "bulk-unavailable-product" is not available for order
    When I enable discount "bulk_gift_unavailable"
    Then discount "bulk_gift_unavailable" is enabled

  Scenario: Single enabling a discount whose gift product became out of stock succeeds
    Given there is a product in the catalog named "single-gift-product" with a price of 10.0 and 100 items in stock
    When I create a "free_gift" discount "single_gift_discount" with following properties:
      | name[en-US]  | Single gift discount  |
      | active       | false                 |
      | valid_from   | 2025-01-01 00:00:00   |
      | valid_to     | 2025-12-31 00:00:00   |
      | gift_product | single-gift-product   |
    Then discount "single_gift_discount" is disabled
    When product "single-gift-product" is out of stock
    And the product "single-gift-product" denies order if out of stock
    When I enable discount "single_gift_discount"
    Then discount "single_gift_discount" is enabled

  Scenario: Updating a free gift discount with a product requiring customization fails
    Given there is a product in the catalog named "update-valid-product" with a price of 10.0 and 100 items in stock
    And there is a product in the catalog named "update-custo-product" with a price of 10.0 and 100 items in stock
    And product "update-custo-product" requires customization
    When I create a "free_gift" discount "update_gift_custo" with following properties:
      | name[en-US]  | Update gift custo    |
      | active       | true                 |
      | valid_from   | 2025-01-01 00:00:00  |
      | valid_to     | 2025-12-31 00:00:00  |
      | gift_product | update-valid-product |
    Then discount "update_gift_custo" is enabled
    When I update discount "update_gift_custo" with the following properties:
      | gift_product | update-custo-product |
    Then I should get error that discount field gift_product is invalid

  Scenario: Updating a free gift discount with a product whose minimum quantity is greater than 1 fails
    Given there is a product in the catalog named "update-valid-product2" with a price of 10.0 and 100 items in stock
    And there is a product in the catalog named "update-min-qty-product" with a price of 10.0 and 100 items in stock
    And the product "update-min-qty-product" minimal quantity is 2
    When I create a "free_gift" discount "update_gift_min_qty" with following properties:
      | name[en-US]  | Update gift min qty   |
      | active       | true                  |
      | valid_from   | 2025-01-01 00:00:00   |
      | valid_to     | 2025-12-31 00:00:00   |
      | gift_product | update-valid-product2 |
    Then discount "update_gift_min_qty" is enabled
    When I update discount "update_gift_min_qty" with the following properties:
      | gift_product | update-min-qty-product |
    Then I should get an error that the gift product has a minimum quantity greater than 1

  Scenario: Updating product minimum quantity to more than 1 auto-disables active free gift discounts
    Given there is a product in the catalog named "auto-disable-min-qty" with a price of 10.0 and 100 items in stock
    When I create a "free_gift" discount "auto_disable_min_qty_discount" with following properties:
      | name[en-US]  | Auto disable min qty |
      | active       | true                 |
      | valid_from   | 2025-01-01 00:00:00  |
      | valid_to     | 2025-12-31 00:00:00  |
      | gift_product | auto-disable-min-qty |
    Then discount "auto_disable_min_qty_discount" is enabled
    When I update product "auto-disable-min-qty" with following values:
      | minimal_quantity | 2 |
    Then discount "auto_disable_min_qty_discount" is disabled

  Scenario: Making a product require customization auto-disables active free gift discounts
    Given there is a product in the catalog named "auto-disable-custo" with a price of 10.0 and 100 items in stock
    When I create a "free_gift" discount "auto_disable_custo_discount" with following properties:
      | name[en-US]  | Auto disable custo  |
      | active       | true                |
      | valid_from   | 2025-01-01 00:00:00 |
      | valid_to     | 2025-12-31 00:00:00 |
      | gift_product | auto-disable-custo  |
    Then discount "auto_disable_custo_discount" is enabled
    When product "auto-disable-custo" requires customization
    Then discount "auto_disable_custo_discount" is disabled

  Scenario: Deleting a gift product auto-disables active free gift discounts
    Given there is a product in the catalog named "auto-disable-deleted" with a price of 10.0 and 100 items in stock
    When I create a "free_gift" discount "auto_disable_delete_discount" with following properties:
      | name[en-US]  | Auto disable deleted  |
      | active       | true                  |
      | valid_from   | 2025-01-01 00:00:00   |
      | valid_to     | 2025-12-31 00:00:00   |
      | gift_product | auto-disable-deleted  |
    Then discount "auto_disable_delete_discount" is enabled
    When I delete product "auto-disable-deleted"
    Then discount "auto_disable_delete_discount" is disabled
