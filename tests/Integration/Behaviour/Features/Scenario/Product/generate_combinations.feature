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
    Then product product1 should have following combinations:
      | combination name        | attributes           |
      | Size - S, Color - White | [Size:S,Color:White] |
      | Size - S, Color - Black | [Size:S,Color:Black] |
      | Size - S, Color - Blue  | [Size:S,Color:Blue]  |
      | Size - M, Color - White | [Size:M,Color:White] |
      | Size - M, Color - Black | [Size:M,Color:Black] |
      | Size - M, Color - Blue  | [Size:M,Color:Blue]  |

  Scenario: Product combinations pagination returns correct results
    Given product product1 should have following combinations:
      | combination name        | attributes           |
      | Size - S, Color - White | [Size:S,Color:White] |
      | Size - S, Color - Black | [Size:S,Color:Black] |
      | Size - S, Color - Blue  | [Size:S,Color:Blue]  |
      | Size - M, Color - White | [Size:M,Color:White] |
      | Size - M, Color - Black | [Size:M,Color:Black] |
      | Size - M, Color - Blue  | [Size:M,Color:Blue]  |
    When I limit product product1 combinations per page to 2
    Then I should see following combinations of product product1 in page 1:
      | combination name        | attributes           |
      | Size - S, Color - White | [Size:S,Color:White] |
      | Size - S, Color - Black | [Size:S,Color:Black] |
    And I should see following combinations of product product1 in page 2:
      | combination name        | attributes           |
      | Size - S, Color - Blue  | [Size:S,Color:Blue]  |
      | Size - M, Color - White | [Size:M,Color:White] |
    And I should see following combinations of product product1 in page 3:
      | combination name        | attributes           |
      | Size - M, Color - Black | [Size:M,Color:Black] |
      | Size - M, Color - Blue  | [Size:M,Color:Blue]  |
    And there should be no combinations of product1 in page 4
