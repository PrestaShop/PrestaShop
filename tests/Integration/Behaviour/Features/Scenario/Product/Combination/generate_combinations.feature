# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags generate-combinations
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@generate-combinations
Feature: Generate attribute combinations for product in Back Office (BO)
  As an employee
  I need to be able to generate product attribute combinations from BO

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute group "Dimension" named "Dimension" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists
    And attribute "40x60cm" named "40x60cm" in en language exists
    And attribute "60x90cm" named "60x90cm" in en language exists
    And attribute "80x120cm" named "80x120cm" in en language exists

  Scenario: Generate product combinations
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
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference  |
      | Size        | Size               | false          | select     | 0        | Size       |
      | Color       | Color              | true           | color      | 1        | Color      |
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

  Scenario: Generate product combinations does not create duplicates
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
    When I update combination "product1SWhite" with following values:
      | reference        | ref1SWhite |
    When I update combination "product1MBlack" with following values:
      | reference        | ref1MBlack |
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference  | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White | ref1SWhite | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |            | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |            | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |            | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black | ref1MBlack | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |            | [Size:M,Color:Blue]  | 0               | 0        | false      |
    When I generate combinations for product product1 using following attributes:
      | Size  | [S,M]       |
      | Color | [Black,Red] |
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference  | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White | ref1SWhite | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |            | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |            | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |            | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black | ref1MBlack | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |            | [Size:M,Color:Blue]  | 0               | 0        | false      |
      | product1SRed   | Size - S, Color - Red   |            | [Size:S,Color:Red]   | 0               | 0        | false      |
      | product1MRed   | Size - M, Color - Red   |            | [Size:M,Color:Red]   | 0               | 0        | false      |
    And product "product1" should have the following list of attribute groups:
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference  |
      | Size        | Size               | false          | select     | 0        | Size       |
      | Color       | Color              | true           | color      | 1        | Color      |
    And product "product1" should have the following list of attributes in attribute group "Size":
      | name[en-US] | color | position | reference |
      | S           |       | 0        | S         |
      | M           |       | 1        | M         |
    And product "product1" should have the following list of attributes in attribute group "Color":
      | name[en-US] | color   | position | reference |
      | White       | #ffffff | 3        | White     |
      | Red         | #E84C3D | 5        | Red       |
      | Black       | #434A54 | 6        | Black     |
      | Blue        | #5D9CEC | 9        | Blue      |
    And combination product1SWhite should be named "Size - S, Color - White"
    And combination product1SBlack should be named "Size - S, Color - Black"
    And combination product1SBlue should be named "Size - S, Color - Blue"
    And combination product1SRed should be named "Size - S, Color - Red"
    And combination product1MWhite should be named "Size - M, Color - White"
    And combination product1MBlack should be named "Size - M, Color - Black"
    And combination product1MBlue should be named "Size - M, Color - Blue"
    And combination product1MRed should be named "Size - M, Color - Red"
    # These new combinations use three kind of attributes so they do not match with previous ones and are added
    When I generate combinations for product product1 using following attributes:
      | Size      | [S]                |
      | Color     | [White,Black,Blue] |
      | Dimension | [40x60cm]          |
    Then product "product1" should have following combinations:
      | id reference         | combination name                             | reference  | attributes                             | impact on price | quantity | is default |
      | product1SWhite       | Size - S, Color - White                      | ref1SWhite | [Size:S,Color:White]                   | 0               | 0        | true       |
      | product1SBlack       | Size - S, Color - Black                      |            | [Size:S,Color:Black]                   | 0               | 0        | false      |
      | product1SBlue        | Size - S, Color - Blue                       |            | [Size:S,Color:Blue]                    | 0               | 0        | false      |
      | product1MWhite       | Size - M, Color - White                      |            | [Size:M,Color:White]                   | 0               | 0        | false      |
      | product1MBlack       | Size - M, Color - Black                      | ref1MBlack | [Size:M,Color:Black]                   | 0               | 0        | false      |
      | product1MBlue        | Size - M, Color - Blue                       |            | [Size:M,Color:Blue]                    | 0               | 0        | false      |
      | product1SRed         | Size - S, Color - Red                        |            | [Size:S,Color:Red]                     | 0               | 0        | false      |
      | product1MRed         | Size - M, Color - Red                        |            | [Size:M,Color:Red]                     | 0               | 0        | false      |
      | product1SWhite4060cm | Size - S, Color - White, Dimension - 40x60cm |            | [Size:S,Color:White,Dimension:40x60cm] | 0               | 0        | false       |
      | product1SBlack4060cm | Size - S, Color - Black, Dimension - 40x60cm |            | [Size:S,Color:Black,Dimension:40x60cm] | 0               | 0        | false       |
      | product1SBlue4060cm  | Size - S, Color - Blue, Dimension - 40x60cm  |            | [Size:S,Color:Blue,Dimension:40x60cm]  | 0               | 0        | false       |
    And product "product1" should have the following list of attribute groups:
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference  |
      | Size        | Size               | false          | select     | 0        | Size       |
      | Color       | Color              | true           | color      | 1        | Color      |
      | Dimension   | Dimension          | false          | select     | 2        | Dimension  |
    And product "product1" should have the following list of attributes in attribute group "Dimension":
      | name[en-US] | color | position | reference |
      | 40x60cm     |       | 0        | 40x60cm   |
    And combination product1SWhite4060cm should be named "Size - S, Color - White, Dimension - 40x60cm"
    And combination product1SBlack4060cm should be named "Size - S, Color - Black, Dimension - 40x60cm"
    And combination product1SBlue4060cm should be named "Size - S, Color - Blue, Dimension - 40x60cm"
