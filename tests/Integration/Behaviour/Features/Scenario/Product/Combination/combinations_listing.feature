# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags combinations-listing
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@combinations-listing
Feature: List attribute combinations for product in Back Office (BO)
  As an employee
  I need to be able to see and manipulate a list of product attribute combinations from BO

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
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And product product1 does not have a default combination
    And I generate combinations for product product1 using following attributes:
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
    And product "product1" should have the following combination ids:
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |

  Scenario: I can paginate combinations and limit combinations per page
    Given product "product1" combinations list search criteria is set to defaults
    When I search product "product1" combinations list by following search criteria:
      | criteria | value |
      | page     | 1     |
      | limit    | 5     |
    Then I should see following combinations in paginated list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria | value |
      | page     | 2     |
      | limit    | 5     |
    Then I should see following combinations in paginated list of product "product1":
      | id reference  | combination name       | reference | attributes          | impact on price | quantity | is default |
      | product1MBlue | Size - M, Color - Blue |           | [Size:M,Color:Blue] | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria | value |
      | page     | 3     |
      | limit    | 5     |
    Then combinations list of product "product1" should be empty

  Scenario: I can paginate and limit combination ids per page
    Given product "product1" combination ids search criteria is set to defaults
    When I search product "product1" combination ids by following search criteria:
      | criteria | value |
      | page     | 1     |
      | limit    | 5     |
    Then I should see following paginated combination ids of product "product1":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
      | product1MWhite |
      | product1MBlack |
    When I search product "product1" combination ids by following search criteria:
      | criteria | value |
      | page     | 2     |
      | limit    | 5     |
    Then I should see following paginated combination ids of product "product1":
      | id reference  |
      | product1MBlue |
    When I search product "product1" combination ids by following search criteria:
      | criteria | value |
      | page     | 3     |
      | limit    | 5     |
    Then combination ids list of product "product1" should be empty

  Scenario: I can filter combinations by attributes
    Given product "product1" combinations list search criteria is set to defaults
    When I search product "product1" combinations list by following search criteria:
      | criteria         | value |
      | attributes[Size] | [S,M] |
    Then I should see following combinations in filtered list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria         | value |
      | attributes[Size] | [S]   |
    Then I should see following combinations in filtered list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria          | value  |
      | attributes[Color] | [Blue] |
    Then I should see following combinations in filtered list of product "product1":
      | id reference  | combination name       | reference | attributes          | impact on price | quantity | is default |
      | product1SBlue | Size - S, Color - Blue |           | [Size:S,Color:Blue] | 0               | 0        | false      |
      | product1MBlue | Size - M, Color - Blue |           | [Size:M,Color:Blue] | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria          | value  |
      | attributes[Size]  | [M]    |
      | attributes[Color] | [Blue] |
    Then I should see following combinations in filtered list of product "product1":
      | id reference  | combination name       | reference | attributes          | impact on price | quantity | is default |
      | product1MBlue | Size - M, Color - Blue |           | [Size:M,Color:Blue] | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria          | value  |
      | attributes[Size]  | [M]    |
      | attributes[Color] | [Blue] |
    Then I should see following combinations in filtered list of product "product1":
      | id reference  | combination name       | reference | attributes          | impact on price | quantity | is default |
      | product1MBlue | Size - M, Color - Blue |           | [Size:M,Color:Blue] | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria          | value        |
      | attributes[Size]  | [M]          |
      | attributes[Color] | [Blue,Black] |
    Then I should see following combinations in filtered list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |

  Scenario: I can filter combination ids by attributes
    Given product "product1" combination ids search criteria is set to defaults
    When I search product "product1" combination ids by following search criteria:
      | criteria         | value |
      | attributes[Size] | [S,M] |
    Then I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |
    When I search product "product1" combination ids by following search criteria:
      | criteria         | value |
      | attributes[Size] | [S]   |
    Then I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
    When I search product "product1" combination ids by following search criteria:
      | criteria          | value  |
      | attributes[Color] | [Blue] |
    Then I should see following filtered combination ids of product "product1":
      | id reference  |
      | product1SBlue |
      | product1MBlue |
    When I search product "product1" combination ids by following search criteria:
      | criteria          | value  |
      | attributes[Size]  | [M]    |
      | attributes[Color] | [Blue] |
    Then I should see following filtered combination ids of product "product1":
      | id reference  |
      | product1MBlue |
    When I search product "product1" combination ids by following search criteria:
      | criteria          | value  |
      | attributes[Size]  | [M]    |
      | attributes[Color] | [Blue] |
    Then I should see following filtered combination ids of product "product1":
      | id reference  |
      | product1MBlue |
    When I search product "product1" combination ids by following search criteria:
      | criteria          | value        |
      | attributes[Size]  | [M]          |
      | attributes[Color] | [Blue,Black] |
    Then I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1MBlack |
      | product1MBlue  |

  Scenario: I can filter combinations by default combination
    Given product "product1" combinations list search criteria is set to defaults
    When I search product "product1" combinations list by following search criteria:
      | criteria   | value |
      | is default | true  |
    Then I should see following combinations in filtered list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
    When I search product "product1" combinations list by following search criteria:
      | criteria   | value |
      | is default | false |
    Then I should see following combinations in filtered list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |

  Scenario: I can filter combination ids by default combination
    Given product "product1" combination ids search criteria is set to defaults
    When I search product "product1" combination ids by following search criteria:
      | criteria   | value |
      | is default | true  |
    Then I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1SWhite |
    When I search product "product1" combination ids by following search criteria:
      | criteria   | value |
      | is default | false |
    Then I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1SBlack |
      | product1SBlue  |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |

  Scenario: I can sort combinations and combination ids by reference, quantity, impact on price
    Given product "product1" combinations list search criteria is set to defaults
    And I update combination "product1SWhite" with following values:
      | impact on price | -1  |
    And I update combination "product1SWhite" stock with following details:
      | delta quantity  | 10  |
    And I update combination "product1SWhite" with following values:
      | reference       | AAA |
    And I update combination "product1SBlue" with following values:
      | impact on price | 1   |
    And I update combination "product1SBlue" stock with following details:
      | delta quantity  | 100 |
    And I update combination "product1SBlue" with following values:
      | reference       | BBB |
    And I update combination "product1SBlack" with following values:
      | impact on price | 10   |
    And I update combination "product1SBlack" stock with following details:
      | delta quantity  | 50 |
    And I update combination "product1SBlack" with following values:
      | reference       | CCC |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value           |
      | order by  | impact on price |
      | order way | asc             |
    Then I should see following combinations in paginated list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White | AAA       | [Size:S,Color:White] | -1              | 10       | true       |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  | BBB       | [Size:S,Color:Blue]  | 1               | 100      | false      |
      | product1SBlack | Size - S, Color - Black | CCC       | [Size:S,Color:Black] | 10              | 50       | false      |
    And I search product "product1" combination ids by following search criteria:
      | criteria  | value           |
      | order by  | impact on price |
      | order way | asc             |
    And I should see following paginated combination ids of product "product1":
      | id reference   |
      | product1SWhite |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |
      | product1SBlue  |
      | product1SBlack |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value     |
      | order by  | reference |
      | order way | asc       |
    Then I should see following combinations in paginated list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
      | product1SWhite | Size - S, Color - White | AAA       | [Size:S,Color:White] | -1              | 10       | true       |
      | product1SBlue  | Size - S, Color - Blue  | BBB       | [Size:S,Color:Blue]  | 1               | 100      | false      |
      | product1SBlack | Size - S, Color - Black | CCC       | [Size:S,Color:Black] | 10              | 50       | false      |
    And I search product "product1" combination ids by following search criteria:
      | criteria  | value     |
      | order by  | reference |
      | order way | asc       |
    And I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |
      | product1SWhite |
      | product1SBlue  |
      | product1SBlack |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value     |
      | order by  | reference |
      | order way | desc      |
      | page      | 1         |
      | limit     | 3         |
    Then I should see following combinations in paginated list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black | CCC       | [Size:S,Color:Black] | 10              | 50       | false      |
      | product1SBlue  | Size - S, Color - Blue  | BBB       | [Size:S,Color:Blue]  | 1               | 100      | false      |
      | product1SWhite | Size - S, Color - White | AAA       | [Size:S,Color:White] | -1              | 10       | true       |
    And I search product "product1" combination ids by following search criteria:
      | criteria  | value     |
      | order by  | reference |
      | order way | desc      |
      | page      | 1         |
      | limit     | 3         |
    And I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1SBlack |
      | product1SBlue  |
      | product1SWhite |
      # This sorts by StockAvailable::quantity
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value    |
      | order by  | quantity |
      | order way | desc     |
    Then I should see following combinations in paginated list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlue  | Size - S, Color - Blue  | BBB       | [Size:S,Color:Blue]  | 1               | 100      | false      |
      | product1SBlack | Size - S, Color - Black | CCC       | [Size:S,Color:Black] | 10              | 50       | false      |
      | product1SWhite | Size - S, Color - White | AAA       | [Size:S,Color:White] | -1              | 10       | true       |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And I search product "product1" combination ids by following search criteria:
      | criteria  | value    |
      | order by  | quantity |
      | order way | desc     |
    And I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1SBlue  |
      | product1SBlack |
      | product1SWhite |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value    |
      | order by  | quantity |
      | order way | asc      |
    Then I should see following combinations in paginated list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
      | product1SWhite | Size - S, Color - White | AAA       | [Size:S,Color:White] | -1              | 10       | true       |
      | product1SBlack | Size - S, Color - Black | CCC       | [Size:S,Color:Black] | 10              | 50       | false      |
      | product1SBlue  | Size - S, Color - Blue  | BBB       | [Size:S,Color:Blue]  | 1               | 100      | false      |
    And I search product "product1" combination ids by following search criteria:
      | criteria  | value    |
      | order by  | quantity |
      | order way | asc      |
    And I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |

  Scenario: I can filter combinations and combination ids by reference
    Given product "product1" combinations list search criteria is set to defaults
    And I update combination "product1SWhite" with following values:
      | reference | ABC |
    And I update combination "product1SBlue" with following values:
      | reference | BBB |
    And I update combination "product1SBlack" with following values:
      | reference | CCCD |
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White | ABC       | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black | CCCD      | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  | BBB       | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value |
      | reference | C     |
    Then I should see following combinations in filtered list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White | ABC       | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black | CCCD      | [Size:S,Color:Black] | 0               | 0        | false      |
    And I search product "product1" combination ids by following search criteria:
      | criteria  | value |
      | reference | C     |
    And I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value |
      | reference | b     |
    Then I should see following combinations in filtered list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White | ABC       | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlue  | Size - S, Color - Blue  | BBB       | [Size:S,Color:Blue]  | 0               | 0        | false      |
    And I search product "product1" combination ids by following search criteria:
      | criteria  | value |
      | reference | b     |
    And I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1SWhite |
      | product1SBlue  |
    When I search product "product1" combinations list by following search criteria:
      | criteria  | value |
      | reference | cD    |
    Then I should see following combinations in filtered list of product "product1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black | CCCD      | [Size:S,Color:Black] | 0               | 0        | false      |
    And I search product "product1" combination ids by following search criteria:
      | criteria  | value |
      | reference | cD    |
    And I should see following filtered combination ids of product "product1":
      | id reference   |
      | product1SBlack |
