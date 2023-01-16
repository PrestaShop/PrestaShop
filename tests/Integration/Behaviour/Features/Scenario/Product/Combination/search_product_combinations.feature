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
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And I identify tax rules group named "US-AL Rate (4%)" as "us-al-tax-rate"
    And single shop context is loaded
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I copy product product1 from shop shop1 to shop shop2
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
    # @todo: doesn't work. Apparently fixture attributes are only generated in one shop. That raises questions like -
    # how did it work in all other scenarios? - We are missing some parts, because it shouldn't be possible to generate
    # combination for certain shop if it is missing the required attributes in attribute_shop table.
    When I search product "product1" combinations by phrase "color" in language "en" for shop "shop2" limited to "2" results I should see following results:
      | id reference  | combination name |
      | product1White | Color - White    |
      | product1Black | Color - Black    |
      | product1Blue  | Color - Blue     |
