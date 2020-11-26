# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination
@reset-database-before-feature
@clear-cache-before-feature
@update-combination
Feature: Update product combinations in Back Office (BO)
  As an employee
  I need to be able to update product combinations from BO

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
    And product product1 should have following combinations:
      | reference      | combination name        |
      | product1SWhite | Size - S, Color - White |
      | product1SBlack | Size - S, Color - Black |
      | product1SBlue  | Size - S, Color - Blue  |
      | product1MWhite | Size - M, Color - White |
      | product1MBlack | Size - M, Color - Black |
      | product1MBlue  | Size - M, Color - Blue  |
    And combination "product1SWhite" should have following attributes information:
      | attribute group | attribute |
      | Size            | S         |
      | Color           | White     |
    And combination "product1SWhite" should have following options:
      | ean13     |  |
      | isbn      |  |
      | mpn       |  |
      | reference |  |
      | upc       |  |
    When I update combination "product1SWhite" options with following details:
      | ean13     | 978020137962      |
      | isbn      | 978-3-16-148410-0 |
      | mpn       | mpn1              |
      | reference | ref1              |
      | upc       | 72527273070       |
    Then combination "product1SWhite" should have following options:
      | ean13     | 978020137962      |
      | isbn      | 978-3-16-148410-0 |
      | mpn       | mpn1              |
      | reference | ref1              |
      | upc       | 72527273070       |
