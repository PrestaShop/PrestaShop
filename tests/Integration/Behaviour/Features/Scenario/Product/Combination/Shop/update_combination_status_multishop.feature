# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-status-multishop
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@update-combination-status-multishop
Feature: Enable and disable combinations from Back Office (BO) in multi shop context
  As an employee
  I need to be able to disable a combination in some shops only

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And shop "shop1" with name "test_shop" exists
    And I enable multishop feature
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I associate attribute group "Size" with shops "shop1,shop2"
    And I associate attribute group "Color" with shops "shop1,shop2"
    And I associate attribute "S" with shops "shop1,shop2"
    And I associate attribute "M" with shops "shop1,shop2"
    And I associate attribute "White" with shops "shop1,shop2"
    And I associate attribute "Black" with shops "shop1,shop2"
    And single shop context is loaded
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    And product "product1" should have no combinations for shops "shop2"
    And I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    And product "product1" should have the following combinations for shops "shop1,shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Disable the default combination in one shop only
    When I update combination "product1SWhite" with following values for shop "shop2":
      | active | false |
    Then combination "product1SWhite" should be enabled for shops "shop1"
    And combination "product1SWhite" should be disabled for shops "shop2"
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SBlack"

  Scenario: Disable the default combination in all shops
    When I update combination "product1SWhite" with following values for all shops:
      | active | false |
    Then combination "product1SWhite" should be disabled for shops "shop1,shop2"
    And product "product1" default combination for shop "shop1" should be "product1SBlack"
    And product "product1" default combination for shop "shop2" should be "product1SBlack"
    When I update combination "product1SWhite" with following values for all shops:
      | active | true |
    Then combination "product1SWhite" should be enabled for shops "shop1,shop2"
    And product "product1" default combination for shop "shop1" should be "product1SBlack"
