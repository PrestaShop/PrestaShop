# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-management
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-multi-shop
@update-multi-shop-management
Feature: Copy product from shop to shop.
  As a BO user I want to be able to copy product from shop to shop.

  Background:
    Given shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And language "french" with locale "fr-FR" exists

  Scenario: Add products in specific shop
    Given I add product "createdProduct" to shop "shop2" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product createdProduct is associated to shop shop2
    And default shop for product createdProduct is shop2
    And product createdProduct is not associated to shop shop1
    And product createdProduct is not associated to shop shop3
    And product createdProduct is not associated to shop shop4
    # Assert stock has correctly been created for the appropriate shop
    Then product "createdProduct" should have following stock information for shops "shop2":
      | pack_stock_type     | default |
      | out_of_stock_type   | default |
      | quantity            | 0       |
      | minimal_quantity    | 1       |
      | location            |         |
      | low_stock_threshold | 0       |
      | low_stock_alert     | false   |
      | available_date      |         |

  Scenario: I copy product to another shop that was not associated, prices are copied
    # By default the product is created for default shop
    Given I add product "productWithPrices" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product productWithPrices is associated to shop shop1
    And default shop for product productWithPrices is shop1
    When I update product "productWithPrices" prices with following information:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
    Then product productWithPrices should have following prices information for shops "shop1":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product productWithPrices is not associated to shop shop2
    And product productWithPrices is not associated to shop shop3
    And product productWithPrices is not associated to shop shop4
    # Copy values to another shop which was not associated yet
    When I copy product productWithPrices from shop shop1 to shop shop2
    Then product productWithPrices is associated to shop shop2
    And product productWithPrices is associated to shop shop1
    And default shop for product productWithPrices is shop1
    And product productWithPrices should have following prices information for shops "shop1,shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product productWithPrices is not associated to shop shop3
    And product productWithPrices is not associated to shop shop4
    # Now modify and copy the values but this time the shop is already associated so it is an update
    When I update product "productWithPrices" prices with following information:
      | price              | 200.99            |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
    Then product productWithPrices should have following prices information for shops "shop1":
      | price              | 200.99            |
      | price_tax_included | 214.25534         |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
      | unit_price_ratio   | 10.0495           |
    But product productWithPrices should have following prices information for shops "shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    # Copy values to a shop which is already associated
    When I copy product productWithPrices from shop shop1 to shop shop2
    Then product productWithPrices is associated to shop shop2
    And product productWithPrices should have following prices information for shops "shop1,shop2":
      | price              | 200.99            |
      | price_tax_included | 214.25534         |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
      | unit_price_ratio   | 10.0495           |
    And product productWithPrices is not associated to shop shop3
    And product productWithPrices is not associated to shop shop4

  Scenario: I copy product to another shop that was not associated, basic information are copied
    # By default the product is created for default shop
    Given I add product "productWithBasic" with following information:
      | name[en-US] | funny mug |
      | type        | standard  |
    Then product productWithBasic is associated to shop shop1
    And default shop for product productWithBasic is shop1
    When I update product "productWithBasic" basic information with following values:
      | name[en-US]              | photo of funny mug |
      | description[en-US]       | nice mug           |
      | description_short[en-US] | Just a nice mug    |
    Then product "productWithBasic" localized "name" should be:
      | locale     | value              |
      | en-US      | photo of funny mug |
    And product "productWithBasic" localized "description" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "productWithBasic" localized "description_short" should be:
      | locale | value           |
      | en-US  | Just a nice mug |
    And product productWithBasic is not associated to shop shop2
    And product productWithBasic is not associated to shop shop3
    And product productWithBasic is not associated to shop shop4
    # Copy values to another shop which was not associated yet
    When I copy product productWithBasic from shop shop1 to shop shop2
    Then product productWithBasic is associated to shop shop2
    And product productWithBasic is associated to shop shop1
    And default shop for product productWithBasic is shop1
    Then product "productWithBasic" localized "name" for shops "shop1,shop2" should be:
      | locale     | value              |
      | en-US      | photo of funny mug |
    And product "productWithBasic" localized "description" for shops "shop1,shop2" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "productWithBasic" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value           |
      | en-US  | Just a nice mug |
    And product productWithBasic is not associated to shop shop3
    And product productWithBasic is not associated to shop shop4
    # Now modify and copy the values but this time the shop is already associated so it is an update
    When I update product "productWithBasic" basic information with following values:
      | name[en-US]              | photo of super mug |
      | description[en-US]       | super mug          |
      | description_short[en-US] | Just a super mug   |
    Then product "productWithBasic" localized "name" for shops "shop1" should be:
      | locale     | value              |
      | en-US      | photo of super mug |
    And product "productWithBasic" localized "description" for shops "shop1" should be:
      | locale | value     |
      | en-US  | super mug |
    And product "productWithBasic" localized "description_short" for shops "shop1" should be:
      | locale | value            |
      | en-US  | Just a super mug |
    But product "productWithBasic" localized "name" for shops "shop2" should be:
      | locale     | value              |
      | en-US      | photo of funny mug |
    And product "productWithBasic" localized "description" for shops "shop2" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "productWithBasic" localized "description_short" for shops "shop2" should be:
      | locale | value           |
      | en-US  | Just a nice mug |
    # Copy values to a shop which is already associated
    When I copy product productWithBasic from shop shop1 to shop shop2
    Then product productWithBasic is associated to shop shop2
    And product "productWithBasic" localized "name" for shops "shop1,shop2" should be:
      | locale     | value              |
      | en-US      | photo of super mug |
    And product "productWithBasic" localized "description" for shops "shop1,shop2" should be:
      | locale | value     |
      | en-US  | super mug |
    And product "productWithBasic" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value            |
      | en-US  | Just a super mug |
    And product productWithBasic is not associated to shop shop3
    And product productWithBasic is not associated to shop shop4

  Scenario: I copy product to another shop that was not associated, stock data are copied
    # By default the product is created for default shop
    Given I add product "productWithStock" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product productWithStock is associated to shop shop1
    And default shop for product productWithStock is shop1
    # First modify data for default shop
    When I update product "productWithStock" stock with following information:
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
    Then product "productWithStock" should have following stock information for shops "shop1":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productWithStock" localized "available_now_labels" for shops "shop1" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productWithStock" localized "available_later_labels" for shops "shop1" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product productWithStock is not associated to shop shop2
    And product productWithStock is not associated to shop shop3
    And product productWithStock is not associated to shop shop4
    # Copy values to another shop which was not associated yet
    When I copy product productWithStock from shop shop1 to shop shop2
    Then product productWithStock is associated to shop shop2
    And product productWithStock is associated to shop shop1
    And default shop for product productWithStock is shop1
    Then product "productWithStock" should have following stock information for shops "shop1,shop2":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productWithStock" localized "available_now_labels" for shops "shop1,shop2" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productWithStock" localized "available_later_labels" for shops "shop1,shop2" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product productWithStock is not associated to shop shop3
    And product productWithStock is not associated to shop shop4
    # Now modify and copy the values but this time the shop is already associated so it is an update
    When I update product "productWithStock" stock for shop shop1 with following information:
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
    # First only one shop is updated
    Then product "productWithStock" should have following stock information for shops "shop1":
      | pack_stock_type     | products_only |
      | out_of_stock_type   | not_available |
      | quantity            | 111           |
      | minimal_quantity    | 24            |
      | location            | upa           |
      | low_stock_threshold | 51            |
      | low_stock_alert     | false         |
      | available_date      | 1969-09-16    |
    And product "productWithStock" localized "available_now_labels" for shops "shop1" should be:
      | locale | value    |
      | en-US  | hurry up |
      | fr-FR  |          |
    And product "productWithStock" localized "available_later_labels" for shops "shop1" should be:
      | locale | value       |
      | en-US  | too slow... |
      | fr-FR  |             |
    But product "productWithStock" should have following stock information for shops "shop2":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productWithStock" localized "available_now_labels" for shops "shop2" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productWithStock" localized "available_later_labels" for shops "shop2" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product productWithStock is not associated to shop shop3
    And product productWithStock is not associated to shop shop4
    # Now copy new values to the other shop
    When I copy product productWithStock from shop shop1 to shop shop2
    Then product "productWithStock" should have following stock information for shops "shop1,shop2":
      | pack_stock_type     | products_only |
      | out_of_stock_type   | not_available |
      | quantity            | 111           |
      | minimal_quantity    | 24            |
      | location            | upa           |
      | low_stock_threshold | 51            |
      | low_stock_alert     | false         |
      | available_date      | 1969-09-16    |
    And product "productWithStock" localized "available_now_labels" for shops "shop1,shop2" should be:
      | locale | value    |
      | en-US  | hurry up |
      | fr-FR  |          |
    And product "productWithStock" localized "available_later_labels" for shops "shop1,shop2" should be:
      | locale | value       |
      | en-US  | too slow... |
      | fr-FR  |             |
