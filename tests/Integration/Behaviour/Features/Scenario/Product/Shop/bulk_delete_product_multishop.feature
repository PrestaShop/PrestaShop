# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags bulk-delete-product-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multishop
@bulk-delete-product-multishop
Feature: Bulk delete products when multishop feature is enabled
  As a BO user I want to be able to delete multiple products at once depending on shop context.

  Background:
    Given I enable multishop feature
    And language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And I associate attribute group "Size" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute group "Color" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "S" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "M" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "L" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "White" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "Black" with shops "shop1,shop2,shop3,shop4"
    And I associate attribute "Blue" with shops "shop1,shop2,shop3,shop4"
    And language "french" with locale "fr-FR" exists
    And I add product "standardProduct" to shop "shop2" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    When I update product "standardProduct" stock for shop shop2 with following information:
      | delta_quantity | 51 |
    And I add new image "image1" named "app_icon.png" to product "standardProduct" for shop "shop2"
    And I add new image "image2" named "some_image.jpg" to product "standardProduct" for shop "shop2"
    When I set following shops for product "standardProduct":
      | source shop | shop2                   |
      | shops       | shop1,shop2,shop3,shop4 |
    And product standardProduct is associated to shops "shop1,shop2,shop3,shop4"
    And product "standardProduct" should have following stock information for shops "shop1,shop2,shop3,shop4":
      | quantity | 51 |
    And product "standardProduct" should have following images for shops "shop1,shop2,shop3,shop4":
      | image reference | position | shops                      |
      | image1          | 1        | shop1, shop2, shop3, shop4 |
      | image2          | 2        | shop1, shop2, shop3, shop4 |
    And default shop for product standardProduct is shop2
    And I add product "productWithCombinations" to shop shop3 with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And I generate combinations in shop "shop3" for product productWithCombinations using following attributes:
      | Size  | [L]                |
      | Color | [White,Black,Blue] |
    And I set following shops for product "productWithCombinations":
      | source shop | shop3                   |
      | shops       | shop1,shop2,shop3,shop4 |
    And product productWithCombinations is associated to shops "shop1,shop2,shop3,shop4"
    And default shop for product productWithCombinations is shop3
    And product "productWithCombinations" should have the following combinations for shops "shop1,shop2,shop3,shop4":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | false      |
    And I update combination "product1LWhite" stock for all shops with following details:
      | delta quantity | 10 |
    And I update combination "product1LBlack" stock for all shops with following details:
      | delta quantity | 20 |
    And I update combination "product1LBlue" stock for all shops with following details:
      | delta quantity | 30 |
    And product "productWithCombinations" should have the following combinations for shops "shop1,shop2,shop3,shop4":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 10       | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 20       | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 30       | false      |

  Scenario: I can bulk delete product from first shop group
    When I bulk delete following products from shop group default_shop_group:
      | reference               |
      | standardProduct         |
      | productWithCombinations |
    And product standardProduct is not associated to shops "shop1,shop2"
    And product standardProduct is associated to shop "shop3,shop4"
    And default shop for product standardProduct is shop3
    And product "standardProduct" should have following stock information for shops "shop3,shop4":
      | quantity | 51 |
    And product "standardProduct" should have following images for shops "shop3,shop4":
      | image reference | position | shops        |
      | image1          | 1        | shop3, shop4 |
      | image2          | 2        | shop3, shop4 |
    And product productWithCombinations is associated to shops "shop3,shop4"
    And default shop for product productWithCombinations is shop3
    And product "productWithCombinations" should have the following combinations for shops "shop3,shop4":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 10       | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 20       | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 30       | false      |
    And product productWithCombinations is not associated to shops "shop1,shop2"
    And combinations "product1LWhite,product1LBlack,product1LBlue" are not associated to shops "shop1,shop2"

  Scenario: I can bulk delete product from second shop group
    When I bulk delete following products from shop group test_second_shop_group:
      | reference               |
      | standardProduct         |
      | productWithCombinations |
    And product standardProduct is associated to shops "shop1,shop2"
    And product standardProduct is not associated to shops "shop3,shop4"
    And default shop for product standardProduct is shop2
    And product "standardProduct" should have following stock information for shops "shop1,shop2":
      | quantity | 51 |
    And product "standardProduct" should have following images for shops "shop1,shop2":
      | image reference | position | shops        |
      | image1          | 1        | shop1, shop2 |
      | image2          | 2        | shop1, shop2 |
    And default shop for product productWithCombinations is shop1
    And product "productWithCombinations" should have the following combinations for shops "shop1,shop2":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 10       | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 20       | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 30       | false      |
    And product productWithCombinations is associated to shops "shop1,shop2"
    And product productWithCombinations is not associated to shops "shop3,shop4"
    And combinations "product1LWhite,product1LBlack,product1LBlue" are not associated to shops "shop3,shop4"

  Scenario: I can bulk delete product from shop shop2
    When I bulk delete following products from shop shop2:
      | reference               |
      | standardProduct         |
      | productWithCombinations |
    And product standardProduct is not associated to shop shop2
    And product standardProduct is associated to shops "shop1,shop3,shop4"
    And default shop for product standardProduct is shop1
    And product "standardProduct" should have following stock information for shops "shop1,shop3,shop4":
      | quantity | 51 |
    And product "standardProduct" should have following images for shops "shop1,shop3,shop4":
      | image reference | position | shops               |
      | image1          | 1        | shop1, shop3, shop4 |
      | image2          | 2        | shop1, shop3, shop4 |
    And default shop for product productWithCombinations is shop3
    And product "productWithCombinations" should have the following combinations for shops "shop1,shop3,shop4":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 10       | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 20       | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 30       | false      |
    And product productWithCombinations is associated to shops "shop1,shop3,shop4"
    And product productWithCombinations is not associated to shop shop2
    And combinations "product1LWhite,product1LBlack,product1LBlue" are not associated to shop "shop2"

  Scenario: I can bulk delete product from shop shop3
    When I bulk delete following products from shop shop3:
      | reference               |
      | standardProduct         |
      | productWithCombinations |
    And product standardProduct is associated to shop "shop1,shop2,shop4"
    And product standardProduct is not associated to shop shop3
    And default shop for product standardProduct is shop2
    And product "standardProduct" should have following stock information for shops "shop1,shop2,shop4":
      | quantity | 51 |
    And product "standardProduct" should have following images for shops "shop1,shop2,shop4":
      | image reference | position | shops               |
      | image1          | 1        | shop1, shop2, shop4 |
      | image2          | 2        | shop1, shop2, shop4 |
    And default shop for product productWithCombinations is shop1
    And product "productWithCombinations" should have the following combinations for shops "shop1,shop2,shop4":
      | combination id | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 10       | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 20       | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 30       | false      |
    And product productWithCombinations is associated to shops "shop1,shop2,shop4"
    And product productWithCombinations is not associated to shop shop3
    And combinations "product1LWhite,product1LBlack,product1LBlue" are not associated to shop "shop3"

  Scenario: I can bulk delete product from all shops
    When I bulk delete following products from all shops:
      | reference               |
      | standardProduct         |
      | productWithCombinations |
    And product "standardProduct" should not exist anymore
    And product "productWithCombinations" should not exist anymore
    And combinations "product1LWhite,product1LBlack,product1LBlue" should not exist anymore
