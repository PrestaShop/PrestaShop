# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-from-listing
@reset-database-before-feature
@clear-cache-before-feature
@product-combination
@update-combination-from-listing
Feature: Update product combination from listing in Back Office (BO)
  As an employee
  I need to be able to update product combination from listing in BO

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

  Scenario: I update combination from listing:
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And product product1 does not have a default combination
    And I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product product1 should have following prices information:
      | price | 0 |
    And I update product "product1" prices with following information:
      | price | 100.99 |
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product product1 default combination should be "product1SWhite"
    When I update combination "product1SBlue" from list with following values:
      | impact on price | 5      |
      | quantity        | 10     |
      | is default      | true   |
      | reference       | test_1 |
    And I update combination "product1MWhite" from list with following values:
      | impact on price | -4.99  |
      | quantity        | 9      |
      | is default      | false  |
      | reference       | test 2 |
    And I update combination "product1MBlack" from list with following values:
      | quantity | 50 |
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | false      |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  | test_1    | [Size:S,Color:Blue]  | 5               | 10       | true       |
      | product1MWhite | Size - M, Color - White | test 2    | [Size:M,Color:White] | -4.99           | 9        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 50       | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product product1 default combination should be "product1SBlue"
