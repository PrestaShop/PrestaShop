# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-prices
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-multi-shop
@update-multi-shop-prices
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
    Given I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    When I update product "product1" prices with following information:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
    And I copy product product1 from shop shop1 to shop shop2
    Then product product1 should have following prices information for shops "shop1,shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product prices for a specific shop
    When I update product "product1" prices for shop shop2 with following information:
      | price              | 200.99            |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
    Then product product1 should have following prices information for shops "shop2":
      | price              | 200.99            |
      | price_tax_included | 214.25534         |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
      | unit_price_ratio   | 10.0495           |
    And product product1 should have following prices information for shops "shop1":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product prices for all associated shop
    When I update product "product1" prices for all shops with following information:
      | price              | 200.99            |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
    Then product product1 should have following prices information for shops "shop1,shop2":
      | price              | 200.99            |
      | price_tax_included | 214.25534         |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
      | unit_price_ratio   | 10.0495           |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update some fields for single shop and right after for all shops
    Given product product1 should have following prices information for shops "shop1,shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4
    # Important to test unity because it was always overridden in all shops mode (because of a bug in
    # Product::getFieldsShops that did not handle partial update correctly)
    When I update product "product1" prices for shop shop2 with following information:
      | unit_price         | 20              |
      | unity              | bag of twenty   |
    Then product product1 should have following prices information for shops "shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 20              |
      | unity              | bag of twenty   |
      | unit_price_ratio   | 5.0495          |
    # Important to test the data after a command for all shops because the partial update did not work correctly for
    # shop fields and all fields were overridden (because of a bug in ObjectModel::formatFields)
    And I update product "product1" prices for all shops with following information:
      | wholesale_price    | 90              |
    Then product product1 should have following prices information for shops "shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 90              |
      | unit_price         | 20              |
      | unity              | bag of twenty   |
      | unit_price_ratio   | 5.0495          |
    And product product1 should have following prices information for shops "shop1":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 90              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update price and unit price for all shops and right after for single shop
    Given product product1 should have following prices information for shops "shop1,shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4
    # First change unit price for one shop
    When I update product "product1" prices for shop shop2 with following information:
      | unit_price         | 20              |
      | unity              | bag of twenty   |
    Then product product1 should have following prices information for shops "shop1":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    Then product product1 should have following prices information for shops "shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 20              |
      | unity              | bag of twenty   |
      | unit_price_ratio   | 5.0495          |
    # Now update price for all, it should update unit price accordingly for each shop
    When I update product "product1" prices for all shops with following information:
      | price | 90 |
    Then product product1 should have following prices information for shops "shop1":
      | price              | 90              |
      | price_tax_included | 93.60           |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 9.00            |
    And product product1 should have following prices information for shops "shop2":
      | price              | 90              |
      | price_tax_included | 93.60           |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 20              |
      | unity              | bag of twenty   |
      | unit_price_ratio   | 4.50            |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4
    # Now update prices for first shop (default shop) only this one is affected
    When I update product "product1" prices for shop shop1 with following information:
      | price              | 108             |
      | unit_price         | 30              |
      | unity              | bag of thirty   |
    Then product product1 should have following prices information for shops "shop1":
      | price              | 108             |
      | price_tax_included | 112.32          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 30              |
      | unity              | bag of thirty   |
      | unit_price_ratio   | 3.60            |
    And product product1 should have following prices information for shops "shop2":
      | price              | 90              |
      | price_tax_included | 93.60           |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 20              |
      | unity              | bag of twenty   |
      | unit_price_ratio   | 4.50            |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4
    # Now update unit price for second shop only this one is affected
    When I update product "product1" prices for shop shop2 with following information:
      | price              | 60              |
    Then product product1 should have following prices information for shops "shop1":
      | price              | 108             |
      | price_tax_included | 112.32          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 30              |
      | unity              | bag of thirty   |
      | unit_price_ratio   | 3.60            |
    And product product1 should have following prices information for shops "shop2":
      | price              | 60              |
      | price_tax_included | 62.40           |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 20              |
      | unity              | bag of twenty   |
      | unit_price_ratio   | 3.00            |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4
    # Finally update unit price for all, each shop is updated appropriately
    When I update product "product1" prices for all shops with following information:
      | unit_price         | 50              |
    Then product product1 should have following prices information for shops "shop1":
      | price              | 108             |
      | price_tax_included | 112.32          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 50              |
      | unity              | bag of thirty   |
      | unit_price_ratio   | 2.16            |
    And product product1 should have following prices information for shops "shop2":
      | price              | 60              |
      | price_tax_included | 62.40           |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 50              |
      | unity              | bag of twenty   |
      | unit_price_ratio   | 1.20            |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4
