# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags combinations-listing-multishop
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@product-multishop
@combinations-listing-multishop
Feature: List attribute combinations for product in Back Office (BO) of multiple shops
  As an employee
  I need to be able to see and manipulate a list of product attribute combinations in BO of multiple shops

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
    And shop "shop1" with name "test_shop" exists
    And I enable multishop feature
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I associate attribute group "Size" with shops "shop1,shop2"
    And I associate attribute group "Color" with shops "shop1,shop2"
    And I associate attribute "S" with shops "shop1,shop2"
    And I associate attribute "M" with shops "shop1,shop2"
    And I associate attribute "L" with shops "shop1,shop2"
    And I associate attribute "White" with shops "shop1,shop2"
    And I associate attribute "Black" with shops "shop1,shop2"
    And I associate attribute "Blue" with shops "shop1,shop2"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And I identify tax rules group named "US-AL Rate (4%)" as "us-al-tax-rate"
    And single shop context is loaded
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    When I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    When I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [L]                |
      | Color | [White,Black,Blue] |
    Then product "product1" should have the following combinations for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1LWhite"

  Scenario: I can paginate combinations and limit combinations per page in different shops
    Given product "product1" combinations list search criteria is set to defaults for shops "shop1,shop2"
    When I search product "product1" combinations list by following search criteria for shop "shop1":
      | criteria | value |
      | page     | 1     |
      | limit    | 5     |
    Then I should see following combinations in paginated list of product "product1" for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria | value |
      | page     | 1     |
      | limit    | 2     |
    Then I should see following combinations in paginated list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria for shop "shop1":
      | criteria | value |
      | page     | 2     |
      | limit    | 5     |
    Then I should see following combinations in paginated list of product "product1" for shops "shop1":
      | id reference  | combination name       | reference | attributes          | impact on price | quantity | is default |
      | product1MBlue | Size - M, Color - Blue |           | [Size:M,Color:Blue] | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria | value |
      | page     | 2     |
      | limit    | 2     |
    Then I should see following combinations in paginated list of product "product1" for shops "shop2":
      | id reference  | combination name       | reference | attributes          | impact on price | quantity | is default |
      | product1LBlue | Size - L, Color - Blue |           | [Size:L,Color:Blue] | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria | value |
      | page     | 3     |
      | limit    | 5     |
    Then combinations list of product "product1" should be empty for shops "shop2"

  Scenario: I can paginate and limit combination ids per page in different shops
    Given product "product1" combination ids search criteria is set to defaults for shops "shop1,shop2"
    When I search product "product1" combination ids by following search criteria for shop "shop1":
      | criteria | value |
      | page     | 1     |
      | limit    | 5     |
    Then I should see following paginated combination ids of product "product1" for shops "shop1":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
      | product1MWhite |
      | product1MBlack |
    When I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria | value |
      | page     | 1     |
      | limit    | 5     |
    Then I should see following paginated combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LWhite |
      | product1LBlack |
      | product1LBlue  |
    When I search product "product1" combination ids by following search criteria for shop "shop1":
      | criteria | value |
      | page     | 2     |
      | limit    | 5     |
    Then I should see following paginated combination ids of product "product1" for shops "shop1":
      | id reference  |
      | product1MBlue |
    When I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria | value |
      | page     | 2     |
      | limit    | 2     |
    Then I should see following paginated combination ids of product "product1" for shops "shop2":
      | id reference  |
      | product1LBlue |
    When I search product "product1" combination ids by following search criteria for shop "shop1":
      | criteria | value |
      | page     | 3     |
      | limit    | 5     |
    Then combination ids list of product "product1" should be empty for shops "shop1"
    When I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria | value |
      | page     | 2     |
      | limit    | 3     |
    Then combination ids list of product "product1" should be empty for shops "shop2"

  Scenario: I can filter combinations by attributes in different shops
    Given product "product1" combinations list search criteria is set to defaults for shops "shop1,shop2"
    When I search product "product1" combinations list by following search criteria for shop "shop1":
      | criteria         | value |
      | attributes[Size] | [S,M] |
    Then I should see following combinations in filtered list of product "product1" for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria         | value |
      | attributes[Size] | [S,M] |
    Then combinations list of product "product1" should be empty for shops "shop2"
    When I search product "product1" combinations list by following search criteria for shop "shop1":
      | criteria          | value        |
      | attributes[Size]  | [M]          |
      | attributes[Color] | [Blue,Black] |
    Then I should see following combinations in filtered list of product "product1" for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria          | value   |
      | attributes[Size]  | [L]     |
      | attributes[Color] | [White] |
    Then I should see following combinations in paginated list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |

  Scenario: I can filter combination ids by attributes in different shops
    Given product "product1" combination ids search criteria is set to defaults for shops "shop1,shop2"
    When I search product "product1" combination ids by following search criteria for shop "shop1":
      | criteria         | value |
      | attributes[Size] | [S,M] |
    Then I should see following filtered combination ids of product "product1" for shops "shop1":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
      | product1SBlue  |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |
    When I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria         | value |
      | attributes[Size] | [S,M] |
    Then combination ids list of product "product1" should be empty for shops "shop2"
    When I search product "product1" combination ids by following search criteria for shop "shop1":
      | criteria          | value        |
      | attributes[Size]  | [M]          |
      | attributes[Color] | [Blue,Black] |
    Then I should see following filtered combination ids of product "product1" for shops "shop1":
      | id reference   |
      | product1MBlack |
      | product1MBlue  |
    When I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria          | value   |
      | attributes[Size]  | [L]     |
      | attributes[Color] | [White] |
    Then I should see following filtered combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LWhite |

  Scenario: I can filter combinations by default combination in different shops
    Given product "product1" combinations list search criteria is set to defaults for shops "shop1,shop2"
    When I search product "product1" combinations list by following search criteria for shop "shop1":
      | criteria   | value |
      | is default | true  |
    Then I should see following combinations in filtered list of product "product1" for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria   | value |
      | is default | true  |
    Then I should see following combinations in filtered list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
    When I search product "product1" combinations list by following search criteria for shop "shop1":
      | criteria   | value |
      | is default | false |
    Then I should see following combinations in filtered list of product "product1" for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria   | value |
      | is default | false |
    Then I should see following combinations in filtered list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | false      |

  Scenario: I can filter combination ids by default combination in different shops
    Given product "product1" combination ids search criteria is set to defaults for shops "shop1,shop2"
    When I search product "product1" combination ids by following search criteria for shop "shop1":
      | criteria   | value |
      | is default | true  |
    Then I should see following filtered combination ids of product "product1" for shops "shop1":
      | id reference   |
      | product1SWhite |
    When I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria   | value |
      | is default | true  |
    Then I should see following filtered combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LWhite |
    When I search product "product1" combination ids by following search criteria for shop "shop1":
      | criteria   | value |
      | is default | false |
    Then I should see following filtered combination ids of product "product1" for shops "shop1":
      | id reference   |
      | product1SBlack |
      | product1SBlue  |
      | product1MWhite |
      | product1MBlack |
      | product1MBlue  |
    When I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria   | value |
      | is default | false |
    Then I should see following filtered combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LBlack |
      | product1LBlue  |


  Scenario: I can filter combinations and combination ids by reference in different shops
    Given product "product1" combinations list search criteria is set to defaults for shops "shop1,shop2"
    And I update combination "product1SWhite" with following values for shop "shop1":
      | reference | ABC |
    And I update combination "product1SBlue" with following values for shop "shop1":
      | reference | BBB |
    And I update combination "product1SBlack" with following values for shop "shop1":
      | reference | CCCD |
    And product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White | ABC       | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black | CCCD      | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  | BBB       | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria for shop "shop1":
      | criteria  | value |
      | reference | C     |
    Then I should see following combinations in filtered list of product "product1" for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White | ABC       | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black | CCCD      | [Size:S,Color:Black] | 0               | 0        | false      |
    And I search product "product1" combination ids by following search criteria for shop "shop1":
      | criteria  | value |
      | reference | C     |
    And I should see following filtered combination ids of product "product1" for shops "shop1":
      | id reference   |
      | product1SWhite |
      | product1SBlack |
    Given  I update combination "product1LWhite" with following values for shop "shop2":
      | reference | ABC |
    And I update combination "product1LBlue" with following values for shop "shop2":
      | reference | BBB |
    And I update combination "product1LBlack" with following values for shop "shop2":
      | reference | CCCD |
    And product "product1" should have the following combinations for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White | ABC       | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black | CCCD      | [Size:L,Color:Black] | 0               | 0        | false      |
      | product1LBlue  | Size - L, Color - Blue  | BBB       | [Size:L,Color:Blue]  | 0               | 0        | false      |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria  | value |
      | reference | C     |
    Then I should see following combinations in filtered list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White | ABC       | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black | CCCD      | [Size:L,Color:Black] | 0               | 0        | false      |
    When I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria  | value |
      | reference | C     |
    Then I should see following filtered combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LWhite |
      | product1LBlack |

  Scenario: I can sort combinations and combination ids by reference, quantity, impact on price in different shops
    Given product "product1" combinations list search criteria is set to defaults for shops "shop1,shop2"
    And I update combination "product1LWhite" with following values for shop "shop2":
      | impact on price | -1  |
    And I update combination "product1LWhite" stock for shop "shop2" with following details:
      | delta quantity  | 10  |
    And I update combination "product1LWhite" with following values for shop "shop2":
      | reference       | AAA |
    And I update combination "product1LBlue" with following values for shop "shop2":
      | impact on price | 1   |
    And I update combination "product1LBlue" stock for shop "shop2" with following details:
      | delta quantity  | 100 |
    And I update combination "product1LBlue" with following values for shop "shop2":
      | reference       | BBB |
    And I update combination "product1LBlack" with following values for shop "shop2":
      | impact on price | 10   |
    And I update combination "product1LBlack" stock for shop "shop2" with following details:
      | delta quantity  | 50 |
    And I update combination "product1LBlack" with following values for shop "shop2":
      | reference       | CCC |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria  | value           |
      | order by  | impact on price |
      | order way | asc             |
    Then I should see following combinations in paginated list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White | AAA       | [Size:L,Color:White] | -1              | 10       | true       |
      | product1LBlue  | Size - L, Color - Blue  | BBB       | [Size:L,Color:Blue]  | 1               | 100      | false      |
      | product1LBlack | Size - L, Color - Black | CCC       | [Size:L,Color:Black] | 10              | 50       | false      |
    And I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria  | value           |
      | order by  | impact on price |
      | order way | asc             |
    And I should see following paginated combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LWhite |
      | product1LBlue  |
      | product1LBlack |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria  | value     |
      | order by  | reference |
      | order way | asc       |
    Then I should see following combinations in paginated list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White | AAA       | [Size:L,Color:White] | -1              | 10       | true       |
      | product1LBlue  | Size - L, Color - Blue  | BBB       | [Size:L,Color:Blue]  | 1               | 100      | false      |
      | product1LBlack | Size - L, Color - Black | CCC       | [Size:L,Color:Black] | 10              | 50       | false      |
    And I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria  | value     |
      | order by  | reference |
      | order way | asc       |
    And I should see following filtered combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LWhite |
      | product1LBlue  |
      | product1LBlack |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria  | value     |
      | order by  | reference |
      | order way | desc      |
      | page      | 1         |
      | limit     | 2         |
    Then I should see following combinations in paginated list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LBlack | Size - L, Color - Black | CCC       | [Size:L,Color:Black] | 10              | 50       | false      |
      | product1LBlue  | Size - L, Color - Blue  | BBB       | [Size:L,Color:Blue]  | 1               | 100      | false      |
    And I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria  | value     |
      | order by  | reference |
      | order way | desc      |
      | page      | 1         |
      | limit     | 2         |
    And I should see following filtered combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LBlack |
      | product1LBlue  |
      # This sorts by StockAvailable::quantity
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria  | value    |
      | order by  | quantity |
      | order way | desc     |
    Then I should see following combinations in paginated list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LBlue  | Size - L, Color - Blue  | BBB       | [Size:L,Color:Blue]  | 1               | 100      | false      |
      | product1LBlack | Size - L, Color - Black | CCC       | [Size:L,Color:Black] | 10              | 50       | false      |
      | product1LWhite | Size - L, Color - White | AAA       | [Size:L,Color:White] | -1              | 10       | true       |
    And I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria  | value    |
      | order by  | quantity |
      | order way | desc     |
    And I should see following filtered combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LBlue  |
      | product1LBlack |
      | product1LWhite |
    When I search product "product1" combinations list by following search criteria for shop "shop2":
      | criteria  | value    |
      | order by  | quantity |
      | order way | asc      |
    Then I should see following combinations in paginated list of product "product1" for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White | AAA       | [Size:L,Color:White] | -1              | 10       | true       |
      | product1LBlack | Size - L, Color - Black | CCC       | [Size:L,Color:Black] | 10              | 50       | false      |
      | product1LBlue  | Size - L, Color - Blue  | BBB       | [Size:L,Color:Blue]  | 1               | 100      | false      |
    And I search product "product1" combination ids by following search criteria for shop "shop2":
      | criteria  | value    |
      | order by  | quantity |
      | order way | asc      |
    And I should see following filtered combination ids of product "product1" for shops "shop2":
      | id reference   |
      | product1LWhite |
      | product1LBlack |
      | product1LBlue  |
