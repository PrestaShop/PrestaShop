# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-management
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multi-shop
@update-multi-shop-management
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
    And product createdProduct is not associated to shop shop1
    And product createdProduct is not associated to shop shop3
    And product createdProduct is not associated to shop shop4
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

  Scenario: I copy product to another shop that was not associated, prices are copied
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
    And product productWithPrices is not associated to shop shop2
    And product productWithPrices is not associated to shop shop3
    And product productWithPrices is not associated to shop shop4
    # Copy values to another shop which was not associated yet
    When I copy product productWithPrices from shop shop1 to shop shop2
    Then product productWithPrices is associated to shop shop2
    And product productWithPrices is associated to shop shop1
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
    And product productWithPrices is not associated to shop shop3
    And product productWithPrices is not associated to shop shop4
    # Now modify and copy the values but this time the shop is already associated so it is an update
    When I update product "productWithPrices" with following values:
      | price           | 200.99            |
      | ecotax          | 2                 |
      | tax rules group | US-AZ Rate (6.6%) |
      | on_sale         | false             |
      | wholesale_price | 60                |
      | unit_price      | 20                |
      | unity           | bag of twenty     |
    Then product productWithPrices should have following prices information for shops "shop1":
      | price              | 200.99            |
      | price_tax_included | 214.25534         |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
      | unit_price_ratio   | 10.0495           |
    But product productWithPrices should have following prices information for shops "shop2":
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 10              |
      | unity              | bag of ten      |
      | unit_price_ratio   | 10.099          |
    # Copy values to a shop which is already associated
    When I copy product productWithPrices from shop shop1 to shop shop2
    Then product productWithPrices is associated to shop shop2
    And product productWithPrices should have following prices information for shops "shop1,shop2":
      | price              | 200.99            |
      | price_tax_included | 214.25534         |
      | ecotax             | 2                 |
      | tax rules group    | US-AZ Rate (6.6%) |
      | on_sale            | false             |
      | wholesale_price    | 60                |
      | unit_price         | 20                |
      | unity              | bag of twenty     |
      | unit_price_ratio   | 10.0495           |
    And product productWithPrices is not associated to shop shop3
    And product productWithPrices is not associated to shop shop4

  Scenario: I copy product to another shop that was not associated, basic information are copied
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
    And product productWithBasic is not associated to shop shop2
    And product productWithBasic is not associated to shop shop3
    And product productWithBasic is not associated to shop shop4
    # Copy values to another shop which was not associated yet
    When I copy product productWithBasic from shop shop1 to shop shop2
    Then product productWithBasic is associated to shop shop2
    And product productWithBasic is associated to shop shop1
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
    And product productWithBasic is not associated to shop shop3
    And product productWithBasic is not associated to shop shop4
    # Now modify and copy the values but this time the shop is already associated so it is an update
    When I update product "productWithBasic" with following values:
      | name[en-US]              | photo of super mug |
      | description[en-US]       | super mug          |
      | description_short[en-US] | Just a super mug   |
    Then product "productWithBasic" localized "name" for shops "shop1" should be:
      | locale | value              |
      | en-US  | photo of super mug |
    And product "productWithBasic" localized "description" for shops "shop1" should be:
      | locale | value     |
      | en-US  | super mug |
    And product "productWithBasic" localized "description_short" for shops "shop1" should be:
      | locale | value            |
      | en-US  | Just a super mug |
    But product "productWithBasic" localized "name" for shops "shop2" should be:
      | locale | value              |
      | en-US  | photo of funny mug |
    And product "productWithBasic" localized "description" for shops "shop2" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "productWithBasic" localized "description_short" for shops "shop2" should be:
      | locale | value           |
      | en-US  | Just a nice mug |
    # Copy values to a shop which is already associated
    When I copy product productWithBasic from shop shop1 to shop shop2
    Then product productWithBasic is associated to shop shop2
    And product "productWithBasic" localized "name" for shops "shop1,shop2" should be:
      | locale | value              |
      | en-US  | photo of super mug |
    And product "productWithBasic" localized "description" for shops "shop1,shop2" should be:
      | locale | value     |
      | en-US  | super mug |
    And product "productWithBasic" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value            |
      | en-US  | Just a super mug |
    And product productWithBasic is not associated to shop shop3
    And product productWithBasic is not associated to shop shop4

  Scenario: I copy a pack to another shop, the product associated are in sync
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
    When I copy product productWithBasic from shop shop1 to shop shop2
    Then pack "productPack" should contain products with following details for shops "shop1,shop2":
      | product  | combination | name                | quantity | image url                                              | reference |
      | product5 |             | work sunglasses     | 10       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product6 |             | personal sunglasses | 11       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |
      | product7 |             | casual sunglasses   | 15       | http://myshop.com/img/p/{no_picture}-small_default.jpg |           |


  Scenario: I copy product to another shop that was not associated, stock data are copied
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
    And product productWithStock is not associated to shop shop2
    And product productWithStock is not associated to shop shop3
    And product productWithStock is not associated to shop shop4
    # Copy values to another shop which was not associated yet
    When I copy product productWithStock from shop shop1 to shop shop2
    Then product productWithStock is associated to shop shop2
    And product productWithStock is associated to shop shop1
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
    And product productWithStock is not associated to shop shop3
    And product productWithStock is not associated to shop shop4
    # Now modify and copy the values but this time the shop is already associated so it is an update
    When I update product "productWithStock" for shop shop1 with following values:
      | pack_stock_type               | products_only |
      | minimal_quantity              | 24            |
      | low_stock_threshold           | 0             |
      | available_now_labels[en-US]   | hurry up      |
      | available_later_labels[en-US] | too slow...   |
      | available_date                | 1969-09-16    |
    When I update product "productWithStock" stock for shop shop1 with following information:
      | out_of_stock_type | not_available |
      | delta_quantity    | 69            |
      | location          | upa           |
    # First only one shop is updated
    Then product "productWithStock" should have following stock information for shops "shop1":
      | pack_stock_type     | products_only |
      | out_of_stock_type   | not_available |
      | quantity            | 111           |
      | minimal_quantity    | 24            |
      | location            | upa           |
      | low_stock_threshold | 0             |
      | low_stock_alert     | false         |
      | available_date      | 1969-09-16    |
    And product "productWithStock" localized "available_now_labels" for shops "shop1" should be:
      | locale | value    |
      | en-US  | hurry up |
      | fr-FR  |          |
    And product "productWithStock" localized "available_later_labels" for shops "shop1" should be:
      | locale | value       |
      | en-US  | too slow... |
      | fr-FR  |             |
    But product "productWithStock" should have following stock information for shops "shop2":
      | pack_stock_type     | pack_only  |
      | out_of_stock_type   | available  |
      | quantity            | 42         |
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "productWithStock" localized "available_now_labels" for shops "shop2" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "productWithStock" localized "available_later_labels" for shops "shop2" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    And product productWithStock is not associated to shop shop3
    And product productWithStock is not associated to shop shop4
    # Now copy new values to the other shop
    When I copy product productWithStock from shop shop1 to shop shop2
    Then product "productWithStock" should have following stock information for shops "shop1,shop2":
      | pack_stock_type     | products_only |
      | out_of_stock_type   | not_available |
      | quantity            | 111           |
      | minimal_quantity    | 24            |
      | location            | upa           |
      | low_stock_threshold | 0             |
      | low_stock_alert     | false         |
      | available_date      | 1969-09-16    |
    And product "productWithStock" localized "available_now_labels" for shops "shop1,shop2" should be:
      | locale | value    |
      | en-US  | hurry up |
      | fr-FR  |          |
    And product "productWithStock" localized "available_later_labels" for shops "shop1,shop2" should be:
      | locale | value       |
      | en-US  | too slow... |
      | fr-FR  |             |

  Scenario: I copy product to another shop that was not associated, customization fields are copied
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
    And I copy product customizable_product from shop shop1 to shop shop2
    And product customizable_product should have following customization fields for shops shop1,shop2:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField1 | text | front-text  | texte devant | true        |
      | customField2 | text | bottom-text | texte du bas | true        |

  Scenario: I copy product to another shop that was not associated, image associations are copied
    Given I add product "graphicProduct" with following information:
      | name[en-US] | funny mug |
      | type        | standard  |
    And I add new image "image1" named "app_icon.png" to product "graphicProduct" for shop "shop1"
    And I add new image "image2" named "some_image.jpg" to product "graphicProduct" for shop "shop1"
    And I copy product graphicProduct from shop shop1 to shop shop2
    Then product "graphicProduct" should have following images for shop "shop1":
      | image reference | position | shops        |
      | image1          | 1        | shop1, shop2 |
      | image2          | 2        | shop1, shop2 |
    And product "graphicProduct" should have following images for shop "shop2":
      | image reference | position | shops        |
      | image1          | 1        | shop1, shop2 |
      | image2          | 2        | shop1, shop2 |
    And product "graphicProduct" should have following images for shop "shop3":
      | image reference | position | shops |
    And product "graphicProduct" should have following images for shop "shop4":
      | image reference | position | shops |
    And following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |

  Scenario: I delete product from one shop the data are still present in the other shop
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
      | name[en-US]              | photo of super mug |
      | description[en-US]       | super mug          |
      | description_short[en-US] | Just a super mug   |
    When I update product "productToDelete" for shop shop2 with following values:
      | price           | 100.99          |
      | ecotax          | 0               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 70              |
      | unit_price      | 10              |
      | unity           | bag of ten      |
    # Copy values to another shop which was not associated yet
    When I copy product productToDelete from shop shop2 to shop shop1
    And I copy product productToDelete from shop shop2 to shop shop3
    Then product productToDelete is associated to shop shop1
    And product productToDelete is associated to shop shop2
    And product productToDelete is associated to shop shop3
    And product productToDelete is not associated to shop shop4
    And default shop for product productToDelete is shop2
    And product "productToDelete" localized "name" for shops "shop1,shop2,shop3" should be:
      | locale | value              |
      | en-US  | photo of super mug |
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
    # Now I delete product from its default shop, a new default shop is assigned
    When I delete product productToDelete from shops "shop2"
    Then product productToDelete is associated to shop shop1
    And product productToDelete is associated to shop shop3
    And product productToDelete is not associated to shop shop2
    And product productToDelete is not associated to shop shop4
    And default shop for product productToDelete is shop1
    # Check that values are still present for other shops
    And product "productToDelete" localized "name" for shops "shop1,shop3" should be:
      | locale | value              |
      | en-US  | photo of super mug |
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
    And product "product1" should have no combinations for shops "shop2"
    When I copy product "product1" from shop shop1 to shop shop2
    Then product "product1" should have the following combinations for shops "shop1,shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | false      |
    When I delete product "product1" from shops "shop2"
    Then product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1LWhite | Size - L, Color - White |           | [Size:L,Color:White] | 0               | 0        | true       |
      | product1LBlack | Size - L, Color - Black |           | [Size:L,Color:Black] | 0               | 0        | false      |
      | product1LBlue  | Size - L, Color - Blue  |           | [Size:L,Color:Blue]  | 0               | 0        | false      |
    And product "product1" should have no combinations for shops "shop2"

  Scenario: Product images are copied/deleted when product is being copied/deleted to/from shop.
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | standard          |
    And product product1 type should be standard
    And product "product1" should have no images for shops "shop1"
    And product "product1" is not associated to shop "shop2"
    When I add new image "image1" named "app_icon.png" to product "product1" for shop "shop1"
    Then product "product1" should have following images for shops "shop1":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
    When I copy product "product1" from shop "shop1" to shop "shop2"
    Then product "product1" should have following images for shops "shop1,shop2":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
    When I delete product "product1" from shops "shop2"
    Then product "product1" should have no images for shops "shop2"
    And product "product1" is not associated to shop "shop2"
    And product "product1" should have following images for shops "shop1":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
    # copy product again to make sure there are no images left from the previous product, therefore no uniqueConstraint errors
    When I copy product "product1" from shop "shop1" to shop "shop2"
    Then product "product1" should have following images for shops "shop1,shop2":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
