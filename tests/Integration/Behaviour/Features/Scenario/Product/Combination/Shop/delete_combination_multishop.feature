# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags delete-combination-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-combination
@delete-combination
@product-multishop
@delete-combination-multishop
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
    And I enable multishop feature
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I associate attribute group "Size" with shops "shop1,shop2"
    And I associate attribute group "Color" with shops "shop1,shop2"
    And I associate attribute "S" with shops "shop1,shop2"
    And I associate attribute "M" with shops "shop1,shop2"
    And I associate attribute "White" with shops "shop1,shop2"
    And I associate attribute "Black" with shops "shop1,shop2"
    And I associate attribute "Blue" with shops "shop1,shop2"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have no combinations for shops "shop2"
    And I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have the following combinations for shops "shop1,shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Delete one non-default combination from the default shop
    When I delete combination "product1SBlack" from shops "shop1"
    Then product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Delete one default combination from the default shop
    When I delete combination "product1SWhite" from shops "shop1"
    Then product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SBlack"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Delete one default combination from the non-default shop
    When I delete combination "product1SWhite" from shops "shop2"
    Then product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    Then product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SBlack"

  Scenario: Delete one non-default combination from the non-default shop
    When I delete combination "product1SBlue" from shops "shop2"
    Then product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    Then product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Delete all combinations one by one from the default shop
    When I delete combination "product1SWhite" from shops "shop1"
    And I delete combination "product1SBlack" from shops "shop1"
    And I delete combination "product1SBlue" from shops "shop1"
    And I delete combination "product1MWhite" from shops "shop1"
    And I delete combination "product1MBlack" from shops "shop1"
    And I delete combination "product1MBlue" from shops "shop1"
    And product "product1" should have no combinations for shops "shop1"
    And product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" should not have a default combination for shop "shop1"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Delete all combinations one by one from all shops
    When I delete combination "product1SWhite" from shops "shop1"
    And I delete combination "product1SBlack" from shops "shop1"
    And I delete combination "product1SBlue" from shops "shop1"
    And I delete combination "product1MWhite" from shops "shop1"
    And I delete combination "product1MBlack" from shops "shop1"
    And I delete combination "product1MBlue" from shops "shop1"
    And I delete combination "product1SWhite" from shops "shop2"
    And I delete combination "product1SBlack" from shops "shop2"
    And I delete combination "product1SBlue" from shops "shop2"
    And I delete combination "product1MWhite" from shops "shop2"
    And I delete combination "product1MBlack" from shops "shop2"
    And I delete combination "product1MBlue" from shops "shop2"
    Then product "product1" should have no combinations for shops "shop1"
    And product "product1" should have no combinations for shops "shop2"
    And product "product1" should not have a default combination for shop "shop1"
    And product "product1" should not have a default combination for shop "shop2"

  Scenario: Bulk delete combinations from specified shop
    When I delete following combinations of product product1 from shop "shop1":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
    Then product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | true       |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1MWhite"
    And product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop2" should be "product1SWhite"
    When I delete following combinations of product product1 from shop "shop2":
      | id reference   |
      | product1MBlue  |
      | product1MBlack |
      | product1MWhite |
    Then product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop2" should be "product1SWhite"
    And product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | true       |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1MWhite"
    When I delete following combinations of product product1 from shop "shop1":
      | id reference   |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |
    And I delete following combinations of product product1 from shop "shop2":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
    Then product "product1" should have no combinations for shops "shop1"
    And product "product1" should not have a default combination for shop "shop1"
    Then product "product1" should have no combinations for shops "shop2"
    And product "product1" should not have a default combination for shop "shop2"

  Scenario: Delete all the combinations for a specific shop and generate the same combinations again for the same shop
    When I delete following combinations of product product1 from shop "shop1":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |
    Then product "product1" should have no combinations for shops "shop1"
    And product "product1" should not have a default combination for shop "shop1"
    And I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Delete combination for all shops
    When I delete combination "product1SBlack" from all shops
    Then product "product1" should have the following combinations for shops "shop1,shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: Bulk delete combinations for all shops
    When I delete following combinations of product "product1" from all shops:
      | id reference   |
      | product1SWhite |
      | product1MBlue  |
    Then product "product1" should have the following combinations for shops "shop1,shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SBlack"
    And product "product1" default combination for shop "shop2" should be "product1SBlack"

  Scenario: Delete combination for all shops while not associated to default shop anymore
    When I delete following combinations of product "product1" from shop shop1:
      | id reference   |
      | product1SWhite |
      | product1MBlue  |
    Then product "product1" should have the following combinations for shops "shop1":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And product "product1" should have the following combinations for shops "shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SBlack"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"
    # Now delete from all shops but the combinations are not in shop1 anymore
    When I delete following combinations of product "product1" from all shops:
      | id reference   |
      | product1SWhite |
      | product1MBlue  |
    Then product "product1" should have the following combinations for shops "shop1,shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SBlack"
    And product "product1" default combination for shop "shop2" should be "product1SBlack"
