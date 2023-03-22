# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-pack-multishop
@restore-products-before-feature
@clear-cache-after-feature
@restore-shops-before-feature
@restore-shops-after-feature
@product-multishop
@update-pack-multishop
Feature: Add product to pack from Back Office (BO)
  As a BO user
  I need to be able to add product to pack from BO

  Scenario: This is not an actual scenario, it prepares the multishop required fixtures, we don't use Background here
  to avoid creating different shops for each scenario
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And I associate attribute group "Size" with shops "shop1,shop2"
    And I associate attribute group "Color" with shops "shop1,shop2"
    And I associate attribute "S" with shops "shop1,shop2"
    And I associate attribute "M" with shops "shop1,shop2"
    And I associate attribute "White" with shops "shop1,shop2"
    And I associate attribute "Black" with shops "shop1,shop2"

  Scenario: I add standard product to a multishop pack, all shops are synced
    Given I add product "productPack1" to shop shop1 with following information:
      | name[en-US] | weird sunglasses box |
      | type        | pack                 |
    And product "productPack1" type should be pack
    And I set following shops for product "productPack1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I add product "product2" to shop shop1 with following information:
      | name[en-US] | shady sunglasses |
      | type        | standard         |
    And I set following shops for product "product2":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I update product "product2" with following values:
      | reference | ref1 |
    And product "product2" type should be standard
    When I update pack "productPack1" with following product quantities:
      | product  | quantity |
      | product2 | 5        |
    Then product "productPack1" type should be pack
    And pack "productPack1" should contain products with following details for shops shop1,shop2:
      | product  | combination | name             | quantity | image url                                              | reference |
      | product2 |             | shady sunglasses | 5        | http://myshop.com/img/p/{no_picture}-small_default.jpg | Ref: ref1 |

  Scenario: I add virtual products to a pack
    Given I add product "productPack2" to shop shop1 with following information:
      | name[en-US] | street photos |
      | type        | pack          |
    And product "productPack2" type should be pack
    And I set following shops for product "productPack2":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I add product "product3" to shop shop1 with following information:
      | name[en-US] | summerstreet |
      | type        | virtual      |
    And I set following shops for product "product3":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I add new image "image3" named "logo.jpg" to product "product3"
    And I apply the following matrix of images for product "product3":
      | imageReference | shopReferences |
      | image3         | shop1, shop2   |
    And I add product "product4" to shop shop1 with following information:
      | name[en-US] | winterstreet |
      | type        | virtual      |
    And I set following shops for product "product4":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I add new image "image4" named "logo.jpg" to product "product4"
    And I apply the following matrix of images for product "product4":
      | imageReference | shopReferences |
      | image4         | shop1, shop2   |
    And product "product3" type should be virtual
    And product "product4" type should be virtual
    When I update pack "productPack2" with following product quantities:
      | product  | quantity |
      | product3 | 3        |
      | product4 | 20       |
    Then product "productPack2" type should be pack
    And pack "productPack2" should contain products with following details for shops shop1,shop2:
      | product  | combination | name         | quantity | image url                            |
      | product3 |             | summerstreet | 3        | http://myshop.com/img/p/{image3}.jpg |
      | product4 |             | winterstreet | 20       | http://myshop.com/img/p/{image4}.jpg |

  Scenario: I update pack by removing one of the products
    Given pack "productPack2" should contain products with following details for shops shop1,shop2:
      | product  | combination | name         | quantity | image url                            |
      | product3 |             | summerstreet | 3        | http://myshop.com/img/p/{image3}.jpg |
      | product4 |             | winterstreet | 20       | http://myshop.com/img/p/{image4}.jpg |
    When I update pack "productPack2" with following product quantities:
      | product  | quantity |
      | product3 | 3        |
    And pack "productPack2" should contain products with following details for shops shop1,shop2:
      | product  | combination | name         | quantity | image url                            |
      | product3 |             | summerstreet | 3        | http://myshop.com/img/p/{image3}.jpg |

  Scenario: I add virtual and standard product to the same pack
    Given I add product productPack4 to shop shop1 with following information:
      | name[en-US] | mixed pack |
      | type        | pack       |
    And I set following shops for product "productPack4":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Given product "product2" type should be standard
    And product "product3" type should be virtual
    When I update pack productPack4 with following product quantities:
      | product  | quantity |
      | product2 | 2        |
      | product3 | 3        |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following details for shops shop1,shop2:
      | product  | combination | name             | quantity | image url                                              |
      | product2 |             | shady sunglasses | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | product3 |             | summerstreet     | 3        | http://myshop.com/img/p/{image3}.jpg                   |

  Scenario: I remove all products from existing pack
    Given product "productPack4" type should be pack
    When pack productPack4 should contain products with following details for shops shop1,shop2:
      | product  | name             | combination | quantity | image url                                              |
      | product2 | shady sunglasses |             | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | product3 | summerstreet     |             | 3        | http://myshop.com/img/p/{image3}.jpg                   |
    And I remove all products from pack productPack4
    Then product "productPack4" type should be pack
    And pack "productPack4" should be empty for shops shop1,shop2

  Scenario: Add combination product to a pack
    Given I add product "productSkirt1" to shop shop1 with following information:
      | name[en-US] | regular skirt |
      | type        | combinations  |
    And I set following shops for product "productSkirt1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    When I generate combinations in shop "shop1" for product productSkirt1 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    And I generate combinations in shop "shop2" for product productSkirt1 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    Then product "productSkirt1" should have the following combinations for shops "shop1,shop2":
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
    And pack productPack4 should contain products with following details for shops shop1,shop2:
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
    And pack productPack4 should contain products with following details for shops shop1,shop2:
      | product       | name                                   | combination         | quantity | image url                                              | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg              | Ref: productSkirtRef       |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              | Ref: productSkirtRef       |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg | Ref: ref1                  |

  Scenario: I remove one combination of same product from existing pack and change another combination quantity
    Given product "productPack4" type should be pack
    When pack productPack4 should contain products with following details for shops shop1,shop2:
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
    Then pack productPack4 should contain products with following details for shops shop1,shop2:
      | product       | name                                   | combination         | quantity | image url                                              |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 9        | http://myshop.com/img/p/{skirtBlackM}.jpg              |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    Then product "productPack4" type should be pack

  Scenario: I remove all products from existing pack when it contains combination and standard products
    Given product "productPack4" type should be pack
    When pack productPack4 should contain products with following details for shops shop1,shop2:
      | product       | name                                   | combination         | quantity | image url                                              |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 9        | http://myshop.com/img/p/{skirtBlackM}.jpg              |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    And I remove all products from pack productPack4
    Then product "productPack4" type should be pack
    And pack "productPack4" should be empty for shops shop1,shop2

  Scenario: I remove a product or a combination its relation should also be removed if it was associated with a pack
    # We create a product only associated on the default shop for now because the delete command doesn't allow us to correctly remove it
    Given I update pack productPack4 with following product quantities:
      | product       | combination         | quantity |
      | productSkirt1 | productSkirt1SWhite | 10       |
      | productSkirt1 | productSkirt1MWhite | 11       |
      | productSkirt1 | productSkirt1MBlack | 12       |
      | product2      |                     | 2        |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following details for shops shop1,shop2:
      | product       | name                                   | combination         | quantity | image url                                              | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg              | Ref: productSkirtRef       |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              | Ref: productSkirtRef       |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg | Ref: ref1                  |
    When I delete product product2 from shops "shop2"
    # Product2 still exist in shop1
    Then pack productPack4 should contain products with following details for shops shop1,shop2:
      | product       | name                                   | combination         | quantity | image url                                              | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg              | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg              | Ref: productSkirtRef       |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg              | Ref: productSkirtRef       |
      | product2      | shady sunglasses                       |                     | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg | Ref: ref1                  |
    When I delete product product2 from shops "shop1"
    # Product2 is fully removed
    Then pack productPack4 should contain products with following details for shops shop1,shop2:
      | product       | name                                   | combination         | quantity | image url                                 | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg | Ref: productSkirtRef       |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg | Ref: productSkirtRef       |
    When I delete combination productSkirt1MWhite from shops "shop2"
    # productSkirt1MWhite is still in shop1
    Then pack productPack4 should contain products with following details for shops shop1,shop2:
      | product       | name                                   | combination         | quantity | image url                                 | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - White | productSkirt1MWhite | 11       | http://myshop.com/img/p/{skirtWhiteM}.jpg | Ref: productSkirtRef       |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg | Ref: productSkirtRef       |
    When I delete combination productSkirt1MWhite from shops "shop1"
    # productSkirt1MWhite is fully removed
    Then pack productPack4 should contain products with following details for shops shop1,shop2:
      | product       | name                                   | combination         | quantity | image url                                 | reference                  |
      | productSkirt1 | regular skirt: Size - S, Color - White | productSkirt1SWhite | 10       | http://myshop.com/img/p/{skirtWhiteS}.jpg | Ref: productSkirtSWhiteRef |
      | productSkirt1 | regular skirt: Size - M, Color - Black | productSkirt1MBlack | 12       | http://myshop.com/img/p/{skirtBlackM}.jpg | Ref: productSkirtRef       |

  Scenario: I can add product to a pack regardless of their common shops (or uncommon), the name depends on the shop though
    Given I add product "productPack5" to shop shop2 with following information:
      | name[en-US] | weird sunglasses box |
      | type        | pack                 |
    And product "productPack5" type should be pack for shop shop2
    When I add product "product5" to shop shop1 with following information:
      | name[en-US] | work sunglasses |
      | type        | standard        |
    And I add product "product6" to shop shop3 with following information:
      | name[en-US] | personal sunglasses |
      | type        | standard            |
    And I add product "product7" to shop shop4 with following information:
      | name[en-US] | casual sunglasses |
      | type        | standard          |
    And I set following shops for product "product7":
      | source shop | shop4       |
      | shops       | shop4,shop2 |
    And I update pack productPack5 with following product quantities:
      | product  | quantity |
      | product5 | 10       |
      | product6 | 11       |
      | product7 | 15       |
    And pack "productPack5" should contain products with following details for shops "shop1,shop2,shop4":
      | product  | combination | name                | quantity | image url                                              | reference |
      | product5 |             | work sunglasses     | 10       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product6 |             | personal sunglasses | 11       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product7 |             | casual sunglasses   | 15       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
    And I update product "product7" for shop "shop2" with following values:
      | name[en-US] | casual sunglasses 2 |
    And I update product "product7" for shop "shop4" with following values:
      | name[en-US] | casual sunglasses 4 |
    Then product "product7" localized "name" for shops "shop4" should be:
      | locale | value               |
      | en-US  | casual sunglasses 4 |
    And product "product7" localized "name" for shops "shop2" should be:
      | locale | value               |
      | en-US  | casual sunglasses 2 |
    And pack "productPack5" should contain products with following details for shops "shop1":
      | product  | combination | name                | quantity | image url                                              | reference |
      | product5 |             | work sunglasses     | 10       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product6 |             | personal sunglasses | 11       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product7 |             | casual sunglasses   | 15       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
    And pack "productPack5" should contain products with following details for shops "shop2":
      | product  | combination | name                | quantity | image url                                              | reference |
      | product5 |             | work sunglasses     | 10       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product6 |             | personal sunglasses | 11       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product7 |             | casual sunglasses 2 | 15       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
    And pack "productPack5" should contain products with following details for shops "shop4":
      | product  | combination | name                | quantity | image url                                              | reference |
      | product5 |             | work sunglasses     | 10       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product6 |             | personal sunglasses | 11       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product7 |             | casual sunglasses 4 | 15       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
