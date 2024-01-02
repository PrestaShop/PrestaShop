# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags duplicate-product
@restore-products-before-feature
@restore-languages-after-feature
@restore-taxes-after-feature
@reset-downloads-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@duplicate-product
Feature: Duplicate product from Back Office (BO).
  As an employee I want to be able to duplicate product

  Background:
    Given manufacturer studioDesign named "Studio Design" exists
    And language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    And carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "Red" named "Red" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Pink" named "Pink" in en language exists
    And shop "shop1" with name "test_shop" exists
    And single shop shop1 context is loaded

  Scenario: I duplicate a product all its direct fields are correctly copied
    When I add product "productWithFields" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I add product "productForRedirection" with following information:
      | name[en-US] | dumb sunglasses   |
      | name[fr-FR] | lunettes de nuage |
      | type        | standard          |
    And I update product "productWithFields" with following values:
      | description[en-US]                      | nice sunglasses            |
      | description[fr-FR]                      | belles lunettes            |
      | description_short[en-US]                | Simple & nice sunglasses   |
      | description_short[fr-FR]                | lunettes simples et belles |
      | visibility                              | catalog                    |
      | available_for_order                     | false                      |
      | online_only                             | true                       |
      | show_price                              | false                      |
      | condition                               | used                       |
      | manufacturer                            | studioDesign               |
      | isbn                                    | 978-3-16-148410-0          |
      | upc                                     | 72527273070                |
      | ean13                                   | 978020137962               |
      | mpn                                     | mpn1                       |
      | reference                               | ref1                       |
      | price                                   | 100.00                     |
      | ecotax                                  | 0                          |
      | tax rules group                         | US-AL Rate (4%)            |
      | on_sale                                 | true                       |
      | wholesale_price                         | 70                         |
      | unit_price                              | 500                        |
      | unity                                   | bag of ten                 |
      | meta_title[en-US]                       | SUNGLASSES meta title      |
      | meta_description[en-US]                 | Its so smart               |
      | meta_description[fr-FR]                 | lel joke                   |
      | link_rewrite[en-US]                     | smart-sunglasses           |
      | link_rewrite[fr-FR]                     | lunettes-de-soleil         |
      | redirect_type                           | 301-product                |
      | redirect_target                         | productForRedirection      |
      | width                                   | 10.5                       |
      | height                                  | 6                          |
      | depth                                   | 7                          |
      | weight                                  | 0.5                        |
      | additional_shipping_cost                | 12                         |
      | delivery time notes type                | specific                   |
      | delivery time in stock notes[en-US]     | product in stock           |
      | delivery time in stock notes[fr-FR]     | en stock                   |
      | delivery time out of stock notes[en-US] | product out of stock       |
      | delivery time out of stock notes[fr-FR] | En rupture de stock        |
      | active                                  | true                       |
    When I duplicate product productWithFields to a productWithFieldsCopy
    # Even if the initial product was active the copy is disabled by default
    Then product "productWithFieldsCopy" should be disabled
    And product "productWithFieldsCopy" type should be standard
    And product "productWithFieldsCopy" localized "name" should be:
      | locale | value                       |
      | en-US  | copy of smart sunglasses    |
      | fr-FR  | copie de lunettes de soleil |
    And product "productWithFieldsCopy" localized "description" should be:
      | locale | value           |
      | en-US  | nice sunglasses |
      | fr-FR  | belles lunettes |
    And product "productWithFieldsCopy" localized "description_short" should be:
      | locale | value                      |
      | en-US  | Simple & nice sunglasses   |
      | fr-FR  | lunettes simples et belles |
    And product "productWithFieldsCopy" should have following options:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "productWithFieldsCopy" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    And product productWithFieldsCopy should have following prices information:
      | price                   | 100.00          |
      | ecotax                  | 0               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 500             |
      | unit_price_tax_included | 520             |
      | unit_price_ratio        | 0.2             |
      | unity                   | bag of ten      |
    And product "productWithFieldsCopy" localized "meta_title" should be:
      | locale | value                 |
      | en-US  | SUNGLASSES meta title |
    And product "productWithFieldsCopy" localized "meta_description" should be:
      | locale | value        |
      | en-US  | Its so smart |
      | fr-FR  | lel joke     |
    And product "productWithFieldsCopy" localized "link_rewrite" should be:
      | locale | value              |
      | en-US  | smart-sunglasses   |
      | fr-FR  | lunettes-de-soleil |
    And product productWithFieldsCopy should have following seo options:
      | redirect_type   | 301-product           |
      | redirect_target | productForRedirection |
    And product "productWithFieldsCopy" should have following shipping information:
      | width                                   | 10.5                 |
      | height                                  | 6                    |
      | depth                                   | 7                    |
      | weight                                  | 0.5                  |
      | additional_shipping_cost                | 12                   |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time in stock notes[fr-FR]     | en stock             |
      | delivery time out of stock notes[en-US] | product out of stock |
      | delivery time out of stock notes[fr-FR] | En rupture de stock  |
      | carriers                                | []                   |
    And productWithFields and productWithFieldsCopy have different values

  Scenario: I duplicate a product its stock is copied
    When I add product "productWithStock" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    When I update product "productWithStock" stock with following information:
      | delta_quantity | 51  |
      | location       | dtc |
    And I update product "productWithStock" stock with following information:
      | delta_quantity | -9 |
    Then product "productWithStock" should have following stock information:
      | quantity | 42  |
      | location | dtc |
    And product "productWithStock" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | -9             |
      | Puff Daddy | 51             |
    And product "productWithStock" last stock movement decreased by 9
    When I duplicate product productWithStock to a productWithStockCopy
    Then product "productWithStockCopy" should have following stock information:
      | quantity | 42  |
      | location | dtc |
    And product "productWithStockCopy" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 42             |

  Scenario: I duplicate a product with combinations their stock are copied
    When I add product "productWithCombinationAndStock" with following information:
      | name[en-US] | Jar of sand  |
      | type        | combinations |
    And I generate combinations for product productWithCombinationAndStock using following attributes:
      | Color | [Red,Blue] |
    Then product "productWithCombinationAndStock" should have following combinations:
      | id reference                | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRed  | Color - Red      |           | [Color:Red]  | 0               | 0        | true       |
      | productWithCombinationsBlue | Color - Blue     |           | [Color:Blue] | 0               | 0        | false      |
    And I update combination "productWithCombinationsRed" stock with following details:
      | delta quantity | 100         |
      | location       | Storage nr1 |
    And I update combination "productWithCombinationsRed" stock with following details:
      | delta quantity | -40 |
    And I update combination "productWithCombinationsBlue" stock with following details:
      | delta quantity | 50          |
      | location       | Storage nr1 |
    And I update combination "productWithCombinationsBlue" stock with following details:
      | delta quantity | 20          |
      | location       | Storage nr2 |
    When I duplicate product productWithCombinationAndStock to a productWithCombinationAndStockCopy
    Then product "productWithCombinationAndStockCopy" should have following combinations:
      | id reference                    | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRedCopy  | Color - Red      |           | [Color:Red]  | 0               | 60       | true       |
      | productWithCombinationsBlueCopy | Color - Blue     |           | [Color:Blue] | 0               | 70       | false      |
    And productWithCombinationsRed and productWithCombinationsRedCopy have different values
    And productWithCombinationsBlue and productWithCombinationsBlueCopy have different values
    And combination "productWithCombinationsRedCopy" should have following stock details:
      | combination stock detail   | value       |
      | quantity                   | 60          |
      | location                   | Storage nr1 |
      | minimal quantity           | 1           |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
      | available date             |             |
    And combination "productWithCombinationsRedCopy" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 60             |
    And combination "productWithCombinationsBlueCopy" should have following stock details:
      | combination stock detail   | value       |
      | quantity                   | 70          |
      | location                   | Storage nr2 |
      | minimal quantity           | 1           |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
      | available date             |             |
    And combination "productWithCombinationsBlueCopy" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 70             |

  Scenario: I duplicate a product all its categories are correctly copied
    Given category "home" in default language named "Home" exists
    And category "home" is the default one
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    And I add product "productWithCategories" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I assign product productWithCategories to following categories:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    When I duplicate product productWithCategories to a productWithCategoriesCopy
    Then product productWithCategoriesCopy should be assigned to following categories:
      | id reference | name    | is default |
      | home         | Home    | false      |
      | men          | Men     | false      |
      | clothes      | Clothes | true       |

  Scenario: I duplicate a product its tags are copied
    Given I add product "productWithTags" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I update product "productWithTags" tags with following values:
      | tags[en-US] | smart,glasses,sunglasses,men |
      | tags[fr-FR] | lunettes,bien,soleil         |
    When I duplicate product productWithTags to a productWithTagsCopy
    Then product "productWithTagsCopy" localized "tags" should be:
      | locale | value                        |
      | en-US  | smart,glasses,sunglasses,men |
      | fr-FR  | lunettes,bien,soleil         |

  Scenario: I duplicate a product its related products are copied
    Given I add product "productWithRelations" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I add product "relatedProduct" with following information:
      | name[en-US] | Reading glasses |
      | name[fr-FR] | lunettes        |
      | type        | standard        |
    And I set following related products to product productWithRelations:
      | relatedProduct |
    When I duplicate product productWithRelations to a productWithRelationsCopy
    And product productWithRelationsCopy should have following related products:
      | product        | name            | reference | image url                                             |
      | relatedProduct | Reading glasses |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: I duplicate a product its carriers are copied
    Given I add product "productWithCarriers" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I assign product productWithCarriers with following carriers:
      | carrier1 |
      | carrier2 |
    When I duplicate product productWithCarriers to a productWithCarriersCopy
    And product "productWithCarriersCopy" should have following shipping information:
      | width                                   | 0                   |
      | height                                  | 0                   |
      | depth                                   | 0                   |
      | weight                                  | 0                   |
      | additional_shipping_cost                | 0                   |
      | delivery time notes type                | default             |
      | delivery time in stock notes[en-US]     |                     |
      | delivery time in stock notes[fr-FR]     |                     |
      | delivery time out of stock notes[en-US] |                     |
      | delivery time out of stock notes[fr-FR] |                     |
      | carriers                                | [carrier1,carrier2] |

  Scenario: I duplicate a product its customization fields are copied
    Given I add product "productWithCustomizations" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I update product productWithCustomizations with following customization fields:
      | reference    | type | name[en-US]                 | name[fr-FR]                         | is required |
      | customField1 | text | text on top of left lense   | texte en haut de la lentille gauche | true        |
      | customField2 | file | image on top of right lense | image en haut de la lentille droite | false       |
    Then product productWithCustomizations should have following customization fields:
      | reference    | type | name[en-US]                 | name[fr-FR]                         | is required |
      | customField1 | text | text on top of left lense   | texte en haut de la lentille gauche | true        |
      | customField2 | file | image on top of right lense | image en haut de la lentille droite | false       |
    When I duplicate product productWithCustomizations to a productWithCustomizationsCopy
    Then product productWithCustomizationsCopy should have 1 customizable text field
    And product productWithCustomizationsCopy should have 1 customizable file field
    And product productWithCustomizationsCopy should have following customization fields:
      | new reference    | type | name[en-US]                 | name[fr-FR]                         | is required |
      | customField1Copy | text | text on top of left lense   | texte en haut de la lentille gauche | true        |
      | customField2Copy | file | image on top of right lense | image en haut de la lentille droite | false       |
    And customField1 and customField1Copy have different values
    And customField2 and customField2Copy have different values

  Scenario: I duplicate a product its attachments are copied
    Given I add product "productWithAttachments" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I add new attachment "att1" with following properties:
      | description[en-US] | puffin photo nr1 |
      | description[fr-FR] | macareux         |
      | name[en-US]        | puffin           |
      | name[fr-FR]        | macareux         |
      | file_name          | app_icon.png     |
    And I associate product productWithAttachments with following attachments: "[att1]"
    When I duplicate product productWithAttachments to a productWithAttachmentsCopy
    # Product is duplicated but both are related to the same attachment
    And product productWithAttachmentsCopy should have following attachments associated:
      | attachment reference | title                       | description                           | file name    | type      | size  |
      | att1                 | en-US:puffin;fr-Fr:macareux | en-US:puffin photo nr1;fr-Fr:macareux | app_icon.png | image/png | 19187 |

  Scenario: I duplicate a product its specific prices and priorities are copied
    Given I add product "productWithSpecificPrices" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And default specific price priorities are set to following:
      | priorities  |
      | id_group    |
      | id_currency |
      | id_country  |
      | id_shop     |
    And I add a specific price price1 to product productWithSpecificPrices with following details:
      | fixed price     | 0.00   |
      | reduction type  | amount |
      | reduction value | 5.00   |
      | includes tax    | true   |
      | from quantity   | 1      |
    And I set following custom specific price priorities for product productWithSpecificPrices:
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
    When I duplicate product productWithSpecificPrices to a productWithSpecificPricesCopy
    And product "productWithSpecificPricesCopy" should have 1 specific prices
    Then product "productWithSpecificPricesCopy" should have following list of specific prices in "en" language:
      | id reference | combination | reduction type | reduction value | includes tax | fixed price | from quantity | shop | currency | currencyISOCode | country | group | customer | from                | to                  |
      | price1Copy   |             | amount         | 5.0             | true         | 0.0         | 1             |      |          |                 |         |       |          | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And specific price price1 should have following details:
      | specific price detail | value                     |
      | reduction type        | amount                    |
      | reduction value       | 5.0                       |
      | includes tax          | true                      |
      | fixed price           | 0.0                       |
      | from quantity         | 1                         |
      | from                  | 0000-00-00 00:00:00       |
      | to                    | 0000-00-00 00:00:00       |
      | shop                  |                           |
      | currency              |                           |
      | country               |                           |
      | group                 |                           |
      | customer              |                           |
      | product               | productWithSpecificPrices |
    And specific price price1Copy should have following details:
      | specific price detail | value                         |
      | reduction type        | amount                        |
      | reduction value       | 5.0                           |
      | includes tax          | true                          |
      | fixed price           | 0.0                           |
      | from quantity         | 1                             |
      | from                  | 0000-00-00 00:00:00           |
      | to                    | 0000-00-00 00:00:00           |
      | shop                  |                               |
      | currency              |                               |
      | country               |                               |
      | group                 |                               |
      | customer              |                               |
      | product               | productWithSpecificPricesCopy |
    And price1 and price1Copy have different values
    And product productWithSpecificPricesCopy should have following custom specific price priorities:
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
    And following specific price priorities should be used for product productWithSpecificPricesCopy:
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |

  Scenario: I duplicate a product its suppliers are copied
    Given I add product "productWithSuppliers" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I add new supplier supplier1 with the following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,1              |
      | shops                   | [shop1]            |
    When I associate suppliers to product "productWithSuppliers"
      | supplier  | product_supplier  |
      | supplier1 | product1supplier1 |
    And I update product productWithSuppliers suppliers:
      | supplier  | reference                      | currency | price_tax_excluded |
      | supplier1 | my first supplier for product1 | USD      | 10                 |
    When I duplicate product productWithSuppliers to a productWithSuppliersCopy
    And product productWithSuppliersCopy should have following suppliers:
      | supplier  | reference                      | currency | price_tax_excluded |
      | supplier1 | my first supplier for product1 | USD      | 10                 |
    # assign reference to new product supplier and check that they are not equal

  Scenario: I duplicate product with combinations
    When I add product productWithCombinations with following information:
      | name[en-US] | Jar of sand  |
      | type        | combinations |
    And I generate combinations for product productWithCombinations using following attributes:
      | Color | [Red,Blue] |
    Then product "productWithCombinations" should have following combinations:
      | id reference                | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRed  | Color - Red      |           | [Color:Red]  | 0               | 0        | true       |
      | productWithCombinationsBlue | Color - Blue     |           | [Color:Blue] | 0               | 0        | false      |
    When I add a specific price price2 to product productWithCombinations with following details:
      | fixed price     | 123.00                     |
      | combination     | productWithCombinationsRed |
      | reduction type  | amount                     |
      | reduction value | 0.00                       |
      | includes tax    | true                       |
      | from quantity   | 1                          |
    And I add a specific price price3 to product productWithCombinations with following details:
      | fixed price     | 0.00   |
      | reduction type  | amount |
      | reduction value | 5.00   |
      | includes tax    | true   |
      | from quantity   | 1      |
    Then product "productWithCombinations" should have 2 specific prices
    And product "productWithCombinations" should have following list of specific prices in "en" language:
      | price id | combination | reduction type | reduction value | includes tax | fixed price | from quantity | shop | currency | currencyISOCode | country | group | customer | from                | to                  |
      | price2   | Color - Red | amount         | 0.0             | true         | 123.00      | 1             |      |          |                 |         |       |          | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | price3   |             | amount         | 5.0             | true         | 0.00        | 1             |      |          |                 |         |       |          | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    When I duplicate product productWithCombinations to a productWithCombinationsCopy
    Then product productWithCombinationsCopy type should be combinations
    And product "productWithCombinationsCopy" should be disabled
    And product "productWithCombinationsCopy" should have following combinations:
      | id reference                    | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsCopyRed  | Color - Red      |           | [Color:Red]  | 0               | 0        | true       |
      | productWithCombinationsCopyBlue | Color - Blue     |           | [Color:Blue] | 0               | 0        | false      |
    And productWithCombinations and productWithCombinationsCopy have different values
    And productWithCombinationsRed and productWithCombinationsCopyRed have different values
    And productWithCombinationsBlue and productWithCombinationsCopyBlue have different values
    Then product "productWithCombinationsCopy" should have 2 specific prices
    And product "productWithCombinationsCopy" should have following list of specific prices in "en" language:
      | id reference | combination | reduction type | reduction value | includes tax | fixed price | from quantity | shop | currency | currencyISOCode | country | group | customer | from                | to                  |
      | price2Copy   | Color - Red | amount         | 0.0             | true         | 123.00      | 1             |      |          |                 |         |       |          | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | price3Copy   |             | amount         | 5.0             | true         | 0.00        | 1             |      |          |                 |         |       |          | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And specific price price2Copy should have following details:
      | specific price detail | value                          |
      | reduction type        | amount                         |
      | reduction value       | 0.0                            |
      | includes tax          | true                           |
      | fixed price           | 123.0                          |
      | from quantity         | 1                              |
      | from                  | 0000-00-00 00:00:00            |
      | to                    | 0000-00-00 00:00:00            |
      | shop                  |                                |
      | currency              |                                |
      | country               |                                |
      | group                 |                                |
      | customer              |                                |
      | product               | productWithCombinationsCopy    |
      | combination           | productWithCombinationsCopyRed |
    And specific price price3Copy should have following details:
      | specific price detail | value                       |
      | reduction type        | amount                      |
      | reduction value       | 5.0                         |
      | includes tax          | true                        |
      | fixed price           | 0.0                         |
      | from quantity         | 1                           |
      | from                  | 0000-00-00 00:00:00         |
      | to                    | 0000-00-00 00:00:00         |
      | shop                  |                             |
      | currency              |                             |
      | country               |                             |
      | group                 |                             |
      | customer              |                             |
      | product               | productWithCombinationsCopy |
    And price2 and price2Copy have different values
    And price3 and price3Copy have different values

  Scenario: I duplicate packed product
    Given I add product "packedProduct" with following information:
      | name[en-US] | packed product   |
      | name[fr-FR] | pack de produits |
      | type        | pack             |
    And I add product "subProduct1" with following information:
      | name[en-US] | sub product 1  |
      | name[fr-FR] | sous produit 1 |
      | type        | standard       |
    And I add product "subProduct2" with following information:
      | name[en-US] | sub product 2  |
      | name[fr-FR] | sous produit 2 |
      | type        | combinations   |
    And I generate combinations for product subProduct2 using following attributes:
      | Color | [Red,Blue] |
    And product "subProduct2" should have following combinations:
      | id reference    | combination name | reference | attributes   | impact on price | quantity | is default |
      | subProduct2Red  | Color - Red      |           | [Color:Red]  | 0               | 0        | true       |
      | subProduct2Blue | Color - Blue     |           | [Color:Blue] | 0               | 0        | false      |
    And I update pack "packedProduct" with following product quantities:
      | product     | combination     | quantity |
      | subProduct1 |                 | 2        |
      | subProduct2 | subProduct2Blue | 3        |
    When I duplicate product packedProduct to a packedProductCopy
    And product "packedProductCopy" should be disabled
    And product "packedProductCopy" type should be pack
    And product "packedProductCopy" localized "name" should be:
      | locale | value                     |
      | en-US  | copy of packed product    |
      | fr-FR  | copie de pack de produits |
    And pack packedProductCopy should contain products with following details:
      | product     | combination     | name                        | quantity | image url                                              |
      | subProduct1 |                 | sub product 1               | 2        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | subProduct2 | subProduct2Blue | sub product 2: Color - Blue | 3        | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    And packedProduct and packedProductCopy have different values

  Scenario: I duplicate virtual product its file should be copied
    Given I add product "virtualProduct" with following information:
      | name[en-US] | puffin icon |
      | type        | virtual     |
    When I add virtual product file "file1" to product "virtualProduct" with following details:
      | display name | puffin-logo.png |
      | file name    | app_icon.png    |
    Then product "virtualProduct" should have a virtual product file "file1" with following details:
      | display name         | puffin-logo.png |
      | access days          | 0               |
      | download times limit | 0               |
      | expiration date      |                 |
    And file "file1" for product "virtualProduct" should exist in system
    When I duplicate product virtualProduct to a virtualProductCopy
    Then product virtualProductCopy type should be virtual
    And product "virtualProductCopy" should be disabled
    And product virtualProductCopy should have a virtual product file which reference is file1Copy and has following details:
      | display name         | puffin-logo.png |
      | access days          | 0               |
      | download times limit | 0               |
      | expiration date      |                 |
    And file file1Copy for product virtualProductCopy should exist in system
    And virtualProduct and virtualProductCopy have different values
    And file1 and file1Copy have different values
    And file file1 for product virtualProduct should have same file as app_icon.png
    And file file1Copy for product virtualProductCopy should have same file as app_icon.png

  Scenario: I duplicate product features
    Given I create product feature "element" with specified properties:
      | name[en-US]      | Nature Element |
      | associated shops | shop1          |
    And I create feature value "fire" for feature "element" with following properties:
      | value[en-US]     | Fire  |
      | value[fr-FR]     | Feu   |
      | associated shops | shop1 |
    And I create product feature "emotion" with specified properties:
      | name[en-US]      | Emotion |
      | associated shops | shop1   |
    And I create feature value "anger" for feature "emotion" with following properties:
      | value[en-US] | Anger  |
      | value[fr-FR] | Colère |
    # Now create the product with both regular features and custom ones
    Given I add product "darkMagicBook" with following information:
      | name[en-US] | Dark Magic Book |
      | type        | standard        |
    And I set to product "darkMagicBook" the following feature values:
      | feature | feature_value | custom_values                 | custom_reference |
      | element | fire          |                               |                  |
      | emotion | anger         |                               |                  |
      | element |               | en-US:Darkness;fr-FR:Ténèbres | darkness         |
    Then product "darkMagicBook" should have following feature values:
      | feature | feature_value | custom_values                 |
      | element | fire          |                               |
      | emotion | anger         |                               |
      | element | darkness      | en-US:Darkness;fr-FR:Ténèbres |
    When I duplicate product darkMagicBook to a darkMagicBookCopy
    Then product "darkMagicBookCopy" should have following feature values:
      | feature | feature_value | new_feature_value | custom_values                 |
      | element | fire          |                   |                               |
      | emotion | anger         |                   |                               |
      | element |               | darknessCopy      | en-US:Darkness;fr-FR:Ténèbres |
    And darkness and darknessCopy have different values

  Scenario: I duplicate a product with images they are correctly duplicated and associated
    When I add product "productWithCombinationAndImages" with following information:
      | name[en-US] | Jar of sand  |
      | type        | combinations |
    And I add new image "image1" named "app_icon.png" to product "productWithCombinationAndImages"
    And I add new image "image2" named "logo.jpg" to product "productWithCombinationAndImages"
    And I add new image "image3" named "app_icon.png" to product "productWithCombinationAndImages"
    And I add new image "image4" named "logo.jpg" to product "productWithCombinationAndImages"
    And product "productWithCombinationAndImages" should have following images:
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | false    |               |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
      | image3          | false    |               |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image4          | false    |               |               | 4        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
    And images "[image1, image2, image3, image4]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |
    And image "image1" should have same file as "app_icon.png"
    And image "image2" should have same file as "logo.jpg"
    And image "image3" should have same file as "app_icon.png"
    And image "image4" should have same file as "logo.jpg"
    When I generate combinations for product productWithCombinationAndImages using following attributes:
      | Color | [Red,Blue,Pink] |
    Then product "productWithCombinationAndImages" should have following combinations:
      | id reference                        | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationAndImagesRed  | Color - Red      |           | [Color:Red]  | 0               | 0        | true       |
      | productWithCombinationAndImagesBlue | Color - Blue     |           | [Color:Blue] | 0               | 0        | false      |
      | productWithCombinationAndImagesPink | Color - Pink     |           | [Color:Pink] | 0               | 0        | false      |
    And combination "productWithCombinationAndImagesRed" should have no images
    And combination "productWithCombinationAndImagesRed" should have the following cover "http://myshop.com/img/p/{image1}-cart_default.jpg"
    When I associate "[image2]" to combination "productWithCombinationAndImagesBlue"
    Then combination "productWithCombinationAndImagesBlue" should have following images "[image2]"
    And combination "productWithCombinationAndImagesBlue" should have the following cover "http://myshop.com/img/p/{image2}-cart_default.jpg"
    When I associate "[image3, image4]" to combination "productWithCombinationAndImagesPink"
    Then combination "productWithCombinationAndImagesPink" should have following images "[image3, image4]"
    And combination "productWithCombinationAndImagesPink" should have the following cover "http://myshop.com/img/p/{image3}-cart_default.jpg"
    And product "productWithCombinationAndImages" should have following combinations:
      | id reference                        | combination name | reference | attributes   | impact on price | quantity | is default | image url                                          |
      | productWithCombinationAndImagesRed  | Color - Red      |           | [Color:Red]  | 0               | 0        | true       | http://myshop.com/img/p/{image1}-small_default.jpg |
      | productWithCombinationAndImagesBlue | Color - Blue     |           | [Color:Blue] | 0               | 0        | false      | http://myshop.com/img/p/{image2}-small_default.jpg |
      | productWithCombinationAndImagesPink | Color - Pink     |           | [Color:Pink] | 0               | 0        | false      | http://myshop.com/img/p/{image3}-small_default.jpg |
    # Now duplicate the product check the images are correctly duplicated and combination associations as well
    When I duplicate product productWithCombinationAndImages to a productWithCombinationAndImagesCopy
    And product "productWithCombinationAndImagesCopy" should have following images:
      | new image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                                | thumbnail url                                          |
      | image1Copy          | true     |               |               | 1        | http://myshop.com/img/p/{image1Copy}.jpg | http://myshop.com/img/p/{image1Copy}-small_default.jpg |
      | image2Copy          | false    |               |               | 2        | http://myshop.com/img/p/{image2Copy}.jpg | http://myshop.com/img/p/{image2Copy}-small_default.jpg |
      | image3Copy          | false    |               |               | 3        | http://myshop.com/img/p/{image3Copy}.jpg | http://myshop.com/img/p/{image3Copy}-small_default.jpg |
      | image4Copy          | false    |               |               | 4        | http://myshop.com/img/p/{image4Copy}.jpg | http://myshop.com/img/p/{image4Copy}-small_default.jpg |
    And image1 and image1Copy have different values
    And image2 and image2Copy have different values
    And image3 and image3Copy have different values
    And image4 and image4Copy have different values
    And image image1Copy should have same file as "app_icon.png"
    And image image2Copy should have same file as "logo.jpg"
    And image image3Copy should have same file as "app_icon.png"
    And image image4Copy should have same file as "logo.jpg"
    And images "[image1Copy, image2Copy, image3Copy, image4Copy]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |
    # Check combinations associations
    And product "productWithCombinationAndImagesCopy" should have following combinations:
      | id reference                            | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationAndImagesRedCopy  | Color - Red      |           | [Color:Red]  | 0               | 0        | true       |
      | productWithCombinationAndImagesBlueCopy | Color - Blue     |           | [Color:Blue] | 0               | 0        | false      |
      | productWithCombinationAndImagesPinkCopy | Color - Pink     |           | [Color:Pink] | 0               | 0        | false      |
    And combination "productWithCombinationAndImagesRedCopy" should have no images
    And combination "productWithCombinationAndImagesRedCopy" should have the following cover "http://myshop.com/img/p/{image1Copy}-cart_default.jpg"
    And combination "productWithCombinationAndImagesBlueCopy" should have following images "[image2Copy]"
    And combination "productWithCombinationAndImagesBlueCopy" should have the following cover "http://myshop.com/img/p/{image2Copy}-cart_default.jpg"
    Then combination "productWithCombinationAndImagesPinkCopy" should have following images "[image3Copy, image4Copy]"
    And combination "productWithCombinationAndImagesPinkCopy" should have the following cover "http://myshop.com/img/p/{image3Copy}-cart_default.jpg"
    And product "productWithCombinationAndImagesCopy" should have following combinations:
      | combination id                          | combination name | reference | attributes   | impact on price | quantity | is default | image url                                              |
      | productWithCombinationAndImagesRedCopy  | Color - Red      |           | [Color:Red]  | 0               | 0        | true       | http://myshop.com/img/p/{image1Copy}-small_default.jpg |
      | productWithCombinationAndImagesBlueCopy | Color - Blue     |           | [Color:Blue] | 0               | 0        | false      | http://myshop.com/img/p/{image2Copy}-small_default.jpg |
      | productWithCombinationAndImagesPinkCopy | Color - Pink     |           | [Color:Pink] | 0               | 0        | false      | http://myshop.com/img/p/{image3Copy}-small_default.jpg |
