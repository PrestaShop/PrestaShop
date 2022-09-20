# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags delete-multi-shop-combination
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-combination
@delete-combination
@product-multi-shop
@delete-multi-shop-combination
Feature: Delete combination from Back Office (BO) in multiple shops
  As a BO user
  I need to be able to delete product combinations from BO in multiple shops

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
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
#   @todo: if I skip copying product then it doesnt exist in shop2, but further combinations generation for shop2 doesn't fail. I think it should
    And I copy product product1 from shop shop1 to shop shop2
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
    When I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product "product1" should have the following combinations for shops "shop2":
      | id reference        | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhiteShop2 | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlackShop2 | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlueShop2  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhiteShop2 | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlackShop2 | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlueShop2  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#   @todo: this fails
    And product "product1" default combination should be "product1SWhite"
#
#  Scenario: Delete one non-default combination from the default shop
#    When I delete combination "product1SBlack" from shop "shop1"
#    Then product "product1" should have the following combinations for shops "shop1":
#      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
#      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
#      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
#      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
#      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
#      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#    And product "product1" should have the following combinations for shops "shop2":
#      | id reference        | combination name        | reference | attributes           | impact on price | quantity | is default |
#      | product1SWhiteShop2 | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
#      | product1SBlackShop2 | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
#      | product1SBlueShop2  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
#      | product1MWhiteShop2 | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
#      | product1MBlackShop2 | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
#      | product1MBlueShop2  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#    And product "product1" default combination should be "product1SWhite"
#
#  Scenario: Delete one default combination from the default shop
#    When I delete combination "product1SWhite" from shop "shop1"
#    Then product "product1" should have the following combinations for shops "shop1":
#      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
#      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
#      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
#      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
#      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
#      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#    And product "product1" should have the following combinations for shops "shop2":
#      | id reference        | combination name        | reference | attributes           | impact on price | quantity | is default |
#      | product1SWhiteShop2 | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
#      | product1SBlackShop2 | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
#      | product1SBlueShop2  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
#      | product1MWhiteShop2 | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
#      | product1MBlackShop2 | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
#      | product1MBlueShop2  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#    And product "product1" default combination should be "product1SBlack"
#
#  Scenario: Delete one default combination from the non-default shop
#    When I delete combination "product1SWhiteShop2" from shop "shop2"
#    Then product "product1" should have the following combinations for shops "shop1":
#      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
#      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
#      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
#      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
#      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
#      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
#      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#    Then product "product1" should have the following combinations for shops "shop2":
#      | id reference        | combination name        | reference | attributes           | impact on price | quantity | is default |
#      | product1SBlackShop2 | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
#      | product1SBlueShop2  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
#      | product1MWhiteShop2 | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
#      | product1MBlackShop2 | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
#      | product1MBlueShop2  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#    And product "product1" default combination should be "product1SWhite"
#
#  Scenario: Delete one non-default combination from the non-default shop
#    When I delete combination "product1SBlueShop2" from shop "shop2"
#    Then product "product1" should have the following combinations for shops "shop1":
#      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
#      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
#      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
#      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
#      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
#      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
#      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#    Then product "product1" should have the following combinations for shops "shop2":
#      | id reference        | combination name        | reference | attributes           | impact on price | quantity | is default |
#      | product1SWhiteShop2 | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
#      | product1SBlackShop2 | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
#      | product1MWhiteShop2 | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
#      | product1MBlackShop2 | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
#      | product1MBlueShop2  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#    And product "product1" default combination should be "product1SWhite"

  Scenario: Delete all combinations one by one from the default shop
    When I delete combination "product1SWhite" from shop "shop1"
    And I delete combination "product1SBlack" from shop "shop1"
    And I delete combination "product1SBlue" from shop "shop1"
    And I delete combination "product1MWhite" from shop "shop1"
    And I delete combination "product1MBlack" from shop "shop1"
    And I delete combination "product1MBlue" from shop "shop1"
    And product "product1" should have no combinations for shops "shop1"
    And product "product1" should have the following combinations for shops "shop2":
      | id reference        | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhiteShop2 | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlackShop2 | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlueShop2  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhiteShop2 | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlackShop2 | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlueShop2  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
#    @todo: apparently when there is no combinations for default shop, then there is no cache_default_attribute too
#    @todo: but there should still be a default_on combination for shop2 I suppose?, but loading combination for shop2 doesn't seem to work well
    And product "product1" default combination should be "product1SWhiteShop2" for shop "shop2"
#    And product "product1" should not have a default combination

#  Scenario: Delete all combinations one by one from all shops
#    When I delete combination "product1SWhite" from shop "shop1"
#    And I delete combination "product1SBlack" from shop "shop1"
#    And I delete combination "product1SBlue" from shop "shop1"
#    And I delete combination "product1MWhite" from shop "shop1"
#    And I delete combination "product1MBlack" from shop "shop1"
#    And I delete combination "product1MBlue" from shop "shop1"
#    And I delete combination "product1SWhiteShop2" from shop "shop2"
#    And I delete combination "product1SBlackShop2" from shop "shop2"
#    And I delete combination "product1SBlueShop2" from shop "shop2"
#    And I delete combination "product1MWhiteShop2" from shop "shop2"
#    And I delete combination "product1MBlackShop2" from shop "shop2"
#    And I delete combination "product1MBlueShop2" from shop "shop2"
#    Then product "product1" should have no combinations for shops "shop1"
#    And product "product1" should have no combinations for shops "shop2"
#    And product "product1" should not have a default combination
