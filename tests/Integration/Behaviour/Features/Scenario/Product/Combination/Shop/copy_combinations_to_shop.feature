# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags copy-combinations-to-shop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-combination
@product-multishop
@copy-combinations-to-shop
Feature: Copy combinations from Back Office (BO) when using multi-shop feature
  As a BO user
  I need to be able to copy product combinations from BO from one shop to another when using multi-shop feature

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
    And product "product1" default combination for shop "shop1" should be "product1SWhite"

  Scenario: I copy product from a shop to another the combinations are associated to the other shop along with the default
    Given product product1 is not associated to shop "shop2"
    And product "product1" should not have a default combination for shop "shop2"
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product "product1" should have the following combinations for shops "shop1,shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"

  Scenario: I copy updated combinations to another shop the data is also copied
    Given I update combination "product1SWhite" with following values for shop "shop1":
      | ean13                      | 978020137962      |
      | isbn                       | 978-3-16-148410-0 |
      | mpn                        | mpn1              |
      | reference                  | ref1              |
      | upc                        | 72527273070       |
      | impact on weight           | 17.25             |
      | eco tax                    | 0.5               |
      | impact on price            | 10.0              |
      | impact on unit price       | 0.5               |
      | wholesale price            | 20                |
      | minimal quantity           | 10                |
      | low stock threshold        | 10                |
      | low stock alert is enabled | true              |
      | available date             | 2021-10-10        |
    And I update combination "product1SWhite" stock for shop "shop1" with following details:
      | delta quantity | 10          |
      | location       | Storage nr1 |
    Then combination "product1SWhite" should have following details for shops "shop1":
      | combination detail         | value             |
      | ean13                      | 978020137962      |
      | isbn                       | 978-3-16-148410-0 |
      | mpn                        | mpn1              |
      | reference                  | ref1              |
      | upc                        | 72527273070       |
      | impact on weight           | 17.25             |
      | eco tax                    | 0.5               |
      | impact on price            | 10.0              |
      | impact on unit price       | 0.5               |
      | wholesale price            | 20                |
      | minimal quantity           | 10                |
      | low stock threshold        | 10                |
      | low stock alert is enabled | true              |
      | available date             | 2021-10-10        |
    And combination "product1SWhite" should have following stock details for shops "shop1":
      | combination stock detail   | value       |
      | quantity                   | 10          |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr1 |
      | available date             | 2021-10-10  |
    And combination "product1SWhite" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 10             |
    And I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then combination "product1SWhite" should have following details for shops "shop1,shop2":
      | combination detail         | value             |
      | ean13                      | 978020137962      |
      | isbn                       | 978-3-16-148410-0 |
      | mpn                        | mpn1              |
      | reference                  | ref1              |
      | upc                        | 72527273070       |
      | impact on weight           | 17.25             |
      | eco tax                    | 0.5               |
      | impact on price            | 10.0              |
      | impact on unit price       | 0.5               |
      | wholesale price            | 20                |
      | minimal quantity           | 10                |
      | low stock threshold        | 10                |
      | low stock alert is enabled | true              |
      | available date             | 2021-10-10        |
    And combination "product1SWhite" should have following stock details for shops "shop1,shop2":
      | combination stock detail   | value       |
      | quantity                   | 10          |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr1 |
      | available date             | 2021-10-10  |
    And combination "product1SWhite" last stock movements for shop "shop1,shop2" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 10             |
