# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-options-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@clear-cache-after-feature
@product-multishop
@update-options-multishop
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
    And language "french" with locale "fr-FR" exists
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have following options:
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    When I update product "product1" with following values:
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product "product1" should have following options for shops "shop1,shop2":
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And product "product1" should be disabled for shops "shop1,shop2"
    And product "product1" should not be indexed for shops "shop1,shop2"

  Scenario: I update product options for specific shop (not the default one)
    When I update product "product1" for shop "shop2" with following values:
      | visibility          | search      |
      | available_for_order | true        |
      | online_only         | false       |
      | show_price          | true        |
      | condition           | refurbished |
      | show_condition      | false       |
      | manufacturer        |             |
    Then product "product1" should have following options for shops "shop2":
      | product option      | value       |
      | visibility          | search      |
      | available_for_order | true        |
      | online_only         | false       |
      | show_price          | true        |
      | condition           | refurbished |
      | show_condition      | false       |
      | manufacturer        |             |
    And product "product1" should have following options for shops "shop1":
      | product option      | value   |
      | visibility          | catalog |
      | available_for_order | false   |
      | online_only         | true    |
      | show_price          | false   |
      | condition           | used    |
      | show_condition      | true    |
#     manufacturer does not depend on multi shop, so it should be updated no matter which shop is targeted
      | manufacturer        |         |
    And product "product1" should not be indexed for shops "shop1,shop2"

  Scenario: I update product options for all associated shops
    When I update product "product1" for all shops with following values:
      | visibility          | none          |
      | available_for_order | true          |
      | online_only         | true          |
      | show_price          | true          |
      | condition           | new           |
      | show_condition      | true          |
      | manufacturer        | graphicCorner |
    Then product "product1" should have following options for shops "shop1,shop2":
      | product option      | value         |
      | visibility          | none          |
      | available_for_order | true          |
      | online_only         | true          |
      | show_price          | true          |
      | condition           | new           |
      | show_condition      | true          |
      | manufacturer        | graphicCorner |
    And product product1 is not associated to shops "shop3,shop4"

  Scenario: I update product search indexation related values in different shops
    Given product "product1" should not be indexed for shops "shop1,shop2"
    And product "product1" should be disabled for shops "shop2"
#   @todo: UpdateProductStatus command does not yet support multishop, so it silently updates only single shop from context
#          need to improve this scenario once UpdateProductStatus command supports multishop
    When I enable product "product1"
    And I update product "product1" for shop "shop1" with following values:
      | visibility          | search       |
      | available_for_order | true         |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    Then product "product1" should be enabled for shops "shop1"
    And product "product1" should be indexed for shops "shop1"
    But product "product1" should be disabled for shops "shop2"
    And product "product1" should not be indexed for shops "shop2"
