# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-status
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@clear-cache-after-feature
@product-multi-shop
@update-multi-shop-status
Feature: Update product status from Back Office (BO) for multiple shops.
  As a BO user I want to be able to update product status for multiple shops.

  Background:
    Given shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And language "french" with locale "fr-FR" exists
    Given I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And I copy product product1 from shop shop1 to shop shop2
    And product "product1" should be disabled for shops "shop1,shop2"
    And product "product1" should not be indexed for shops "shop1,shop2"
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product status in single shop
    When I enable product "product1" for shop "shop1"
    Then product "product1" should be enabled for shops "shop1"
    But product "product1" should be disabled for shops "shop2"
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product status in all shops
    When I enable product "product1" for all shops
    Then product "product1" should be enabled for shops "shop1,shop2"
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I expect product indexation to change when updating product status for each shop separately
    Given product "product1" should have following options for shops "shop1,shop2":
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    And product "product1" should be disabled for shops "shop1,shop2"
    And product "product1" should not be indexed for shops "shop1,shop2"
    When I enable product "product1" for shop "shop1"
    Then product "product1" should be enabled for shops "shop1"
    And product "product1" should be indexed for shops "shop1"
    But product "product1" should be disabled for shops "shop2"
    And product "product1" should not be indexed for shops "shop2"
    When I enable product "product1" for shop "shop2"
    Then product "product1" should be enabled for shops "shop1,shop2"
    And product "product1" should be indexed for shops "shop1,shop2"

  Scenario: I expect product indexation to change when updating product status for all shops
    Given product "product1" should have following options for shops "shop1,shop2":
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    And product "product1" should be disabled for shops "shop1,shop2"
    And product "product1" should not be indexed for shops "shop1,shop2"
#   It is important that we first enable product for shop1 (which is the default shop) to make sure
#   that default shop product differs from others, because in multi-shop context only the default shop may be loaded, so
#   some if statements might rely on default shop values, leaving other shops unhandled
#   (e.g. if statement in UpdateProductStatusHandler which decides if indexation is needed for product based on it's fields)
    When I enable product "product1" for shop "shop1"
    Then product "product1" should be enabled for shops "shop1"
    And product "product1" should be indexed for shops "shop1"
    But product "product1" should be disabled for shops "shop2"
    And product "product1" should not be indexed for shops "shop2"
    When I enable product "product1" for all shops
    Then product "product1" should be enabled for shops "shop1,shop2"
    And product "product1" should be indexed for shops "shop1,shop2"
    When I disable product "product1" for shop "shop1"
    Then product "product1" should be disabled for shops "shop1"
    And product "product1" should not be indexed for shops "shop1"
    But product "product1" should be enabled for shops "shop2"
    And product "product1" should be indexed for shops "shop2"
    When I disable product "product1" for all shops
    Then product "product1" should be disabled for shops "shop1,shop2"
    And product "product1" should be disabled for shops "shop1,shop2"
