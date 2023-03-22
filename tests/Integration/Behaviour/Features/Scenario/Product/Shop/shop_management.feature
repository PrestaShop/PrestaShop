# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags shop-management
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multishop
@shop-management
Feature: Copy product from shop to shop.
  As a BO user I want to be able to copy product from shop to shop.

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
    And language "french" with locale "fr-FR" exists

  Scenario: Add products in specific shop
    Given I add product "createdProduct" to shop "shop2" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product createdProduct is associated to shop shop2
    And default shop for product createdProduct is shop2
    And product createdProduct is not associated to shops "shop1,shop3,shop4"
    # Assert stock has correctly been created for the appropriate shop
    Then product "createdProduct" should have following stock information for shops "shop2":
      | pack_stock_type     | default |
      | out_of_stock_type   | default |
      | quantity            | 0       |
      | minimal_quantity    | 1       |
      | location            |         |
      | low_stock_threshold | 0       |
      | low_stock_alert     | false   |
      | available_date      |         |

  Scenario: Prices are copied when I set new shop association for product
    # By default the product is created for default shop
    Given I add product "productWithPrices" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product productWithPrices is associated to shop shop1
    And default shop for product productWithPrices is shop1
    When I update product "productWithPrices" with following values:
      | price           | 100.99          |
      | ecotax          | 0               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 70              |
      | unit_price      | 10              |
      | unity           | bag of ten      |
    Then product productWithPrices should have following prices information for shops "shop1":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product productWithPrices is not associated to shops "shop2,shop3,shop4"
    # Associate another shop which was not associated yet
    When I set following shops for product "productWithPrices":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product productWithPrices is associated to shops "shop1,shop2"
    And default shop for product productWithPrices is shop1
    And product productWithPrices should have following prices information for shops "shop1,shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    And product productWithPrices is not associated to shops "shop3,shop4"
    # Now modify the values but this time the shop is already associated
    When I update product "productWithPrices" for shop "shop1" with following values:
      | on_sale | false |
    Then product productWithPrices should have following prices information for shops "shop1":
      | on_sale | false |
    But product productWithPrices should have following prices information for shops "shop2":
      | on_sale | true |
    # Set same shops as they already are, nothing should happen (prices should stay as it was both shops)
    When I set following shops for product "productWithPrices":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product productWithPrices is associated to shops "shop1,shop2"
    And product productWithPrices should have following prices information for shops "shop1":
      | on_sale | false |
    And product productWithPrices should have following prices information for shops "shop2":
      | on_sale | true |
    And product productWithPrices is not associated to shops "shop3,shop4"

  Scenario: Basic information is copied when I set new shop association for product
    # By default the product is created for default shop
    Given I add product "productWithBasic" with following information:
      | name[en-US] | funny mug |
      | type        | standard  |
    Then product productWithBasic is associated to shop shop1
    And default shop for product productWithBasic is shop1
    When I update product "productWithBasic" with following values:
      | name[en-US]              | photo of funny mug |
      | description[en-US]       | nice mug           |
      | description_short[en-US] | Just a nice mug    |
    Then product "productWithBasic" localized "name" should be:
      | locale | value              |
      | en-US  | photo of funny mug |
    And product "productWithBasic" localized "description" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "productWithBasic" localized "description_short" should be:
      | locale | value           |
      | en-US  | Just a nice mug |
    And product productWithBasic is not associated to shops "shop2,shop3,shop4"
    # Associate another shop which was not associated yet
    When I set following shops for product "productWithBasic":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product productWithBasic is associated to shops "shop1,shop2"
    And default shop for product productWithBasic is shop1
    Then product "productWithBasic" localized "name" for shops "shop1,shop2" should be:
      | locale | value              |
      | en-US  | photo of funny mug |
    And product "productWithBasic" localized "description" for shops "shop1,shop2" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "productWithBasic" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value           |
      | en-US  | Just a nice mug |
    And product productWithBasic is not associated to shops "shop3,shop4"

  Scenario: I associate a pack to another shop, the product associated are in sync
    Given I add product "productPack" to shop shop1 with following information:
      | name[en-US] | weird sunglasses box |
      | type        | pack                 |
    And product "productPack" type should be pack for shop shop1
    And I add product "product5" to shop shop1 with following information:
      | name[en-US] | work sunglasses |
      | type        | standard        |
    And I add product "product6" to shop shop1 with following information:
      | name[en-US] | personal sunglasses |
      | type        | standard            |
    And I add product "product7" to shop shop1 with following information:
      | name[en-US] | casual sunglasses |
      | type        | standard          |
    When I update pack productPack with following product quantities:
      | product  | quantity |
      | product5 | 10       |
      | product6 | 11       |
      | product7 | 15       |
    Then pack "productPack" should contain products with following details for shops "shop1":
      | product  | combination | name                | quantity | image url                                              | reference |
      | product5 |             | work sunglasses     | 10       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product6 |             | personal sunglasses | 11       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product7 |             | casual sunglasses   | 15       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
    When I set following shops for product "productPack":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then pack "productPack" should contain products with following details for shops "shop1,shop2":
      | product  | combination | name                | quantity | image url                                              | reference |
      | product5 |             | work sunglasses     | 10       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product6 |             | personal sunglasses | 11       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product7 |             | casual sunglasses   | 15       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |

  Scenario: Stock data is copied when set new shop association for product
    # By default the product is created for default shop
    Given I add product "productWithStock" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product productWithStock is associated to shop shop1
    And default shop for product productWithStock is shop1
    # First modify data for default shop
    When I update product "productWithStock" with following values:
      | pack_stock_type               | pack_only    |
      | minimal_quantity              | 12           |
      | low_stock_threshold           | 42           |
      | available_now_labels[en-US]   | get it now   |
      | available_later_labels[en-US] | too late bro |
      | available_date                | 1969-07-16   |
    And I update product "productWithStock" stock with following information:
      | out_of_stock_type | available |
      | delta_quantity    | 42        |
      | location          | dtc       |
    Then product "productWithStock" should have following stock information for shops "shop1":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productWithStock" localized "available_now_labels" for shops "shop1" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productWithStock" localized "available_later_labels" for shops "shop1" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product productWithStock is not associated to shops "shop2,shop3,shop4"
    # I assign product to another shop which was not associated yet
    When I set following shops for product "productWithStock":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product productWithStock is associated to shops "shop1,shop2"
    And default shop for product productWithStock is shop1
    Then product "productWithStock" should have following stock information for shops "shop1,shop2":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productWithStock" localized "available_now_labels" for shops "shop1,shop2" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productWithStock" localized "available_later_labels" for shops "shop1,shop2" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product productWithStock is not associated to shops "shop3,shop4"

  Scenario: Customization fields are copied when I set new shop association for product
    When I add product "customizable_product" with following information:
      | name[en-US] | nice customizable t-shirt |
      | type        | standard                  |
    And product "customizable_product" type should be standard
    When I update product customizable_product with following customization fields:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField1 | text | front-text  | texte devant | true        |
      | customField2 | text | bottom-text | texte du bas | true        |
    Then product "customizable_product" should require customization
    And product customizable_product should have 2 customizable text fields
    And product customizable_product should have 0 customizable file fields
    And product customizable_product should have following customization fields:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField1 | text | front-text  | texte devant | true        |
      | customField2 | text | bottom-text | texte du bas | true        |
    When I set following shops for product "customizable_product":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And product customizable_product should have following customization fields for shops shop1,shop2:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField1 | text | front-text  | texte devant | true        |
      | customField2 | text | bottom-text | texte du bas | true        |

  Scenario: Image associations are copied when I set new shop association for product
    Given I add product "graphicProduct" with following information:
      | name[en-US] | funny mug |
      | type        | standard  |
    And I add new image "image1" named "app_icon.png" to product "graphicProduct" for shop "shop1"
    And I add new image "image2" named "some_image.jpg" to product "graphicProduct" for shop "shop1"
    When I set following shops for product "graphicProduct":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product "graphicProduct" should have following images for shop "shop1, shop2":
      | image reference | position | shops        |
      | image1          | 1        | shop1, shop2 |
      | image2          | 2        | shop1, shop2 |
    And following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |

  Scenario: I unassociate product from one shop the data are still present in the other shop
    # By default the product is created for default shop
    Given I add product "productToDelete" to shop "shop2" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    Then product productToDelete is associated to shop shop2
    And default shop for product productToDelete is shop2
    # First modify data for default shop
    When I update product "productToDelete" for shop shop2 with following values:
      | pack_stock_type               | pack_only    |
      | minimal_quantity              | 12           |
      | low_stock_threshold           | 42           |
      | available_now_labels[en-US]   | get it now   |
      | available_later_labels[en-US] | too late bro |
      | available_date                | 1969-07-16   |
    And I update product "productToDelete" stock for shop shop2 with following information:
      | out_of_stock_type | available |
      | delta_quantity    | 42        |
      | location          | dtc       |
    And I update product "productToDelete" for shop shop2 with following values:
      | name[en-US]              | photo of super mug2 |
      | description[en-US]       | super mug           |
      | description_short[en-US] | Just a super mug    |
    When I update product "productToDelete" for shop shop2 with following values:
      | price           | 100.99          |
      | ecotax          | 0               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 70              |
      | unit_price      | 10              |
      | unity           | bag of ten      |
    # Copy values to another shop which was not associated yet
    When I set following shops for product "productToDelete":
      | source shop | shop2             |
      | shops       | shop1,shop2,shop3 |
    # update product with different values for shops
    And I update product "productToDelete" for shop "shop1" with following values:
      | name[en-US] | photo of super mug1 |
    And I update product "productToDelete" for shop "shop3" with following values:
      | name[en-US] | photo of super mug3 |
    Then product productToDelete is associated to shops "shop1,shop2,shop3"
    And product productToDelete is not associated to shop shop4
    And default shop for product productToDelete is shop2
    And product "productToDelete" localized "name" for shops "shop1" should be:
      | locale | value               |
      | en-US  | photo of super mug1 |
    And product "productToDelete" localized "name" for shops "shop2" should be:
      | locale | value               |
      | en-US  | photo of super mug2 |
    And product "productToDelete" localized "name" for shops "shop3" should be:
      | locale | value               |
      | en-US  | photo of super mug3 |
    And product "productToDelete" localized "description" for shops "shop1,shop2,shop3" should be:
      | locale | value     |
      | en-US  | super mug |
    And product "productToDelete" localized "description_short" for shops "shop1,shop2,shop3" should be:
      | locale | value            |
      | en-US  | Just a super mug |
    Then product "productToDelete" should have following stock information for shops "shop1,shop2,shop3":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productToDelete" localized "available_now_labels" for shops "shop1,shop2,shop3" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productToDelete" localized "available_later_labels" for shops "shop1,shop2,shop3" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product "productToDelete" should have following stock information for shops "shop1,shop2,shop3":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productToDelete" localized "available_now_labels" for shops "shop1,shop2,shop3" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productToDelete" localized "available_later_labels" for shops "shop1,shop2,shop3" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    Then product productToDelete should have following prices information for shops "shop1,shop2,shop3":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    # Now I unasociate product from its default shop, a new default shop is assigned
    When I set following shops for product "productToDelete":
      | source shop | shop1       |
      | shops       | shop1,shop3 |
    Then product productToDelete is associated to shops "shop1,shop3"
    And product productToDelete is not associated to shops "shop2,shop4"
    And default shop for product productToDelete is shop1
    # Check that values are still present for other shops and that they were not altered
    And product "productToDelete" localized "name" for shops "shop1" should be:
      | locale | value               |
      | en-US  | photo of super mug1 |
    And product "productToDelete" localized "name" for shops "shop3" should be:
      | locale | value               |
      | en-US  | photo of super mug3 |
    And product "productToDelete" localized "description" for shops "shop1,shop3" should be:
      | locale | value     |
      | en-US  | super mug |
    And product "productToDelete" localized "description_short" for shops "shop1,shop3" should be:
      | locale | value            |
      | en-US  | Just a super mug |
    Then product "productToDelete" should have following stock information for shops "shop1,shop3":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productToDelete" localized "available_now_labels" for shops "shop1,shop3" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productToDelete" localized "available_later_labels" for shops "shop1,shop3" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product "productToDelete" should have following stock information for shops "shop1,shop3":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productToDelete" localized "available_now_labels" for shops "shop1,shop3" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productToDelete" localized "available_later_labels" for shops "shop1,shop3" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    Then product productToDelete should have following prices information for shops "shop1,shop3":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    # Now I delete product from remaining shops it should be completely removed
    When I delete product productToDelete from shops "shop1,shop3"
    Then product productToDelete should not exist anymore

  Scenario: Product combinations are copied/deleted when product is being copied/deleted to/from shop.
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [L]                |
      | Color | [White,Black,Blue] |
    And product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | false      |
    And product "product1" is not associated to shop shop2
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product "product1" should have the following combinations for shops "shop1,shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | false      |
    And product "product1" is associated to shop shop2
    And combinations "product1LWhite,product1LBlack,product1LBlue" are associated to shop "shop2"
    When I delete product "product1" from shops "shop2"
    Then product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | false      |
    And product "product1" is not associated to shop shop2
    And combinations "product1LWhite,product1LBlack,product1LBlue" are not associated to shop "shop2"

  Scenario: Product images are copied/deleted when product is being copied/deleted to/from shop.
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | standard          |
    And product product1 type should be standard
    And product "product1" should have no images
    And product "product1" is not associated to shop "shop2"
    When I add new image "image1" named "app_icon.png" to product "product1" for shop "shop1"
    Then product "product1" should have following images for shops "shop1":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      | shops |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1 |
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product "product1" should have following images for shops "shop1, shop2":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      | shops        |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1, shop2 |
    When I delete product "product1" from shops "shop2"
    Then product "product1" is not associated to shop "shop2"
    And product "product1" should have following images for shops "shop1":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      | shops |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1 |
    And I try to get product "product1" images for shop "shop2"
    And I should get error that shop association was not found
    # copy product again to make sure there are no images left from the previous product, therefore no uniqueConstraint errors
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product "product1" should have following images for shops "shop1, shop2":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      | shops        |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1, shop2 |

  Scenario: Throws error when trying to reach images for shops which are not associated to product
    Given I add product "product2" to shop "shop2" with following information:
      | name[en-US] | magic staff2 |
      | type        | standard     |
    And product "product2" is not associated to shop shop3
    When I try to get product "product2" images for shop "shop3"
    Then I should get error that shop association was not found

  Scenario: I should not be able to unassociate product from all shops
    Given I add product "product" to shop "shop1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product "product" is associated to shop "shop1"
    When I set following shops for product "product":
      | source shop | shop1 |
      | shops       |       |
    Then I should get error that I cannot unassociate product from all shops
    And product "product" is associated to shop "shop1"

  Scenario: I should not be able to unassociate product from source shop
    Given I add product "product" to shop "shop1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product "product" is associated to shop "shop1"
    When I set following shops for product "product":
      | source shop | shop1 |
      | shops       | shop2 |
    Then I should get error that I cannot unassociate product from source shop
    And product "product" is associated to shop "shop1"

  Scenario: I should not be able to provide a source shop which is not associated to product
    Given I add product "product" to shop "shop1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product "product" is associated to shop "shop1"
    And product "product" is not associated to shop "shop2"
    When I set following shops for product "product":
      | source shop | shop2       |
      | shops       | shop1,shop2 |
    Then I should get error that source shop is not associated to product
    And product "product" is associated to shop "shop1"
