# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags generate-combinations
@reset-database-before-feature
@clear-cache-before-feature
@generate-combinations
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

  Scenario: Generate product combinations
    When I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | is_virtual  | false             |
    Then product product1 type should be standard
    When I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product product1 should have following list of combinations:
      | reference      | combination name        | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
      | product1Blue   | Size - S, Color - Blue  | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |

  Scenario: Product combinations pagination returns correct results
    Given product product1 should have following list of combinations:
      | reference      | combination name        | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
      | product1Blue   | Size - S, Color - Blue  | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
    Then I should see following combinations of product product1 in page 1 limited to maximum 2 per page:
      | reference      | combination name        | attributes           | impact on price | final price | quantity | is default |
      | product1SWhite | Size - S, Color - White | [Size:S,Color:White] | 0               | 0           | 0        | true       |
      | product1SBlack | Size - S, Color - Black | [Size:S,Color:Black] | 0               | 0           | 0        | false      |
    And I should see following combinations of product product1 in page 2 limited to maximum 2 per page:
      | reference      | combination name        | attributes           | impact on price | final price | quantity | is default |
      | product1Blue   | Size - S, Color - Blue  | [Size:S,Color:Blue]  | 0               | 0           | 0        | false      |
      | product1MWhite | Size - M, Color - White | [Size:M,Color:White] | 0               | 0           | 0        | false      |
    And I should see following combinations of product product1 in page 3 limited to maximum 2 per page:
      | reference      | combination name        | attributes           | impact on price | final price | quantity | is default |
      | product1MBlack | Size - M, Color - Black | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
    And there should be no combinations of product1 in page 4 when limited to maximum 2 per page
    And I should see following combinations of product product1 in page 2 limited to maximum 3 per page:
      | reference      | combination name        | attributes           | impact on price | final price | quantity | is default |
      | product1MWhite | Size - M, Color - White | [Size:M,Color:White] | 0               | 0           | 0        | false      |
      | product1MBlack | Size - M, Color - Black | [Size:M,Color:Black] | 0               | 0           | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  | [Size:M,Color:Blue]  | 0               | 0           | 0        | false      |
    And there should be no combinations of product1 in page 3 when limited to maximum 3 per page
