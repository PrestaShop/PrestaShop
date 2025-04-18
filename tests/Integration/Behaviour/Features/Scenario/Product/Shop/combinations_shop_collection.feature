# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags combinations-shop-collection
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multishop
@combinations-shop-collection
Feature: Edit combinations with specific list of shops.
  As a BO user I want to be able to edit combinations for specific shops.

  Background:
    Given I enable multishop feature
    And language with iso code "en" is the default one
    And language "english" with locale "en-US" exists
    And language "french" with locale "fr-FR" exists
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Yellow" named "Yellow" in en language exists
    And manufacturer studioDesign named "Studio Design" exists
    And manufacturer graphicCorner named "Graphic Corner" exists
    And following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And I associate attribute group "Size" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute group "Color" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "S" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "M" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "L" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "White" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "Black" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "Blue" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "Yellow" with shops "shop1,shop2,shop3,shop4"
    And single shop context is loaded
    And language "french" with locale "fr-FR" exists
    And I add product "product" to shop "shop1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And default shop for product product is shop1
    And I set following shops for product "product":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    Then product product is associated to shops "shop1,shop2,shop3,shop4"
    And product "product" should have no images

  Scenario: I can generate combinations for specific shops and edit them
    Given I add product "product" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product type should be combinations
    And default shop for product product is shop1
    And I set following shops for product "product":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    Then product product is associated to shops "shop1,shop2,shop3,shop4"
    When I generate combinations for product product in shops "shop1,shop3" using following attributes:
      | Size  | [L]                  |
      | Color | [White,Black,Yellow] |
    Then product "product" should have the following combinations for shops "shop1,shop3":
      | id reference   | combination name         | reference | attributes            | impact on price | quantity | is default |
      | productLWhite  | Size - L, Color - White  |           | [Size:L,Color:White]  | 0               | 0        | true       |
      | productLBlack  | Size - L, Color - Black  |           | [Size:L,Color:Black]  | 0               | 0        | false      |
      | productLYellow | Size - L, Color - Yellow |           | [Size:L,Color:Yellow] | 0               | 0        | false      |
    And product "product" should have no combinations for shops "shop2,shop4"
    When I generate combinations for product product in shops "shop2,shop3" using following attributes:
      | Size  | [L,M]        |
      | Color | [Black,Blue] |
    Then product "product" should have the following combinations for shop "shop1":
      | id reference   | combination name         | reference | attributes            | impact on price | quantity | is default |
      | productLWhite  | Size - L, Color - White  |           | [Size:L,Color:White]  | 0               | 0        | true       |
      | productLBlack  | Size - L, Color - Black  |           | [Size:L,Color:Black]  | 0               | 0        | false      |
      | productLYellow | Size - L, Color - Yellow |           | [Size:L,Color:Yellow] | 0               | 0        | false      |
    And product "product" should have the following combinations for shop "shop2":
      | id reference  | combination name        | reference | attributes           | impact on price | quantity | is default |
      | productLBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | true       |
      | productLBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | false      |
      | productMBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | productMBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product" should have the following combinations for shop "shop3":
      | id reference   | combination name         | reference | attributes            | impact on price | quantity | is default |
      | productLWhite  | Size - L, Color - White  |           | [Size:L,Color:White]  | 0               | 0        | true       |
      | productLBlack  | Size - L, Color - Black  |           | [Size:L,Color:Black]  | 0               | 0        | false      |
      | productLYellow | Size - L, Color - Yellow |           | [Size:L,Color:Yellow] | 0               | 0        | false      |
      | productLBlue   | Size - L, Color - Blue   |           | [Size:L,Color:Blue]   | 0               | 0        | false      |
      | productMBlack  | Size - M, Color - Black  |           | [Size:M,Color:Black]  | 0               | 0        | false      |
      | productMBlue   | Size - M, Color - Blue   |           | [Size:M,Color:Blue]   | 0               | 0        | false      |
    And product "product" should have no combinations for shops "shop4"
    # Update default combinations on two shops
    When I set combination "productLYellow" as default for shops "shop1,shop3"
    And I set combination "productLBlue" as default for shops "shop2"
    Then product "product" should have the following combinations for shop "shop1":
      | id reference   | combination name         | reference | attributes            | impact on price | quantity | is default |
      | productLWhite  | Size - L, Color - White  |           | [Size:L,Color:White]  | 0               | 0        | false      |
      | productLBlack  | Size - L, Color - Black  |           | [Size:L,Color:Black]  | 0               | 0        | false      |
      | productLYellow | Size - L, Color - Yellow |           | [Size:L,Color:Yellow] | 0               | 0        | true       |
    And product "product" should have the following combinations for shop "shop2":
      | id reference  | combination name        | reference | attributes           | impact on price | quantity | is default |
      | productLBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
      | productLBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | true       |
      | productMBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | productMBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product" should have the following combinations for shop "shop3":
      | id reference   | combination name         | reference | attributes            | impact on price | quantity | is default |
      | productLWhite  | Size - L, Color - White  |           | [Size:L,Color:White]  | 0               | 0        | false      |
      | productLBlack  | Size - L, Color - Black  |           | [Size:L,Color:Black]  | 0               | 0        | false      |
      | productLYellow | Size - L, Color - Yellow |           | [Size:L,Color:Yellow] | 0               | 0        | true       |
      | productLBlue   | Size - L, Color - Blue   |           | [Size:L,Color:Blue]   | 0               | 0        | false      |
      | productMBlack  | Size - M, Color - Black  |           | [Size:M,Color:Black]  | 0               | 0        | false      |
      | productMBlue   | Size - M, Color - Blue   |           | [Size:M,Color:Blue]   | 0               | 0        | false      |
    And product "product" should have no combinations for shops "shop4"
    # Update infos for shop2 and shop3
    When I update combination "productLBlack" with following values for shops "shop2,shop3":
      | reference                  | ABC               |
      | ean13                      | 978020137962      |
      | isbn                       | 978-3-16-148410-0 |
      | mpn                        | mpn1              |
      | upc                        | 72527273070       |
      | impact on weight           | 17.25             |
      | eco tax                    | 0.5               |
      | impact on price            | 10.50             |
      | impact on unit price       | 0.5               |
      | wholesale price            | 20                |
      | minimal quantity           | 10                |
      | low stock threshold        | 10                |
      | low stock alert is enabled | true              |
      | available date             | 2021-10-10        |
    # But update quantity for shop1 and shop2
    When I update combination "productLBlack" stock for shops "shop1,shop2" with following details:
      | fixed quantity | 10 |
    Then product "product" should have the following combinations for shop "shop1":
      | id reference   | combination name         | reference | attributes            | impact on price | quantity | is default |
      | productLWhite  | Size - L, Color - White  |           | [Size:L,Color:White]  | 0               | 0        | false      |
      | productLBlack  | Size - L, Color - Black  | ABC       | [Size:L,Color:Black]  | 0               | 10       | false      |
      | productLYellow | Size - L, Color - Yellow |           | [Size:L,Color:Yellow] | 0               | 0        | true       |
    And product "product" should have the following combinations for shop "shop2":
      | id reference  | combination name        | reference | attributes           | impact on price | quantity | is default |
      | productLBlack | Size - L, Color - Black | ABC       | [Size:L,Color:Black] | 10.5            | 10       | false      |
      | productLBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | true       |
      | productMBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | productMBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product" should have the following combinations for shop "shop3":
      | id reference   | combination name         | reference | attributes            | impact on price | quantity | is default |
      | productLWhite  | Size - L, Color - White  |           | [Size:L,Color:White]  | 0               | 0        | false      |
      | productLBlack  | Size - L, Color - Black  | ABC       | [Size:L,Color:Black]  | 10.50           | 0        | false      |
      | productLYellow | Size - L, Color - Yellow |           | [Size:L,Color:Yellow] | 0               | 0        | true       |
      | productLBlue   | Size - L, Color - Blue   |           | [Size:L,Color:Blue]   | 0               | 0        | false      |
      | productMBlack  | Size - M, Color - Black  |           | [Size:M,Color:Black]  | 0               | 0        | false      |
      | productMBlue   | Size - M, Color - Blue   |           | [Size:M,Color:Blue]   | 0               | 0        | false      |
    And product "product" should have no combinations for shops "shop4"
