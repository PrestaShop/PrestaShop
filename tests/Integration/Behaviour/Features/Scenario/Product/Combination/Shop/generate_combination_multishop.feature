# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags generate-combination-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-combination
@generate-combination
@product-multishop
@generate-combination-multishop
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
    And I associate attribute group "Size" with shops "shop1,shop2"
    And I associate attribute group "Color" with shops "shop1,shop2"
    And I associate attribute "S" with shops "shop1,shop2"
    And I associate attribute "M" with shops "shop1,shop2"
    And I associate attribute "L" with shops "shop1,shop2"
    And I associate attribute "White" with shops "shop1,shop2"
    And I associate attribute "Black" with shops "shop1,shop2"
    And I associate attribute "Blue" with shops "shop1,shop2"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |

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

  Scenario: I cannot generate combinations for all shops when not all selected attributes are present in all shops
    Given I add product "product2" to shop "shop3" with following information:
      | name[en-US] | universal T-shirt2 |
      | type        | combinations       |
    And I associate attribute group "Size" with shops "shop3,shop4"
    And I associate attribute "S" with shops "shop3"
    But attribute "S" is not associated to shops "shop4"
    And I associate attribute "M" with shops "shop3,shop4"
    And I set following shops for product "product2":
      | source shop | shop3       |
      | shops       | shop3,shop4 |
    And product "product2" should have no combinations for shops "shop3,shop4"
    # Generate when attribute is missing in another shop
    When I generate combinations for product "product2" in all shops using following attributes:
      | Size | [S,M] |
    Then I should get error that it is not allowed to generate combinations when not all attributes are present in all shops
    And I associate attribute "S" with shops "shop3,shop4"
    When I generate combinations for product "product2" in all shops using following attributes:
      | Size | [S,M] |
    Then product "product2" should have the following combinations for shops "shop3,shop4":
      | id reference | combination name | reference | attributes | impact on price | quantity | is default |
      | product2S    | Size - S         |           | [Size:S]   | 0               | 0        | true       |
      | product2M    | Size - M         |           | [Size:M]   | 0               | 0        | false      |
    And I associate attribute group "Color" with shops "shop4"
    But attribute group "Color" is not associated to shops "shop3"
    # generate when attribute group is missing in another shop
    When I generate combinations for product "product2" in all shops using following attributes:
      | Size  | [S,M]   |
      | Color | [White] |
    Then I should get error that it is not allowed to generate combinations when not all attributes are present in all shops
    When I associate attribute "L" with shops "shop3,shop4"
    # make sure that missing attribute ("White") doesn't break generation when it is not selected for generation
    And I generate combinations for product "product2" in all shops using following attributes:
      | Size | [L] |
    Then product "product2" should have the following combinations for shops "shop3,shop4":
      | id reference | combination name | reference | attributes | impact on price | quantity | is default |
      | product2S    | Size - S         |           | [Size:S]   | 0               | 0        | true       |
      | product2M    | Size - M         |           | [Size:M]   | 0               | 0        | false      |
      | product2L    | Size - L         |           | [Size:L]   | 0               | 0        | false      |
    And product "product2" should have the following list of attribute groups for shops "shop3,shop4":
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference |
      | Size        | Size               | false          | select     | 0        | Size      |
    And product "product2" should have the following list of attributes in attribute group "Size" for shops "shop3,shop4":
      | name[en-US] | color | position | reference |
      | S           |       | 0        | S         |
      | M           |       | 1        | M         |
      | L           |       | 2        | L         |
    And product "product2" should have the following list of attribute groups for all shops:
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference |
      | Size        | Size               | false          | select     | 0        | Size      |
    And product "product2" should have the following list of attributes in attribute group "Size" for all shops:
      | name[en-US] | color | position | reference |
      | S           |       | 0        | S         |
      | M           |       | 1        | M         |
      | L           |       | 2        | L         |
    When I associate attribute group "Color" with shops "shop3,shop4"
    And I associate attribute "White" with shops "shop3,shop4"
    # generate when attribute groups and attributes are assocaited to all shops
    And I generate combinations for product "product2" in all shops using following attributes:
      | Size  | [S,M,L] |
      | Color | [White] |
    Then product "product2" should have the following combinations for shops "shop3,shop4":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product2S      | Size - S                |           | [Size:S]             | 0               | 0        | true       |
      | product2M      | Size - M                |           | [Size:M]             | 0               | 0        | false      |
      | product2L      | Size - L                |           | [Size:L]             | 0               | 0        | false      |
      | product2SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | false      |
      | product2MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product2LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | false      |
    And product "product2" should have the following list of attribute groups for all shops:
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference |
      | Size        | Size               | false          | select     | 0        | Size      |
      | Color       | Color              | true           | color      | 1        | Color     |
    And product "product2" should have the following list of attributes in attribute group "Color" for all shops:
      | name[en-US] | color   | position | reference |
      | White       | #ffffff | 3        | White     |
    And product "product2" should have the following list of attributes in attribute group "Color" for all shops:
      | name[en-US] | color   | position | reference |
      | White       | #ffffff | 3        | White     |
    And product "product2" should have the following list of attributes in attribute group "Size" for shops "shop4":
      | name[en-US] | color | position | reference |
      | S           |       | 0        | S         |
      | M           |       | 1        | M         |
      | L           |       | 2        | L         |
    And product "product2" should have the following list of attributes in attribute group "Size" for all shops:
      | name[en-US] | color | position | reference |
      | S           |       | 0        | S         |
      | M           |       | 1        | M         |
      | L           |       | 2        | L         |
