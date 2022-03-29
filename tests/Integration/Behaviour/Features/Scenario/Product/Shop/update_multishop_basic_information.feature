# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-basic-information
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-multi-shop
@update-multi-shop-basic-information
Feature: Update product basic information from Back Office (BO)
  As a BO user
  I need to be able to update product basic information from BO

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
    When I update product "product1" basic information with following values:
      | name[en-US]              | magic staff              |
      | description[en-US]       | such a super magic staff |
      | description_short[en-US] | super magic staff        |
    And I copy product product1 from shop shop1 to shop shop2
    Then product "product1" localized "name" for shops "shop1,shop2" should be:
      | locale | value       |
      | en-US  | magic staff |
    And product "product1" localized "description" for shops "shop1,shop2" should be:
      | locale | value                    |
      | en-US  | such a super magic staff |
    And product "product1" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value             |
      | en-US  | super magic staff |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product basic information for specific shop
    When I update product "product1" basic information for shop "shop2" with following values:
      | name[en-US]              | cool magic staff        |
      | description[en-US]       | such a cool magic staff |
      | description_short[en-US] | cool magic staff        |
    Then product "product1" localized "name" for shops "shop1" should be:
      | locale | value       |
      | en-US  | magic staff |
    And product "product1" localized "description" for shops "shop1" should be:
      | locale | value                    |
      | en-US  | such a super magic staff |
    And product "product1" localized "description_short" for shops "shop1" should be:
      | locale | value             |
      | en-US  | super magic staff |
    But product "product1" localized "name" for shops "shop2" should be:
      | locale | value            |
      | en-US  | cool magic staff |
    And product "product1" localized "description" for shops "shop2" should be:
      | locale | value                   |
      | en-US  | such a cool magic staff |
    And product "product1" localized "description_short" for shops "shop2" should be:
      | locale | value            |
      | en-US  | cool magic staff |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product basic information for all associated shop
    When I update product "product1" basic information for all shops with following values:
      | name[en-US]              | cool magic staff        |
      | description[en-US]       | such a cool magic staff |
      | description_short[en-US] | cool magic staff        |
    Then product "product1" localized "name" for shops "shop1,shop2" should be:
      | locale | value            |
      | en-US  | cool magic staff |
    And product "product1" localized "description" for shops "shop1,shop2" should be:
      | locale | value                   |
      | en-US  | such a cool magic staff |
    And product "product1" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value            |
      | en-US  | cool magic staff |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update some fields for single shop and right after for all shops
    When I update product "product1" basic information for shop "shop2" with following values:
      | name[en-US]              | cool magic staff        |
      | description[en-US]       | such a cool magic staff |
    And I update product "product1" basic information for all shops with following values:
      | description_short[en-US] | weird magic staff       |
    Then product "product1" localized "name" for shops "shop2" should be:
      | locale | value            |
      | en-US  | cool magic staff |
    And product "product1" localized "description" for shops "shop2" should be:
      | locale | value                   |
      | en-US  | such a cool magic staff |
    And product "product1" localized "name" for shops "shop1" should be:
      | locale | value       |
      | en-US  | magic staff |
    And product "product1" localized "description" for shops "shop1" should be:
      | locale | value                    |
      | en-US  | such a super magic staff |
    And product "product1" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value             |
      | en-US  | weird magic staff |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update some fields for all shops and right after for single shops
    When I update product "product1" basic information for all shops with following values:
      | name[en-US]              | cool magic staff        |
      | description[en-US]       | such a cool magic staff |
    And I update product "product1" basic information for shop "shop2" with following values:
      | description_short[en-US] | weird magic staff       |
    Then product "product1" localized "name" for shops "shop1,shop2" should be:
      | locale | value            |
      | en-US  | cool magic staff |
    And product "product1" localized "description" for shops "shop1,shop2" should be:
      | locale | value                   |
      | en-US  | such a cool magic staff |
    And product "product1" localized "description_short" for shops "shop1" should be:
      | locale | value             |
      | en-US  | super magic staff |
    And product "product1" localized "description_short" for shops "shop2" should be:
      | locale | value             |
      | en-US  | weird magic staff |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4
