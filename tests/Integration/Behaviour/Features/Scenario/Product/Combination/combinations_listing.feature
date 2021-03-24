# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags combinations-listing
@reset-database-before-feature
@clear-cache-before-feature
@product-combination
@combinations-listing
Feature: Generate attribute combinations for product in Back Office (BO)
  As an employee
  I need to be able to generate product attribute combinations from BO

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

  Scenario: I can see a list of product combinations
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | is_virtual  | false             |
    And product product1 type should be standard
    And product product1 does not have a default combination
    And product "product1" combinations list search criteria is reset to defaults
    When I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White |                       | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black |                       | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |                       | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
    And product product1 default combination should be "product1SWhite"

  Scenario: I can paginate combinations and limit combinations per page
    Given product "product1" combinations list search criteria is reset to defaults
    And I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White |                       | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black |                       | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |                       | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria | value |
      | page     | 1     |
      | limit    | 5     |
    Then I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White |                       | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black |                       | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |                       | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria | value |
      | page     | 2     |
      | limit    | 5     |
    Then I should see following combinations list of product "product1":
      | reference     | combination name       | combination reference | attributes          | impact on price | final price | quantity | is default |
      | product1MBlue | Size - M, Color - Blue |                       | [Size:M,Color:Blue] | 0               | 0           | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria | value |
      | page     | 3     |
      | limit    | 5     |
    Then combinations list of product "product1" should be empty

  Scenario: I can filter combinations by attributes
    Given product "product1" combinations list search criteria is reset to defaults
    And I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White |                       | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black |                       | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |                       | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria   | value |
      | attributes | [S,M] |
    Then I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White |                       | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black |                       | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |                       | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria   | value |
      | attributes | [S]   |
    And I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White |                       | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black |                       | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |                       | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria   | value  |
      | attributes | [Blue] |
    Then I should see following combinations list of product "product1":
      | reference     | combination name       | combination reference | attributes          | impact on price | final price | quantity | is default |
      | product1SBlue | Size - S, Color - Blue |                       | [Size:S,Color:Blue] | 0               | 0           | 0        | false      |
      | product1MBlue | Size - M, Color - Blue |                       | [Size:M,Color:Blue] | 0               | 0           | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria   | value    |
      | attributes | [M,Blue] |
    Then I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SBlue  | Size - S, Color - Blue  |                       | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |

  Scenario: I can sort combinations by reference, quantity, impact on price
    Given product "product1" combinations list search criteria is reset to defaults
    And I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White |                       | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black |                       | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |                       | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
    And I update combination "product1SWhite" from list with following values:
      | impact on price       | -1  |
      | quantity              | 10  |
      | combination reference | AAA |
    And I update combination "product1SBlue" from list with following values:
      | impact on price       | 1   |
      | quantity              | 100 |
      | combination reference | BBB |
    And I update combination "product1SBlack" from list with following values:
      | impact on price       | 10  |
      | quantity              | 50  |
      | combination reference | CCC |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value           |
      | order by  | impact on price |
      | order way | asc             |
    Then I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White | AAA                   | [Size:S,Color:White] | -1              | 0           | 10       | true       |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  | BBB                   | [Size:S,Color:Blue]  | 1               | 0           | 100      | false      |
      | product1SBlack | Size - S, Color - Black | CCC                   | [Size:S,Color:Black] | 10              | 0           | 50       | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value                 |
      | order by  | combination reference |
      | order way | asc                   |
    Then I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1SWhite | Size - S, Color - White | AAA                   | [Size:S,Color:White] | -1              | 0           | 10       | true       |
      | product1SBlue  | Size - S, Color - Blue  | BBB                   | [Size:S,Color:Blue]  | 1               | 0           | 100      | false      |
      | product1SBlack | Size - S, Color - Black | CCC                   | [Size:S,Color:Black] | 10              | 0           | 50       | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value                 |
      | order by  | combination reference |
      | order way | desc                  |
      | page      | 1                     |
      | limit     | 3                     |
    Then I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SBlack | Size - S, Color - Black | CCC                   | [Size:S,Color:Black] | 10              | 0           | 50       | false      |
      | product1SBlue  | Size - S, Color - Blue  | BBB                   | [Size:S,Color:Blue]  | 1               | 0           | 100      | false      |
      | product1SWhite | Size - S, Color - White | AAA                   | [Size:S,Color:White] | -1              | 0           | 10       | true       |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value    |
      | order by  | quantity |
      | order way | asc      |
    Then I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1SWhite | Size - S, Color - White | AAA                   | [Size:S,Color:White] | -1              | 0           | 10       | true       |
      | product1SBlack | Size - S, Color - Black | CCC                   | [Size:S,Color:Black] | 10              | 0           | 50       | false      |
      | product1SBlue  | Size - S, Color - Blue  | BBB                   | [Size:S,Color:Blue]  | 1               | 0           | 100      | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value    |
      | order by  | quantity |
      | order way | desc     |
    Then I should see following combinations list of product "product1":
      | reference      | combination name        | combination reference | attributes           | impact on price | final price | quantity | is default |
      | product1SBlue  | Size - S, Color - Blue  | BBB                   | [Size:S,Color:Blue]  | 1               | 0           | 100      | false      |
      | product1SBlack | Size - S, Color - Black | CCC                   | [Size:S,Color:Black] | 10              | 0           | 50       | false      |
      | product1SWhite | Size - S, Color - White | AAA                   | [Size:S,Color:White] | -1              | 0           | 10       | true       |
      | product1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |                       | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
