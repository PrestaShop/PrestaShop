# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags delete-combination
@reset-database-before-feature
@clear-cache-before-feature
@product-combination
@delete-combination
Feature: Remove attribute combinations for product in Back Office (BO)
  As an employee
  I need to be able to remove product attribute combinations from BO

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
    And attribute "Red" named "Red" in en language exists

  Scenario: Delete product combination one by one
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And product product1 does not have a default combination
    When I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product product1 default combination should be "product1SWhite"
    And product "product1" should have the following list of attribute groups:
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference |
      | Size        | Size               | false          | select     | 0        | Size      |
      | Color       | Color              | true           | color      | 1        | Color     |
    And product "product1" should have the following list of attributes in attribute group "Size":
      | name[en-US] | color | position | reference |
      | S           |       | 0        | S         |
      | M           |       | 1        | M         |
    And product "product1" should have the following list of attributes in attribute group "Color":
      | name[en-US] | color   | position | reference |
      | White       | #ffffff | 3        | White     |
      | Black       | #434A54 | 6        | Black     |
      | Blue        | #5D9CEC | 9        | Blue      |
    And combination product1SWhite should be named "Size - S, Color - White"
    And combination product1SBlack should be named "Size - S, Color - Black"
    And combination product1SBlue should be named "Size - S, Color - Blue"
    And combination product1MWhite should be named "Size - M, Color - White"
    And combination product1MBlack should be named "Size - M, Color - Black"
    And combination product1MBlue should be named "Size - M, Color - Blue"
    When I delete combination product1MWhite
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    When I delete combination product1SWhite
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product product1 default combination should be "product1SBlack"
    And product "product1" should have the following list of attributes in attribute group "Color":
      | name[en-US] | color   | position | reference |
      | Black       | #434A54 | 6        | Black     |
      | Blue        | #5D9CEC | 9        | Blue      |
    When I delete combination product1SBlack
    And I delete combination product1SBlue
    And I delete combination product1MBlack
    And I delete combination product1MBlue
    Then product product1 type should be combinations
    And product product1 does not have a default combination
    And product product1 should have no attribute groups

  Scenario: Bulk delete product combinations
    Given product product1 does not have a default combination
    And product product1 should have no attribute groups
    And I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product product1 default combination should be "product1SWhite"
    When I delete following combinations of product product1:
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | true       |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product product1 default combination should be "product1MWhite"
    When I delete following combinations of product product1:
      | id reference   |
      | product1MWhite |
      | product1MBlue  |
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | true       |
    And product product1 default combination should be "product1MBlack"
