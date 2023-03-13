# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-details-multishop
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@update-combination-details-multishop
Feature: Update product combination details in Back Office (BO) in multi shop context
  As an employee
  I need to be able to update product combination details from BO in multiple shops

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
    And I associate attribute "Red" with shops "shop1,shop2"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have no combinations for shops "shop2"
    And I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have the following combinations for shops "shop1,shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"
    And combinations "product1SWhite,product1SBlack,product1SBlue,product1MWhite,product1MBlack,product1MBlue" are associated to shop "shop1"
    And combinations "product1SWhite,product1SBlack,product1SBlue,product1MWhite,product1MBlack,product1MBlue" are associated to shop "shop2"

  Scenario: I update combination details for specific shop:
    And combination "product1SWhite" should have following details for shops "shop1,shop2":
      | combination detail | value |
      | ean13              |       |
      | isbn               |       |
      | mpn                |       |
      | reference          |       |
      | upc                |       |
      | impact on weight   | 0     |
      # details are shared between shops.
      # doesn't matter which shop we provide, they are updated in general product_attribute table
      # except the impact_on_weight
    When I update combination "product1SWhite" with following values for shop "shop1":
      | ean13            | 978020137962      |
      | isbn             | 978-3-16-148410-0 |
      | mpn              | mpn1              |
      | reference        | ref1              |
      | upc              | 72527273070       |
      | impact on weight | 17.25             |
    Then combination "product1SWhite" should have following details for shops "shop1":
      | combination detail | value             |
      | ean13              | 978020137962      |
      | isbn               | 978-3-16-148410-0 |
      | mpn                | mpn1              |
      | reference          | ref1              |
      | upc                | 72527273070       |
      | impact on weight   | 17.25             |
    And combination "product1SWhite" should have following details for shops "shop2":
      | combination detail | value             |
      | ean13              | 978020137962      |
      | isbn               | 978-3-16-148410-0 |
      | mpn                | mpn1              |
      | reference          | ref1              |
      | upc                | 72527273070       |
      | impact on weight   | 0                 |
    When I update combination "product1SWhite" with following values for shop "shop2":
      | ean13            |      |
      | isbn             |      |
      | mpn              |      |
      | reference        | ref2 |
      | upc              |      |
      | impact on weight | 7    |
    Then combination "product1SWhite" should have following details for shops "shop2":
      | combination detail | value |
      | ean13              |       |
      | isbn               |       |
      | mpn                |       |
      | reference          | ref2  |
      | upc                |       |
      | impact on weight   | 7     |
    Then combination "product1SWhite" should have following details for shops "shop1":
      | combination detail | value |
      | ean13              |       |
      | isbn               |       |
      | mpn                |       |
      | reference          | ref2  |
      | upc                |       |
      | impact on weight   | 17.25 |
    And combinations "product1SWhite" are not associated to shop "shop3"
    And combinations "product1SWhite" are not associated to shop "shop4"

  Scenario: I update combination details for all shops:
    And combination "product1SBlack" should have following details for shops "shop1,shop2":
      | combination detail | value |
      | ean13              |       |
      | isbn               |       |
      | mpn                |       |
      | reference          |       |
      | upc                |       |
      | impact on weight   | 0     |
      # details are shared between shops.
      # doesn't matter which shop we provide, they are updated in general product_attribute table
      # except the impact_on_weight
    When I update combination "product1SBlack" with following values for all shops:
      | ean13            | 978020137962      |
      | isbn             | 978-3-16-148410-0 |
      | mpn              | mpn1              |
      | reference        | ref1              |
      | upc              | 72527273070       |
      | impact on weight | 17.25             |
    Then combination "product1SBlack" should have following details for shops "shop1,shop2":
      | combination detail | value             |
      | ean13              | 978020137962      |
      | isbn               | 978-3-16-148410-0 |
      | mpn                | mpn1              |
      | reference          | ref1              |
      | upc                | 72527273070       |
      | impact on weight   | 17.25             |
    And combinations "product1SBlack" are not associated to shop "shop3"
    And combinations "product1SBlack" are not associated to shop "shop4"
