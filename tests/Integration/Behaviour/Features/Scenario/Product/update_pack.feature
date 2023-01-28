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
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists

  Scenario: I cannot perform pack update on a standard product
    Given I add product "product1" with following information:
      | name[en-US] | weird sunglasses box |
      | type        | standard             |
    And product "product1" type should be standard
    And I add product "standardProduct" with following information:
      | name[en-US] | shady sunglasses |
      | type        | standard         |
    And product "standardProduct" type should be standard
    When I update pack "standardProduct" with following product quantities:
      | product  | quantity |
      | product1 | 5        |
    Then I should get error that this action is allowed for pack product only
    Then product "standardProduct" type should be standard

  Scenario: I cannot perform pack update on a virtual product
    Given I add product "product1" with following information:
      | name[en-US] | weird sunglasses box |
      | type        | standard             |
    And product "product1" type should be standard
    And I add product "virtualProduct" with following information:
      | name[en-US] | shady sunglasses |
      | type        | virtual          |
    And product "virtualProduct" type should be virtual
    When I update pack "virtualProduct" with following product quantities:
      | product  | quantity |
      | product1 | 5        |
    Then I should get error that this action is allowed for pack product only
    Then product "virtualProduct" type should be virtual

  Scenario: I cannot perform pack update on a combinations product
    Given I add product "product1" with following information:
      | name[en-US] | weird sunglasses box |
      | type        | standard             |
    And product "product1" type should be standard
    And I add product "combinationsProduct" with following information:
      | name[en-US] | shady sunglasses |
      | type        | combinations     |
    And product "combinationsProduct" type should be combinations
    When I update pack "combinationsProduct" with following product quantities:
      | product  | quantity |
      | product1 | 5        |
    Then I should get error that this action is allowed for pack product only
    Then product "combinationsProduct" type should be combinations

  Scenario: I add standard product to a pack
    Given I add product "productPack1" with following information:
      | name[en-US] | weird sunglasses box |
      | type        | pack                 |
    And product "productPack1" type should be pack
    And I add product "product2" with following information:
      | name[en-US] | shady sunglasses |
      | type        | standard         |
    And I update product "product2" with following values:
      | reference | ref1              |
    And product "product2" type should be standard
    When I update pack "productPack1" with following product quantities:
      | product  | quantity |
      | product2 | 5        |
    Then product "productPack1" type should be pack
    And pack "productPack1" should contain products with following details:
      | product  | combination | name             | quantity | image url                                              | reference |
      | product2 |             | shady sunglasses | 5        | http://myshop.com/img/p/{no_picture}-small_default.jpg | Ref: ref1 |

  Scenario: I add virtual products to a pack
    Given I add product "productPack2" with following information:
      | name[en-US] | street photos |
      | type        | pack          |
    And product "productPack2" type should be pack
    And I add product "product3" with following information:
      | name[en-US] | summerstreet |
      | type        | virtual      |
    And I add new image "image3" named "logo.jpg" to product "product3"
    And I add product "product4" with following information:
      | name[en-US] | winterstreet |
      | type        | virtual      |
    And I add new image "image4" named "logo.jpg" to product "product4"
    And product "product3" type should be virtual
    And product "product4" type should be virtual
    When I update pack "productPack2" with following product quantities:
      | product  | quantity |
      | product3 | 3        |
      | product4 | 20       |
    Then product "productPack2" type should be pack
    And pack "productPack2" should contain products with following details:
      | product  | combination | name             | quantity | image url                            |
      | product3 |             | summerstreet     | 3        | http://myshop.com/img/p/{image3}.jpg |
      | product4 |             | winterstreet     | 20       | http://myshop.com/img/p/{image4}.jpg |

  Scenario: I update pack by removing one of the products
    When pack productPack2 should contain products with following details:
      | product  | combination | name             | quantity | image url                            |
      | product3 |             | summerstreet     | 3        | http://myshop.com/img/p/{image3}.jpg |
      | product4 |             | winterstreet     | 20       | http://myshop.com/img/p/{image4}.jpg |
    When I update pack "productPack2" with following product quantities:
      | product  | quantity |
      | product3 | 3        |
    And pack "productPack2" should contain products with following details:
      | product  | combination | name             | quantity | image url                            |
      | product3 |             | summerstreet     | 3        | http://myshop.com/img/p/{image3}.jpg |

  Scenario: I add pack product to a pack
    Given product "productPack1" type should be pack
    And product "productPack2" type should be pack
    When I update pack productPack2 with following product quantities:
      | product      | quantity |
      | productPack1 | 1        |
    Then I should get error that I cannot add pack into a pack

  Scenario: I add virtual and standard product to the same pack
    Given I add product productPack4 with following information:
      | name[en-US] | mixed pack |
      | type        | pack       |
    Given product "product2" type should be standard
    And product "product3" type should be virtual
    When I update pack productPack4 with following product quantities:
      | product  | quantity |
      | product2 | 2        |
      | product3 | 3        |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following details:
      | product  | combination | name             | quantity | image url                                              |
      | product2 |             | shady sunglasses | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | product3 |             | summerstreet     | 3        | http://myshop.com/img/p/{image3}.jpg                   |

  Scenario: I add product with negative quantity to a pack
    Given product "product2" type should be standard
    Then product "productPack4" type should be pack
    When I update pack productPack4 with following product quantities:
      | product  | quantity |
      | product2 | -10      |
      | product3 | 3        |
    Then I should get error that product for packing quantity is invalid

  Scenario: I remove all products from existing pack
    Given product "productPack4" type should be pack
    When pack productPack4 should contain products with following details:
      | product  | name             | combination | quantity | image url                                              |
      | product2 | shady sunglasses |             | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | product3 | summerstreet     |             | 3        | http://myshop.com/img/p/{image3}.jpg                   |
    And I remove all products from pack productPack4
    Then product "productPack4" type should be pack
    And pack "productPack4" should be empty

  Scenario: Add combination product to a pack
    Given I add product "productSkirt1" with following information:
      | name[en-US] | regular skirt |
      | type        | combinations  |
    When I generate combinations for product productSkirt1 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    Then product "productSkirt1" should have following combinations:
      | id reference        | combination name        | reference | attributes           | impact on price | quantity | is default |
      | productSkirt1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | productSkirt1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | productSkirt1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | productSkirt1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And I update product "productSkirt1" with following values:
      | reference | productSkirtRef |
    And I update combination "productSkirt1SWhite" with following values:
      | reference | productSkirtSWhiteRef |
    And I add new image "skirtWhiteS" named "app_icon.png" to product "productSkirt1"
    And I add new image "skirtWhiteM" named "logo.jpg" to product "productSkirt1"
    And I add new image "skirtBlackS" named "app_icon.png" to product "productSkirt1"
    And I add new image "skirtBlackM" named "logo.jpg" to product "productSkirt1"
    And I associate "[skirtWhiteS]" to combination "productSkirt1SWhite"
    And I associate "[skirtBlackS]" to combination "productSkirt1SBlack"
    And I associate "[skirtWhiteM]" to combination "productSkirt1MWhite"
    And I associate "[skirtBlackM]" to combination "productSkirt1MBlack"
    And I add new image "image3" named "logo.jpg" to product "productSkirt1"
    And product productSkirt1 type should be combinations
    And product "productPack4" type should be pack
    When I update pack productPack4 with following product quantities:
      | product       | combination         | quantity |
      | productSkirt1 | productSkirt1SWhite | 10       |
      | productSkirt1 | productSkirt1MWhite | 11       |
      | productSkirt1 | productSkirt1MBlack | 12       |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                 | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg | Ref: productSkirtRef       |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg | Ref: productSkirtRef       |

  Scenario: Add combination & standard product to a pack
    Given product "product2" type should be standard
    And product productSkirt1 type should be combinations
    And product "productSkirt1" should have following combinations:
      | id reference        | combination name        | reference             | attributes           | impact on price | quantity | is default |
      | productSkirt1SWhite | Size - S, Color - White | productSkirtSWhiteRef | [Size:S,Color:White] | 0               | 0        | true       |
      | productSkirt1SBlack | Size - S, Color - Black |                       | [Size:S,Color:Black] | 0               | 0        | false      |
      | productSkirt1MWhite | Size - M, Color - White |                       | [Size:M,Color:White] | 0               | 0        | false      |
      | productSkirt1MBlack | Size - M, Color - Black |                       | [Size:M,Color:Black] | 0               | 0        | false      |
    When I update pack productPack4 with following product quantities:
      | product       | combination         | quantity |
      | productSkirt1 | productSkirt1SWhite | 10       |
      | productSkirt1 | productSkirt1MWhite | 11       |
      | productSkirt1 | productSkirt1MBlack | 12       |
      | product2      |                     | 2        |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                              | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg              | Ref: productSkirtRef       |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              | Ref: productSkirtRef       |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg | Ref: ref1                  |

  Scenario: I remove one combination of same product from existing pack and change another combination quantity
    Given product "productPack4" type should be pack
    When pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                              |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg              |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    And I update pack productPack4 with following product quantities:
      | product       | combination         | quantity |
      | productSkirt1 | productSkirt1SWhite | 10       |
      | productSkirt1 | productSkirt1MBlack | 9        |
      | product2      |                     | 2        |
    Then pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                              |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 9        | http://myshop.com/img/p/{skirtBlackM}.jpg              |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    Then product "productPack4" type should be pack

  Scenario: I remove all products from existing pack when it contains combination and standard products
    Given product "productPack4" type should be pack
    When pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                              |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 9        | http://myshop.com/img/p/{skirtBlackM}.jpg              |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    And I remove all products from pack productPack4
    Then product "productPack4" type should be pack
    And pack "productPack4" should be empty

  Scenario: I remove a product or a combination its relation should also be removed if it was associated with a pack
    Given I update pack productPack4 with following product quantities:
      | product       | combination         | quantity |
      | productSkirt1 | productSkirt1SWhite | 10       |
      | productSkirt1 | productSkirt1MWhite | 11       |
      | productSkirt1 | productSkirt1MBlack | 12       |
      | product2      |                     | 2        |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                              | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg              | Ref: productSkirtRef       |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              | Ref: productSkirtRef       |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg | Ref: ref1                  |
    When I delete product product2
    Then pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                              | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg              | Ref: productSkirtRef       |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              | Ref: productSkirtRef       |
    When I delete combination productSkirt1MWhite
    Then pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                              | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              | Ref: productSkirtRef       |

  Scenario: I cannot change a product type to a pack if it is already associated with a pack
    Given I add product "product5" with following information:
      | name[en-US] | shady sunglasses |
      | type        | standard         |
    And product "product5" type should be standard
    Given I update pack productPack4 with following product quantities:
      | product       | combination         | quantity |
      | productSkirt1 | productSkirt1SWhite | 10       |
      | productSkirt1 | productSkirt1MBlack | 12       |
      | product5      |                     | 2        |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                              | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              | Ref: productSkirtRef       |
      | product5      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |                            |
    When I update product "product5" type to pack
    Then I should get error that the product is already associated to a pack
    And product "product5" type should be standard
    And product "productPack4" type should be pack
    And pack productPack4 should contain products with following details:
      | product       | name                                   | combination         | quantity | image url                                              | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              | Ref: productSkirtRef       |
      | product5      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |                            |
