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
    And I enable multishop feature
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

  Scenario: Generate combinations in default shop
    When I generate combinations in shop "shop1" for product product1 using following attributes:
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
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" should have no combinations for shops "shop2"
    And product "product1" should not have a default combination for shop "shop2"

  Scenario: Generate combinations in non-default shop
    When I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [L]           |
      | Color | [White,Black] |
    Then product "product1" should have the following combinations for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
    And product "product1" should have no combinations for shops "shop1"
    And product "product1" should not have a default combination for shop "shop1"
    And product "product1" default combination for shop "shop2" should be "product1LWhite"

  Scenario: Generate different combinations in different shops
    When I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S,M]   |
      | Color | [White] |
    Then product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" should have no combinations for shops "shop2"
    And product "product1" should not have a default combination for shop "shop2"
    And I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [S,M]   |
      | Color | [Black] |
    And product "product1" should have the following combinations for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And product "product1" default combination for shop "shop2" should be "product1SBlack"
    And product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    When I delete combination "product1SBlack" from shops "shop2"
    And I delete combination "product1MBlack" from shops "shop2"
    And product "product1" should have no combinations for shops "shop2"
    And product "product1" should not have a default combination for shop "shop2"
    And I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [S,M]   |
      | Color | [Black] |
    And product "product1" should have the following combinations for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |

  Scenario: Delete default combination and generate same combination again
    Given I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S]           |
      | Color | [White, Blue] |
    And product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And I delete combination "product1SWhite" from shops "shop1"
    Then product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name       | reference | attributes          | impact on price | quantity | is default |
      | product1SBlue  | Size - S, Color - Blue |           | [Size:S,Color:Blue] | 0               | 0        | true       |
    And product "product1" default combination for shop "shop1" should be "product1SBlue"
    When I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S]     |
      | Color | [White] |
    Then product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      |                | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | true       |
      | product1SWhite |                | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SBlue"

  Scenario: Delete default combination and generate it again in same shop while other shop has the same combinations
    Given I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" should have no combinations for shops "shop2"
    And I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop2" should be "product1SWhite"
    Given I delete combination "product1SWhite" from shops "shop1"
    And product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SBlack"
    When I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S]     |
      | Color | [White] |
    Then product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | false      |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SBlack"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Combinations having the same attributes must have the same ID between the shops
    Given I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size | [S] |
    And product "product1" should have the following combinations for shops "shop1":
      | id reference | combination name | reference | attributes | impact on price | quantity | is default |
      | product1S    | Size - S         |           | [Size:S]   | 0               | 0        | true       |
    When I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size | [S] |
    And product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name | reference | attributes | impact on price | quantity | is default |
      | product1S      | Size - S         |           | [Size:S]   | 0               | 0        | true       |

  Scenario: Generate combinations in all shops
    When I generate combinations for product "product1" in all shops using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product "product1" should have the following combinations for shops "shop1,shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Generate combinations in all shops when there is already existing different combinations in one of shops
    Given I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size | [S] |
    And product "product1" should have the following combinations for shops "shop1":
      | id reference | combination name | reference | attributes | impact on price | quantity | is default |
      | product1S    | Size - S         |           | [Size:S]   | 0               | 0        | true       |
    When I generate combinations for product "product1" in all shops using following attributes:
      | Size  | [M]                |
      | Color | [White,Black,Blue] |
    Then product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1S      | Size - S                |           | [Size:S]             | 0               | 0        | true       |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    Then product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | true       |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1S"
    And product "product1" default combination for shop "shop2" should be "product1MWhite"

  Scenario: Generate combinations in all shops when there is already existing identical combinations in one of shops
    Given I generate combinations in shop "shop1" for product "product1" using following attributes:
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
    And product "product1" should not have a default combination for shop "shop2"
    When I generate combinations for product "product1" in all shops using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product "product1" should have the following combinations for shops "shop1,shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Generated combination out of stock type matches with product when generating for specific shop
    Given product "product1" should have following stock information for shops "shop1,shop2":
      | out_of_stock_type | default |
    When I generate combinations in shop "shop1" for product "product1" using following attributes:
      | Size  | [S]           |
      | Color | [White,Black] |
    Then product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
    And product "product1" should have no combinations for shops "shop2"
    And all combinations of product "product1" for shops "shop1" should have the stock policy to "default"
    When I update product "product1" stock for shop "shop2" with following information:
      | out_of_stock_type | available |
    And I generate combinations in shop "shop2" for product "product1" using following attributes:
      | Size  | [S]           |
      | Color | [White,Black] |
    Then product "product1" should have the following combinations for shops "shop1,shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
    And all combinations of product "product1" for shops "shop2" should have the stock policy to "available"
    But all combinations of product "product1" for shops "shop1" should have the stock policy to "default"

  Scenario: Generated combination out of stock type matches with product when generating for all shops
    Given product "product1" should have following stock information for shops "shop1,shop2":
      | out_of_stock_type | default |
    When I generate combinations for product "product1" in all shops using following attributes:
      | Size  | [S]           |
      | Color | [White,Black] |
    And all combinations of product "product1" for shops "shop1" should have the stock policy to "default"
    When I update product "product1" stock for all shops with following information:
      | out_of_stock_type | not_available |
    And I generate combinations for product "product1" in all shops using following attributes:
      | Size  | [S]           |
      | Color | [White,Black] |
    And all combinations of product "product1" for shops "shop2" should have the stock policy to "not_available"
    And all combinations of product "product1" for shops "shop1" should have the stock policy to "not_available"
