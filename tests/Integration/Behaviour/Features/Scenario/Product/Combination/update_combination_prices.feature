# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-prices
@reset-database-before-feature
@clear-cache-before-feature
@update-combination
@update-combination-prices
Feature: Update product combination prices in Back Office (BO)
  As an employee
  I need to be able to update product combination prices from BO

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

  Scenario: I update combination options:
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | is_virtual  | false             |
    And product product1 type should be standard
    And I generate combinations for product product1 using following attributes:
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
    And combination "product1SWhite" should have following prices:
      | combination price detail | value |
      | eco tax                  | 0     |
      | impact on price          | 0     |
      | impact on unit price     | 0     |
      | wholesale price          | 0     |
    When I update combination "product1SWhite" prices with following details:
      | eco tax              | 0.5 |
      | impact on price      | -5  |
      | impact on unit price | -1  |
      | wholesale price      | 20  |
    Then combination "product1SWhite" should have following prices:
      | combination price detail | value |
      | eco tax                  | 0.5   |
      | impact on price          | -5    |
      | impact on unit price     | -1    |
      | wholesale price          | 20    |
    When I update combination "product1SWhite" prices with following details:
      | impact on price | 0 |
    Then combination "product1SWhite" should have following prices:
      | combination price detail | value |
      | eco tax                  | 0.5   |
      | impact on price          | 0     |
      | impact on unit price     | -1    |
      | wholesale price          | 20    |
    When I update combination "product1SWhite" prices with following details:
      | eco tax              | 0 |
      | impact on price      | 0 |
      | impact on unit price | 0 |
      | wholesale price      | 0 |
    Then combination "product1SWhite" should have following prices:
      | combination price detail | value |
      | eco tax                  | 0     |
      | impact on price          | 0     |
      | impact on unit price     | 0     |
      | wholesale price          | 0     |
