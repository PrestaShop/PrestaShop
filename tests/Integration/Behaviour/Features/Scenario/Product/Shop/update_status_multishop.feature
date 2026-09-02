# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-status-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-multishop
@update-status-multishop
Feature: Feature: Update product options from Back Office (BO) for multiple shops
  As a BO user
  I want to be able to update product fields associated with options in multiple shops.

  Background:
    Given shop "shop1" with name "test_shop" exists
    And manufacturer studioDesign named "Studio Design" exists
    And manufacturer graphicCorner named "Graphic Corner" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    When I set following shops for product "product1":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    Then product "product1" should have following options for shops "shop1,shop2,shop3,shop4":
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    Then product "product1" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product1" should not be indexed for shops "shop1,shop2,shop3,shop4"

  Scenario: I update product status in different shops
    Given product "product1" should not be indexed for shops "shop1,shop2,shop3,shop4"
    And product "product1" should be disabled for shops "shop1,shop2,shop3,shop4"
    When I enable product "product1" for shop "shop2"
    When I enable product "product1" for shop "shop3"
    Then product "product1" should be enabled for shops "shop2,shop3"
    And product "product1" should be indexed for shops "shop2,shop3"
    But product "product1" should be disabled for shops "shop1,shop4"
    And product "product1" should not be indexed for shops "shop1,shop4"
    When I disable product "product1" for shop "shop3"
    Then product "product1" should be enabled for shops "shop2"
    And product "product1" should be indexed for shops "shop2"
    But product "product1" should be disabled for shops "shop1,shop3,shop4"
    And product "product1" should not be indexed for shops "shop1,shop3,shop4"

  Scenario: I update product status on all shops
    Given product "product1" should not be indexed for shops "shop1,shop2,shop3,shop4"
    And product "product1" should be disabled for shops "shop1,shop2,shop3,shop4"
    When I enable product "product1" for shop "shop2"
    When I enable product "product1" for shop "shop3"
    Then product "product1" should be enabled for shops "shop2,shop3"
    And product "product1" should be indexed for shops "shop2,shop3"
    But product "product1" should be disabled for shops "shop1,shop4"
    And product "product1" should not be indexed for shops "shop1,shop4"
    When I disable product "product1" for all shops
    Then product "product1" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product1" should not be indexed for shops "shop1,shop2,shop3,shop4"
    When I enable product "product1" for all shops
    Then product "product1" should be enabled for shops "shop1,shop2,shop3,shop4"
    And product "product1" should be indexed for shops "shop1,shop2,shop3,shop4"

  Scenario: I update product status for a group
    Given product "product1" should not be indexed for shops "shop1,shop2,shop3,shop4"
    And product "product1" should be disabled for shops "shop1,shop2,shop3,shop4"
    When I enable product "product1" for shop "shop2"
    When I enable product "product1" for shop "shop3"
    Then product "product1" should be enabled for shops "shop2,shop3"
    And product "product1" should be indexed for shops "shop2,shop3"
    And product "product1" should be disabled for shops "shop1,shop4"
    And product "product1" should not be indexed for shops "shop1,shop4"
    When I disable product "product1" for shop group "default_shop_group"
    Then product "product1" should be enabled for shops "shop3"
    And product "product1" should be indexed for shops "shop3"
    But product "product1" should be disabled for shops "shop1,shop2,shop4"
    And product "product1" should not be indexed for shops "shop1,shop2,shop4"
    When I enable product "product1" for shop group "test_second_shop_group"
    Then product "product1" should be enabled for shops "shop3,shop4"
    And product "product1" should be indexed for shops "shop3,shop4"
    But product "product1" should be disabled for shops "shop1,shop2"
    And product "product1" should not be indexed for shops "shop1,shop2"
    When I disable product "product1" for shop group "test_second_shop_group"
    And I enable product "product1" for shop group "default_shop_group"
    Then product "product1" should be enabled for shops "shop1,shop2"
    And product "product1" should be indexed for shops "shop1,shop2"
    But product "product1" should be disabled for shops "shop3,shop4"
    And product "product1" should not be indexed for shops "shop3,shop4"
