# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags generate-multi-shop-combination
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-combination
@generate-combination
@product-multi-shop
@generate-multi-shop-combination
Feature: Generate combination from Back Office (BO) when using multi-shop feature
  As a BO user
  I need to be able to generate product combinations from BO for specified shop when using multi-shop feature

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And shop "shop1" with name "test_shop" exists
    And multistore feature is enabled
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I copy product product1 from shop shop1 to shop shop2

  Scenario: I generate combinations in default shop
    And I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" should have no combinations for shops "shop2"

  Scenario: I generate combinations in non-default shop
    When I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [L]           |
      | Color | [White,Black] |
    Then product "product1" should have the following combinations for shops "shop2":
      | id reference        | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhiteShop2 | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlackShop2 | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
    And product "product1" should have no combinations for shops "shop1"
    And product "product1" should not have a default combination for shop "shop1"
    And product "product1" default combination for shop "shop2" should be "product1LWhiteShop2"
