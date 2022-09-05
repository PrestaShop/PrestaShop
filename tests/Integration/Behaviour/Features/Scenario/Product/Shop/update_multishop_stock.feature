# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-stock
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@clear-cache-after-feature
@product-multi-shop
@update-multi-shop-stock
Feature: Update product price fields from Back Office (BO) for multiple shops.
  As a BO user I want to be able to update product fields associated with price for multiple shops.

  Background:
    Given shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And shop configuration for "PS_ADVANCED_STOCK_MANAGEMENT" is set to 0
    And language "french" with locale "fr-FR" exists
    Given I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product "product1" should have no stock movements
    When I update product "product1" stock with following information:
      | pack_stock_type               | pack_only    |
      | out_of_stock_type             | available    |
      | delta_quantity                | 42           |
      | minimal_quantity              | 12           |
      | location                      | dtc          |
      | low_stock_threshold           | 42           |
      | low_stock_alert               | true         |
      | available_now_labels[en-US]   | get it now   |
      | available_later_labels[en-US] | too late bro |
      | available_date                | 1969-07-16   |
    And I copy product product1 from shop shop1 to shop shop2
    Then product "product1" should have following stock information for shops "shop1,shop2":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "product1" localized "available_now_labels" for shops "shop1,shop2" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "product1" localized "available_later_labels" for shops "shop1,shop2" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product "product1" last employees stock movements should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 42             |
    And product "product1" last stock movement increased by 42
    And product "product1" should have no stock movements for shop "shop2"
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product stock for specific shop (not default one)
    When I update product "product1" stock for shop shop2 with following information:
      | pack_stock_type               | products_only |
      | out_of_stock_type             | not_available |
      | delta_quantity                | 69            |
      | minimal_quantity              | 24            |
      | location                      | upa           |
      | low_stock_threshold           | 51            |
      | low_stock_alert               | false         |
      | available_now_labels[en-US]   | hurry up      |
      | available_later_labels[en-US] | too slow...   |
      | available_date                | 1969-09-16    |
    Then product "product1" should have following stock information for shops "shop2":
      | pack_stock_type     | products_only |
      | out_of_stock_type   | not_available |
      | quantity            | 111           |
      | minimal_quantity    | 24            |
      | location            | upa           |
      | low_stock_threshold | 51            |
      | low_stock_alert     | false         |
      | available_date      | 1969-09-16    |
    And product "product1" localized "available_now_labels" for shops "shop2" should be:
      | locale | value    |
      | en-US  | hurry up |
      | fr-FR  |          |
    And product "product1" localized "available_later_labels" for shops "shop2" should be:
      | locale | value       |
      | en-US  | too slow... |
      | fr-FR  |             |
    And product "product1" last employees stock movements for shop "shop2" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 69             |
    And product "product1" last stock movement for shop "shop2" increased by 69
    But product "product1" should have following stock information for shops "shop1":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "product1" localized "available_now_labels" for shops "shop1" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "product1" localized "available_later_labels" for shops "shop1" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product "product1" last employees stock movements for shop "shop1" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 42             |
    And product "product1" last stock movement for shop "shop1" increased by 42
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product stock for all associated shop (quantity not handled)
    When I update product "product1" stock for all shops with following information:
      | pack_stock_type               | products_only |
      | out_of_stock_type             | not_available |
      | minimal_quantity              | 24            |
      | location                      | upa           |
      | low_stock_threshold           | 51            |
      | low_stock_alert               | false         |
      | available_now_labels[en-US]   | hurry up      |
      | available_later_labels[en-US] | too slow...   |
      | available_date                | 1969-09-16    |
    Then product "product1" should have following stock information for shops "shop1,shop2":
      | pack_stock_type               | products_only |
      | out_of_stock_type             | not_available |
      | minimal_quantity              | 24            |
      | location                      | upa           |
      | low_stock_threshold           | 51            |
      | low_stock_alert               | false         |
      | available_date                | 1969-09-16    |
    And product "product1" localized "available_now_labels" for shops "shop1,shop2" should be:
      | locale | value    |
      | en-US  | hurry up |
      | fr-FR  |          |
    And product "product1" localized "available_later_labels" for shops "shop1,shop2" should be:
      | locale | value       |
      | en-US  | too slow... |
      | fr-FR  |             |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update some fields for single shop and after for all shops (quantity not handled)
    When I update product "product1" stock for shop shop2 with following information:
      | pack_stock_type               | products_only |
      | out_of_stock_type             | not_available |
      | minimal_quantity              | 24            |
      | location                      | upa           |
      | low_stock_threshold           | 51            |
      | low_stock_alert               | false         |
      | available_now_labels[en-US]   | hurry up      |
      | available_later_labels[en-US] | too slow...   |
      | available_date                | 1969-09-16    |
    When I update product "product1" stock for all shops with following information:
      | pack_stock_type               | default  |
      | out_of_stock_type             | default  |
      | location                      | surprise |
      | minimal_quantity              | 51       |
      | available_now_labels[en-US]   | it is on |
    Then product "product1" should have following stock information for shops "shop2":
      | pack_stock_type     | default    |
      | out_of_stock_type   | default    |
      | quantity            | 42         |
      | minimal_quantity    | 51         |
      | location            | surprise   |
      | low_stock_threshold | 51         |
      | low_stock_alert     | false      |
      | available_date      | 1969-09-16 |
    And product "product1" localized "available_later_labels" for shops "shop2" should be:
      | locale | value       |
      | en-US  | too slow... |
      | fr-FR  |             |
    But product "product1" should have following stock information for shops "shop1":
      | pack_stock_type     | default    |
      | out_of_stock_type   | default    |
      | quantity            | 42         |
      | minimal_quantity    | 51         |
      | location            | surprise   |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "product1" localized "available_later_labels" for shops "shop1" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product "product1" localized "available_now_labels" for shops "shop1,shop2" should be:
      | locale | value    |
      | en-US  | it is on |
      | fr-FR  |          |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update some fields for all shops and after for single shop (quantity not handled)
    When I update product "product1" stock for all shops with following information:
      | pack_stock_type               | default  |
      | out_of_stock_type             | default  |
      | location                      | surprise |
      | minimal_quantity              | 51       |
      | available_now_labels[en-US]   | it is on |
    When I update product "product1" stock for shop shop2 with following information:
      | pack_stock_type               | products_only |
      | out_of_stock_type             | not_available |
      | low_stock_alert               | false         |
      | available_later_labels[en-US] | too slow...   |
    Then product "product1" should have following stock information for shops "shop2":
      | pack_stock_type     | products_only |
      | out_of_stock_type   | not_available |
      | quantity            | 42            |
      | minimal_quantity    | 51            |
      | location            | surprise      |
      | low_stock_threshold | 42            |
      | low_stock_alert     | false         |
      | available_date      | 1969-07-16    |
    And product "product1" localized "available_later_labels" for shops "shop2" should be:
      | locale | value       |
      | en-US  | too slow... |
      | fr-FR  |             |
    But product "product1" should have following stock information for shops "shop1":
      | pack_stock_type     | default    |
      | out_of_stock_type   | default    |
      | quantity            | 42         |
      | minimal_quantity    | 51         |
      | location            | surprise   |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "product1" localized "available_later_labels" for shops "shop1" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product "product1" localized "available_now_labels" for shops "shop1,shop2" should be:
      | locale | value    |
      | en-US  | it is on |
      | fr-FR  |          |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I can update stock quantity independently for each shop
    When I update product "product1" stock for shop shop2 with following information:
      | delta_quantity | 69 |
    And I update product "product1" stock for shop shop1 with following information:
      | delta_quantity | 51 |
    Then product "product1" should have following stock information for shops "shop2":
      | quantity | 111 |
    And product "product1" last employees stock movements for shop "shop2" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 69             |
    And product "product1" last stock movement for shop "shop2" increased by 69
    And product "product1" should have following stock information for shops "shop1":
      | quantity | 93 |
    And product "product1" last employees stock movements for shop "shop1" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 51             |
      | Puff       | Daddy     | 42             |
    And product "product1" last stock movement for shop "shop1" increased by 51

  Scenario: I can update stock quantity independently for all shops at once
    When I update product "product1" stock for all shops with following information:
      | delta_quantity | 69 |
    Then product "product1" should have following stock information for shops "shop1,shop2":
      | quantity | 111 |
    And product "product1" last employees stock movements for shop "shop2" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 69             |
    And product "product1" last stock movement for shop "shop2" increased by 69
    And product "product1" last employees stock movements for shop "shop1" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 69             |
      | Puff       | Daddy     | 42             |
    And product "product1" last stock movement for shop "shop1" increased by 69

  Scenario: I can update stock quantity for single and/or al shops but since it's a delta modification their values are not necessarily synced
    When I update product "product1" stock for shop shop2 with following information:
      | delta_quantity | 69 |
    And I update product "product1" stock for shop shop1 with following information:
      | delta_quantity | 51 |
    When I update product "product1" stock for all shops with following information:
      | delta_quantity | -10 |
    Then product "product1" should have following stock information for shops "shop2":
      | quantity | 101 |
    And product "product1" last employees stock movements for shop "shop2" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | -10            |
      | Puff       | Daddy     | 69             |
    And product "product1" last stock movement for shop "shop2" decreased by 10
    And product "product1" should have following stock information for shops "shop1":
      | quantity | 83 |
    And product "product1" last employees stock movements for shop "shop1" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | -10            |
      | Puff       | Daddy     | 51             |
      | Puff       | Daddy     | 42             |
    And product "product1" last stock movement for shop "shop1" decreased by 10

  Scenario: I can update stock quantity for single and/or al shops but since it's a delta modification their values are not necessarily synced
    When I update product "product1" stock for all shops with following information:
      | delta_quantity | -10 |
    And I update product "product1" stock for shop shop2 with following information:
      | delta_quantity | 69 |
    And I update product "product1" stock for shop shop1 with following information:
      | delta_quantity | 51 |
    Then product "product1" should have following stock information for shops "shop2":
      | quantity | 101 |
    And product "product1" last employees stock movements for shop "shop2" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 69             |
      | Puff       | Daddy     | -10            |
    And product "product1" last stock movement for shop "shop2" increased by 69
    And product "product1" should have following stock information for shops "shop1":
      | quantity | 83 |
    And product "product1" last employees stock movements for shop "shop1" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 51             |
      | Puff       | Daddy     | -10            |
      | Puff       | Daddy     | 42             |
    And product "product1" last stock movement for shop "shop1" increased by 51

  Scenario: I update product type to combinations (stock is reset to zero for ALL associated shops)
    When I add product "productCombinations" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "productCombinations" should be disabled
    And product "productCombinations" type should be standard
    And product "productCombinations" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    When I update product "productCombinations" stock with following information:
      | delta_quantity | 42 |
    And product "productCombinations" should have following stock information:
      | quantity | 42 |
    And I copy product productCombinations from shop shop1 to shop shop2
    Then product "productCombinations" should have following stock information for shops "shop1,shop2":
      | quantity | 42 |
    When I update product "productCombinations" stock for shop shop2 with following information:
      | delta_quantity | 69 |
    And I update product "productCombinations" stock for shop shop1 with following information:
      | delta_quantity | 51 |
    Then product "productCombinations" should have following stock information for shops "shop1":
      | quantity | 93 |
    And product "productCombinations" last employees stock movements for shop "shop1" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 51             |
      | Puff       | Daddy     | 42             |
    And product "productCombinations" last stock movement for shop "shop1" increased by 51
    And product "productCombinations" should have following stock information for shops "shop2":
      | quantity | 111 |
    And product "productCombinations" last employees stock movements for shop "shop2" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 69             |
    And product "productCombinations" last stock movement for shop "shop2" increased by 69
    When I update product "productCombinations" type to combinations
    Then product "productCombinations" type should be combinations
    Then product "productCombinations" should have following stock information for shops "shop1,shop2":
      | quantity | 0 |
    And product "productCombinations" last employees stock movements for shop "shop1" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | -93            |
      | Puff       | Daddy     | 51             |
      | Puff       | Daddy     | 42             |
    And product "productCombinations" last stock movement for shop "shop1" decreased by 93
    And product "productCombinations" last employees stock movements for shop "shop2" should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | -111           |
      | Puff       | Daddy     | 69             |
    And product "productCombinations" last stock movement for shop "shop2" decreased by 111
