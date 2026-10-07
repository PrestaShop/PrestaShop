# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-status
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@update-combination-status
Feature: Enable and disable combinations from Back Office (BO)
  As an employee
  I need to be able to disable a combination without deleting it

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And I generate combinations for product product1 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |

  Scenario: Combinations are enabled by default
    Then combination "product1SWhite" should be enabled
    And combination "product1SBlack" should be enabled
    And combination "product1MWhite" should be enabled
    And combination "product1MBlack" should be enabled

  Scenario: Disable and enable a combination which is not the default one
    When I update combination "product1SBlack" with following values:
      | active | false |
    Then combination "product1SBlack" should be disabled
    And product product1 default combination should be "product1SWhite"
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default | active |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       | true   |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      | false  |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      | true   |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      | true   |
    When I update combination "product1SBlack" with following values:
      | active | true |
    Then combination "product1SBlack" should be enabled
    And product product1 default combination should be "product1SWhite"

  Scenario: Disabling the default combination moves the default to the first active combination
    When I update combination "product1SBlack" with following values:
      | active | false |
    And I update combination "product1SWhite" with following values:
      | active | false |
    Then combination "product1SWhite" should be disabled
    And product product1 default combination should be "product1MWhite"
    When I update combination "product1MWhite" with following values:
      | active | false |
    Then product product1 default combination should be "product1MBlack"

  Scenario: The default combination stays in place when no other combination is active
    When I update combination "product1SWhite" with following values:
      | active | false |
    And I update combination "product1SBlack" with following values:
      | active | false |
    And I update combination "product1MWhite" with following values:
      | active | false |
    Then product product1 default combination should be "product1MBlack"
    When I update combination "product1MBlack" with following values:
      | active | false |
    Then combination "product1MBlack" should be disabled
    And product product1 default combination should be "product1MBlack"

  Scenario: Deleting the default combination prefers an active combination as the new default
    When I update combination "product1SBlack" with following values:
      | active | false |
    And I set combination "product1MWhite" as default
    And I update combination "product1SWhite" with following values:
      | active | false |
    Then product product1 default combination should be "product1MWhite"
    When I delete combination product1MWhite
    Then product product1 default combination should be "product1MBlack"

  Scenario: Setting a combination as default while disabling it keeps the explicit choice
    When I update combination "product1SBlack" with following values:
      | is default | true  |
      | active     | false |
    Then combination "product1SBlack" should be disabled
    And product product1 default combination should be "product1SBlack"
