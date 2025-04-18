# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags search-product-combinations
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@search-product-combinations
Feature: Search attribute combinations for product in Back Office (BO) of multiple shops
  As an employee
  I need to be able to see search product attribute combinations in BO of multiple shops

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
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And I associate attribute group "Size" with shops "shop1,shop2"
    And I associate attribute group "Color" with shops "shop1,shop2"
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
      | Color | [White,Black,Blue] |
    Then product "product1" should have the following combinations for shops "shop2":
      | id reference  | combination name | reference | attributes    | impact on price | quantity | is default |
      | product1White | Color - White    |           | [Color:White] | 0               | 0        | true       |
      | product1Black | Color - Black    |           | [Color:Black] | 0               | 0        | false      |
      | product1Blue  | Color - Blue     |           | [Color:Blue]  | 0               | 0        | false      |

  Scenario: Search combinations by attributes
    When I list product "product1" combinations in language "en" for shop "shop1" limited to "10" results I should see following results:
      | id reference   | combination name        |
      | product1SWhite | Size - S, Color - White |
      | product1SBlack | Size - S, Color - Black |
      | product1SBlue  | Size - S, Color - Blue  |
      | product1MWhite | Size - M, Color - White |
      | product1MBlack | Size - M, Color - Black |
      | product1MBlue  | Size - M, Color - Blue  |
    When I search product "product1" combinations by phrase "b" in language "en" for shop "shop2" limited to "20" results I should see following results:
      | id reference  | combination name |
      | product1Black | Color - Black    |
      | product1Blue  | Color - Blue     |
    When I search product "product1" combinations by phrase "white" in language "en" for shop "shop2" limited to "20" results I should see following results:
      | id reference  | combination name |
      | product1White | Color - White    |
    When I search product "product1" combinations by phrase "white" in language "en" for shop "shop1" limited to "20" results I should see following results:
      | id reference   | combination name        |
      | product1SWhite | Size - S, Color - White |
      | product1MWhite | Size - M, Color - White |
    When I search product "product1" combinations by phrase "b" in language "en" for all shops limited to "20" results I should see following results:
      | id reference   | combination name        |
      | product1SBlack | Size - S, Color - Black |
      | product1SBlue  | Size - S, Color - Blue  |
      | product1MBlack | Size - M, Color - Black |
      | product1MBlue  | Size - M, Color - Blue  |
      | product1Black  | Color - Black           |
      | product1Blue   | Color - Blue            |
    When I search product "product1" combinations by phrase "b" in language "en" for all shops limited to "3" results I should see following results:
      | id reference   | combination name        |
      | product1SBlack | Size - S, Color - Black |
      | product1SBlue  | Size - S, Color - Blue  |
      | product1MBlack | Size - M, Color - Black |
    When I search product "product1" combinations by phrase "white" in language "en" for all shops limited to "20" results I should see following results:
      | id reference   | combination name        |
      | product1SWhite | Size - S, Color - White |
      | product1MWhite | Size - M, Color - White |
      | product1White  | Color - White           |

  Scenario: Search combinations by attribute groups
    And I associate attribute group "Color" with shops "shop1"
    When I search product "product1" combinations by phrase "color" in language "en" for shop "shop1" limited to "20" results I should see following results:
      | id reference   | combination name        |
      | product1SWhite | Size - S, Color - White |
      | product1SBlack | Size - S, Color - Black |
      | product1SBlue  | Size - S, Color - Blue  |
      | product1MWhite | Size - M, Color - White |
      | product1MBlack | Size - M, Color - Black |
      | product1MBlue  | Size - M, Color - Blue  |
    When I search product "product1" combinations by phrase "color" in language "en" for all shops limited to "20" results I should see following results:
      | id reference   | combination name        |
      | product1SWhite | Size - S, Color - White |
      | product1SBlack | Size - S, Color - Black |
      | product1SBlue  | Size - S, Color - Blue  |
      | product1MWhite | Size - M, Color - White |
      | product1MBlack | Size - M, Color - Black |
      | product1MBlue  | Size - M, Color - Blue  |
      | product1White  | Color - White           |
      | product1Black  | Color - Black           |
      | product1Blue   | Color - Blue            |
    When I search product "product1" combinations by phrase "size" in language "en" for shop "shop1" limited to "20" results I should see following results:
      | id reference   | combination name        |
      | product1SWhite | Size - S, Color - White |
      | product1SBlack | Size - S, Color - Black |
      | product1SBlue  | Size - S, Color - Blue  |
      | product1MWhite | Size - M, Color - White |
      | product1MBlack | Size - M, Color - Black |
      | product1MBlue  | Size - M, Color - Blue  |
    When I search product "product1" combinations by phrase "size" in language "en" for all shops limited to "20" results I should see following results:
      | id reference   | combination name        |
      | product1SWhite | Size - S, Color - White |
      | product1SBlack | Size - S, Color - Black |
      | product1SBlue  | Size - S, Color - Blue  |
      | product1MWhite | Size - M, Color - White |
      | product1MBlack | Size - M, Color - Black |
      | product1MBlue  | Size - M, Color - Blue  |
    When I search product "product1" combinations by phrase "size" in language "en" for shop "shop2" limited to "20" results I should see following results:
      | id reference   | combination name        |

  Scenario: Search for combinations by attribute groups which doesn't exist in shop should not find any combinations
    Given I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop3 |
    And attribute group "Color" is not associated to shops "shop3"
    When I search product "product1" combinations by phrase "Color" in language "en" for shop "shop3" limited to "20" results I should see following results:
      | id reference   | combination name        |

  Scenario: Search for combinations by attribute which doesn't exist in shop should not find any combinations
    Given I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop4 |
    And attribute "White" is not associated to shops "shop4"
    When I search product "product1" combinations by phrase "White" in language "en" for shop "shop4" limited to "20" results I should see following results:
      | id reference   | combination name        |

