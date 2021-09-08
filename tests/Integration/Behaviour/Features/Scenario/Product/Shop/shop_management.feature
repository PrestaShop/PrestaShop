# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-management
@reset-database-before-feature
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

  Scenario: I copy product to another shop that was not associated
    # By default the product is created for default shop
    Given I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product product1 is associated to shop shop1
    And default shop for product product1 is shop1
    When I update product "product1" prices with following information:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
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
    And product product1 is not associated to shop shop2
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4
    # Copy values to another shop which was not associated yet
    When I copy product product1 from shop shop1 to shop shop2
    Then product product1 is associated to shop shop2
    And product product1 is associated to shop shop1
    And default shop for product product1 is shop1
    And product product1 should have following prices information for shops "shop1,shop2":
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
    # Now modify and copy the values but this time the shop is already associated so it is an update
    When I update product "product1" prices with following information:
      | price              | 200.99            |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
    Then product product1 should have following prices information for shops "shop1":
      | price              | 200.99            |
      | price_tax_included | 214.25534         |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
      | unit_price_ratio   | 10.0495           |
    Given product product1 should have following prices information for shops "shop2":
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
    When I copy product product1 from shop shop1 to shop shop2
    Then product product1 is associated to shop shop2
    And product product1 should have following prices information for shops "shop1,shop2":
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

  Scenario: Add products in specific shop
    Given I add product "product2" to shop "shop2" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product product2 is associated to shop shop2
    And default shop for product product2 is shop2
    And product product2 is not associated to shop shop1
    And product product2 is not associated to shop shop3
    And product product2 is not associated to shop shop4
