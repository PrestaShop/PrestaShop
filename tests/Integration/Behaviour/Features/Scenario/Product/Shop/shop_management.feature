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
    And I add a shop group "shopGroup2" with name "test_second_shop_group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded

  Scenario: Add products in specific shop
    Given I add product "product1" to shop "shop2" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product product1 is associated to shop shop2
    And default shop for product product1 is shop2
    And product product1 is not associated to shop shop1
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I copy product to another shop that was not associated, prices are copied
    # By default the product is created for default shop
    Given I add product "product2" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product product2 is associated to shop shop1
    And default shop for product product2 is shop1
    When I update product "product2" prices with following information:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
    Then product product2 should have following prices information for shops "shop1":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product product2 is not associated to shop shop2
    And product product2 is not associated to shop shop3
    And product product2 is not associated to shop shop4
    # Copy values to another shop which was not associated yet
    When I copy product product2 from shop shop1 to shop shop2
    Then product product2 is associated to shop shop2
    And product product2 is associated to shop shop1
    And default shop for product product2 is shop1
    And product product2 should have following prices information for shops "shop1,shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product product2 is not associated to shop shop3
    And product product2 is not associated to shop shop4
    # Now modify and copy the values but this time the shop is already associated so it is an update
    When I update product "product2" prices with following information:
      | price              | 200.99            |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
    Then product product2 should have following prices information for shops "shop1":
      | price              | 200.99            |
      | price_tax_included | 214.25534         |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
      | unit_price_ratio   | 10.0495           |
    But product product2 should have following prices information for shops "shop2":
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
    When I copy product product2 from shop shop1 to shop shop2
    Then product product2 is associated to shop shop2
    And product product2 should have following prices information for shops "shop1,shop2":
      | price              | 200.99            |
      | price_tax_included | 214.25534         |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
      | unit_price_ratio   | 10.0495           |
    And product product2 is not associated to shop shop3
    And product product2 is not associated to shop shop4

  Scenario: I copy product to another shop that was not associated, basic information are copied
    # By default the product is created for default shop
    Given I add product "product3" with following information:
      | name[en-US] | funny mug |
      | type        | standard  |
    Then product product3 is associated to shop shop1
    And default shop for product product3 is shop1
    When I update product "product3" basic information with following values:
      | name[en-US]              | photo of funny mug |
      | description[en-US]       | nice mug           |
      | description_short[en-US] | Just a nice mug    |
    Then product "product3" localized "name" should be:
      | locale     | value              |
      | en-US      | photo of funny mug |
    And product "product3" localized "description" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "product3" localized "description_short" should be:
      | locale | value           |
      | en-US  | Just a nice mug |
    And product product3 is not associated to shop shop2
    And product product3 is not associated to shop shop3
    And product product3 is not associated to shop shop4
    # Copy values to another shop which was not associated yet
    When I copy product product3 from shop shop1 to shop shop2
    Then product product3 is associated to shop shop2
    And product product3 is associated to shop shop1
    And default shop for product product3 is shop1
    Then product "product3" localized "name" for shops "shop1,shop2" should be:
      | locale     | value              |
      | en-US      | photo of funny mug |
    And product "product3" localized "description" for shops "shop1,shop2" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "product3" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value           |
      | en-US  | Just a nice mug |
    And product product3 is not associated to shop shop3
    And product product3 is not associated to shop shop4
    # Now modify and copy the values but this time the shop is already associated so it is an update
    When I update product "product3" basic information with following values:
      | name[en-US]              | photo of super mug |
      | description[en-US]       | super mug          |
      | description_short[en-US] | Just a super mug   |
    Then product "product3" localized "name" for shops "shop1" should be:
      | locale     | value              |
      | en-US      | photo of super mug |
    And product "product3" localized "description" for shops "shop1" should be:
      | locale | value     |
      | en-US  | super mug |
    And product "product3" localized "description_short" for shops "shop1" should be:
      | locale | value            |
      | en-US  | Just a super mug |
    But product "product3" localized "name" for shops "shop2" should be:
      | locale     | value              |
      | en-US      | photo of funny mug |
    And product "product3" localized "description" for shops "shop2" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "product3" localized "description_short" for shops "shop2" should be:
      | locale | value           |
      | en-US  | Just a nice mug |
    # Copy values to a shop which is already associated
    When I copy product product3 from shop shop1 to shop shop2
    Then product product3 is associated to shop shop2
    And product "product3" localized "name" for shops "shop1,shop2" should be:
      | locale     | value              |
      | en-US      | photo of super mug |
    And product "product3" localized "description" for shops "shop1,shop2" should be:
      | locale | value     |
      | en-US  | super mug |
    And product "product3" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value            |
      | en-US  | Just a super mug |
    And product product3 is not associated to shop shop3
    And product product3 is not associated to shop shop4
