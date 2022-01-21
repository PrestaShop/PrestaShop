# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-pack
@restore-products-before-feature
@clear-cache-after-feature
@update-pack
Feature: Add product to pack from Back Office (BO)
  As a BO user
  I need to be able to add product to pack from BO

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

  Scenario: I cannot perform pack update on a standard product
    Given I add product product1 with following information:
      | name[en-US] | weird sunglasses box |
      | type        | standard             |
    And product product1 type should be standard
    And I add product standardProduct with following information:
      | name[en-US] | shady sunglasses |
      | type        | standard         |
    And product standardProduct type should be standard
    When I update pack standardProduct with following product quantities:
      | product  | combination | quantity |
      | product1 |             | 5        |
    Then I should get error that this action is allowed for pack product only
    Then product standardProduct type should be standard

  Scenario: I cannot perform pack update on a virtual product
    Given I add product product1 with following information:
      | name[en-US] | weird sunglasses box |
      | type        | standard             |
    And product product1 type should be standard
    And I add product virtualProduct with following information:
      | name[en-US] | shady sunglasses |
      | type        | virtual          |
    And product virtualProduct type should be virtual
    When I update pack virtualProduct with following product quantities:
      | product  | quantity |
      | product1 | 5        |
    Then I should get error that this action is allowed for pack product only
    Then product virtualProduct type should be virtual

  Scenario: I cannot perform pack update on a combinations product
    Given I add product product1 with following information:
      | name[en-US] | weird sunglasses box |
      | type        | standard             |
    And product product1 type should be standard
    And I add product combinationsProduct with following information:
      | name[en-US] | shady sunglasses |
      | type        | combinations     |
    And product combinationsProduct type should be combinations
    When I update pack combinationsProduct with following product quantities:
      | product  | quantity |
      | product1 | 5        |
    Then I should get error that this action is allowed for pack product only
    Then product combinationsProduct type should be combinations

  Scenario: I add standard product to a pack
    Given I add product productPack1 with following information:
      | name[en-US] | weird sunglasses box |
      | type        | pack                 |
    And product productPack1 type should be pack
    And I add product product2 with following information:
      | name[en-US] | shady sunglasses |
      | type        | standard         |
    And product product2 type should be standard
    When I update pack "productPack1" with following product quantities:
      | product  | quantity |
      | product2 | 5        |
    Then product productPack1 type should be pack
    And pack productPack1 should contain products with following details:
      | product  | combination | quantity | name             |
      | product2 |             | 5        | shady sunglasses |

  Scenario: I add virtual products to a pack
    Given I add product productPack2 with following information:
      | name[en-US] | street photos |
      | type        | pack          |
    And product productPack2 type should be pack
    And I add product product3 with following information:
      | name[en-US] | summerstreet |
      | type        | virtual      |
    And I add product product4 with following information:
      | name[en-US] | winterstreet |
      | type        | virtual      |
    And product product3 type should be virtual
    And product product4 type should be virtual
    When I update pack productPack2 with following product quantities:
      | product  | quantity |
      | product3 | 3        |
      | product4 | 20       |
    Then product productPack2 type should be pack
    And pack productPack2 should contain products with following details:
      | product  | combination | quantity | name           |
      | product3 |             | 3        | summerstreet   |
      | product4 |             | 20       | winterstreet |

  Scenario: I update pack by removing one of the products
    Given pack productPack2 should contain products with following details:
      | product  | combination | quantity | name           |
      | product3 |             | 3        | summerstreet   |
      | product4 |             | 20       | winterstreet   |
    When I update pack productPack2 with following product quantities:
      | product  | quantity |
      | product3 | 3        |
    And pack productPack2 should contain products with following details:
      | product  | combination | quantity | name           |
      | product3 |             | 3        | summerstreet   |

  Scenario: I add pack product to a pack
    Given product productPack1 type should be pack
    And product productPack2 type should be pack
    When I update pack productPack2 with following product quantities:
      | product      | quantity |
      | productPack1 | 1        |
    Then I should get error that I cannot add pack into a pack

  Scenario: I add virtual and standard product to the same pack
    Given I add product productPack4 with following information:
      | name[en-US] | mixed pack |
      | type        | pack       |
    Given product product2 type should be standard
    And product product3 type should be virtual
    When I update pack productPack4 with following product quantities:
      | product  | quantity |
      | product2 | 2        |
      | product3 | 3        |
    Then product productPack4 type should be pack
    And pack productPack4 should contain products with following details:
      | product  | combination | quantity | name             |
      | product2 |             | 2        | shady sunglasses |
      | product3 |             | 3        | summerstreet     |

  Scenario: I add product with negative quantity to a pack
    Given product product2 type should be standard
    Then product productPack4 type should be pack
    When I update pack productPack4 with following product quantities:
      | product  | quantity |
      | product2 | -10      |
      | product3 | 3        |
    Then I should get error that product for packing quantity is invalid

  Scenario: I remove all products from existing pack
    Given product productPack4 type should be pack
    And pack productPack4 should contain products with following details:
      | product  | combination | quantity | name             |
      | product2 |             | 2        | shady sunglasses |
      | product3 |             | 3        | summerstreet     |
    When I remove all products from pack productPack4
    Then product "productPack4" type should be pack
    And pack "productPack4" should be empty

  Scenario: Add combination product to a pack
    Given I add product packedProduct with following information:
      | name[en-US] | mixed pack 2 |
      | type        | pack       |
    And I add product productShirtToBePacked with following information:
      | name[en-US] | regular skirt |
      | type        | combinations  |
    And I generate combinations for product productShirtToBePacked using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product productShirtToBePacked has following combinations:
      | id reference | combination name        | reference | attributes           | impact on price | quantity | is default |
      | whiteS       | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | whiteM       | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | blackM       | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And product packedProduct type should be pack
    When I update pack packedProduct with following product quantities:
      | product                | combination | quantity |
      | productShirtToBePacked | whiteS      | 10       |
      | productShirtToBePacked | whiteM      | 11       |
      | productShirtToBePacked | blackM      | 12       |
    Then product productShirtToBePacked type should be combinations
    And product packedProduct type should be pack
    And pack packedProduct should contain products with following details:
      | product                | combination | quantity | name           |
      | productShirtToBePacked | whiteS      | 10       | regular skirt - Size - S, Color - White |
      | productShirtToBePacked | whiteM      | 11       | regular skirt - Size - M, Color - White |
      | productShirtToBePacked | blackM      | 12       | regular skirt - Size - M, Color - Black |

  Scenario: Add combination & standard product to a pack
    Given product product2 type should be standard
    And product productShirtToBePacked type should be combinations
    And I generate combinations for product productShirtToBePacked using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product productShirtToBePacked has following combinations:
      | id reference | combination name        | reference | attributes           | impact on price | quantity | is default |
      | whiteS       | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | whiteM       | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | blackM       | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    When I update pack packedProduct with following product quantities:
      | product                | combination | quantity |
      | productShirtToBePacked | whiteS      | 10       |
      | productShirtToBePacked | whiteM      | 11       |
      | productShirtToBePacked | blackM      | 12       |
      | product2               |             | 2        |
    Then product packedProduct type should be pack
    And pack packedProduct should contain products with following details:
      | product                | combination | quantity | name                                    |
      | productShirtToBePacked | whiteS      | 10       | regular skirt - Size - S, Color - White |
      | productShirtToBePacked | whiteM      | 11       | regular skirt - Size - M, Color - White |
      | productShirtToBePacked | blackM      | 12       | regular skirt - Size - M, Color - Black |
      | product2               |             | 2        | shady sunglasses                        |

  Scenario: I remove one combination of same product from existing pack and change another combination quantity
    Given product packedProduct type should be pack
    And pack packedProduct should contain products with following details:
      | product                | combination | quantity | name                                    |
      | productShirtToBePacked | whiteS      | 10       | regular skirt - Size - S, Color - White |
      | productShirtToBePacked | whiteM      | 11       | regular skirt - Size - M, Color - White |
      | productShirtToBePacked | blackM      | 12       | regular skirt - Size - M, Color - Black |
      | product2               |             | 2        | shady sunglasses                        |
    When I update pack packedProduct with following product quantities:
      | product                | combination | quantity |
      | productShirtToBePacked | whiteS      | 10       |
      | productShirtToBePacked | blackM      | 9        |
      | product2               |             | 2        |
    Then pack packedProduct should contain products with following details:
      | product                | combination | quantity | name           |
      | productShirtToBePacked | whiteS      | 10       | regular skirt - Size - S, Color - White  |
      | productShirtToBePacked | blackM      | 9        | regular skirt - Size - M, Color - Black  |
      | product2               |             | 2        | shady sunglasses |
    Then product packedProduct type should be pack

  Scenario: I remove all products from existing pack when it contains combination and standard products
    Given product packedProduct type should be pack
    And pack packedProduct should contain products with following details:
      | product                | combination | quantity | name                                    |
      | productShirtToBePacked | whiteS      | 10       | regular skirt - Size - S, Color - White |
      | productShirtToBePacked | blackM      | 9        | regular skirt - Size - M, Color - Black |
      | product2               |             | 2        | shady sunglasses                        |
    When I remove all products from pack packedProduct
    Then product packedProduct type should be pack
    And pack packedProduct should be empty
