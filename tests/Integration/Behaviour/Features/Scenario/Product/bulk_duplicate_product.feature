# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags bulk-duplicate-product
@restore-products-before-feature
@reset-downloads-after-feature
@clear-cache-after-feature
@restore-languages-after-feature
@bulk-product
@bulk-duplicate-product
Feature: Duplicate product from Back Office (BO).
  As an employee I want to be able to duplicate product

  Background:
    Given category "home" in default language named "Home" exists
    And category "home" is the default one
    And shop "shop1" with name "test_shop" exists
    And single shop shop1 context is loaded
    And category "men" in default language named "Men" exists
    And category "women" in default language named "Women" exists
    And category "clothes" in default language named "Clothes" exists
    And manufacturer studioDesign named "Studio Design" exists
    And language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    And carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
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
    And I add product "product1" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I add product "product2" with following information:
      | name[en-US] | Reading glasses |
      | name[fr-FR] | lunettes        |
      | type        | standard        |
    And I update product "product1" with following values:
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
      | redirect_target                         | product2                   |
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
      | redirect_type                           | 301-product                |
      | redirect_target                         | product2                   |
    And I assign product product1 to following categories:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    And I update product "product1" tags with following values:
      | tags[en-US] | smart,glasses,sunglasses,men |
      | tags[fr-FR] | lunettes,bien,soleil         |
    And I assign product product1 with following carriers:
      | carrier1 |
      | carrier2 |
    And I associate suppliers to product "product1"
      | supplier  | product_supplier  |
      | supplier1 | product1supplier1 |
    And I update product product1 suppliers:
      | product_supplier  | supplier  | reference                      | currency | price_tax_excluded |
      | product1supplier1 | supplier1 | my first supplier for product1 | USD      | 10                 |
    And I set following related products to product product1:
      | product2 |
    And I update product product1 with following customization fields:
      | reference    | type | name[en-US]               | name[fr-FR]                         | is required |
      | customField1 | text | text on top of left lense | texte en haut de la lentille gauche | true        |
    And I add new attachment "att1" with following properties:
      | description[en-US] | puffin photo nr1 |
      | description[fr-FR] | macareux         |
      | name[en-US]        | puffin           |
      | name[fr-FR]        | macareux         |
      | file_name          | app_icon.png     |
    And I associate product product1 with following attachments: "[att1]"
    And I update product "product2" with following values:
      | description[en-US]                      | Reading glasses                   |
      | description[fr-FR]                      | lunettes de lecture               |
      | description_short[en-US]                | Clear Reading glasses             |
      | description_short[fr-FR]                | Lunettes de lecture transparentes |
      | visibility                              | catalog                           |
      | available_for_order                     | true                              |
      | online_only                             | false                             |
      | show_price                              | true                              |
      | condition                               | new                               |
      | manufacturer                            | studioDesign                      |
      | isbn                                    | 978-3-16-148410-1                 |
      | upc                                     | 72527273072                       |
      | ean13                                   | 978020137964                      |
      | mpn                                     | mpn2                              |
      | reference                               | ref2                              |
      | isbn                                    | 978-3-16-148410-1                 |
      | upc                                     | 72527273072                       |
      | ean13                                   | 978020137964                      |
      | mpn                                     | mpn2                              |
      | reference                               | ref2                              |
      | price                                   | 200.00                            |
      | ecotax                                  | 0                                 |
      | tax rules group                         | US-AL Rate (4%)                   |
      | on_sale                                 | true                              |
      | wholesale_price                         | 150                               |
      | unit_price                              | 500                               |
      | unity                                   | lots                              |
      | meta_title[en-US]                       | READINGGLASSES meta title         |
      | meta_description[en-US]                 | You can read now                  |
      | meta_description[fr-FR]                 | You can read in french now        |
      | link_rewrite[en-US]                     | reading-glasses                   |
      | link_rewrite[fr-FR]                     | lunettes-de-lecture               |
      | redirect_type                           | 301-product                       |
      | redirect_target                         | product1                          |
      | width                                   | 12                                |
      | height                                  | 8                                 |
      | depth                                   | 4                                 |
      | weight                                  | 2                                 |
      | additional_shipping_cost                | 8                                 |
      | delivery time notes type                | specific                          |
      | delivery time in stock notes[en-US]     | product in stock                  |
      | delivery time in stock notes[fr-FR]     | en stock                          |
      | delivery time out of stock notes[en-US] | product out of stock              |
      | delivery time out of stock notes[fr-FR] | En rupture de stock               |
      | active                                  | true                              |
      | redirect_type                           | 301-product                       |
      | redirect_target                         | product1                          |
    And I assign product product2 to following categories:
      | categories       | [home, women, clothes] |
      | default category | women                  |
    And I update product "product2" tags with following values:
      | tags[en-US] | glasses,readingglasses,women     |
      | tags[fr-FR] | lunettes,lunettespourlire,femmes |
    And I assign product product2 with following carriers:
      | carrier1 |
      | carrier2 |
    When I associate suppliers to product "product2"
      | supplier  | product_supplier  |
      | supplier1 | product2supplier1 |
    And I update product product2 suppliers:
      | product_supplier  | supplier  | reference                      | currency | price_tax_excluded |
      | product2supplier1 | supplier1 | my first supplier for product2 | USD      | 10                 |
    And I set following related products to product product2:
      | product1 |
    And I update product product2 with following customization fields:
      | reference    | type | name[en-US]               | name[fr-FR]                         | is required |
      | customField2 | text | text on top of left lense | texte en haut de la lentille gauche | true        |
    And I enable product "product2"
    And product product2 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product1    |

  Scenario: I duplicate products
#todo: add specific prices & priorities, test combinations, packs
    When I bulk duplicate following products:
      | reference | copy_reference   |
      | product1  | copy_of_product1 |
      | product2  | copy_of_product2 |
    Then product "copy_of_product1" should be disabled
    And product "copy_of_product1" type should be standard
    And product "copy_of_product1" localized "name" should be:
      | locale | value                       |
      | en-US  | copy of smart sunglasses    |
      | fr-FR  | copie de lunettes de soleil |
    And product "copy_of_product1" localized "description" should be:
      | locale | value           |
      | en-US  | nice sunglasses |
      | fr-FR  | belles lunettes |
    And product "copy_of_product1" localized "description_short" should be:
      | locale | value                      |
      | en-US  | Simple & nice sunglasses   |
      | fr-FR  | lunettes simples et belles |
    And product copy_of_product1 should be assigned to following categories:
      | id reference | name    | is default |
      | home         | Home    | false      |
      | men          | Men     | false      |
      | clothes      | Clothes | true       |
    And product "copy_of_product1" should have following options:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "copy_of_product2" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-1 |
      | upc            | 72527273072       |
      | ean13          | 978020137964      |
      | mpn            | mpn2              |
      | reference      | ref2              |
    And product "copy_of_product2" localized "tags" should be:
      | locale | value                            |
      | en-US  | glasses,readingglasses,women     |
      | fr-FR  | lunettes,lunettespourlire,femmes |
    And product copy_of_product2 should have following suppliers:
      | supplier  | reference                      | currency | price_tax_excluded |
      | supplier1 | my first supplier for product2 | USD      | 10                 |
    And product copy_of_product1 should have following prices information:
      | price            | 100.00          |
      | ecotax           | 0               |
      | tax rules group  | US-AL Rate (4%) |
      | on_sale          | true            |
      | wholesale_price  | 70              |
      | unit_price       | 500             |
      | unity            | bag of ten      |
      | unit_price_ratio | 0.2             |
    And product "copy_of_product1" localized "meta_title" should be:
      | locale | value                 |
      | en-US  | SUNGLASSES meta title |
    And product "copy_of_product1" localized "meta_description" should be:
      | locale | value        |
      | en-US  | Its so smart |
      | fr-FR  | lel joke     |
    And product "copy_of_product1" localized "link_rewrite" should be:
      | locale | value              |
      | en-US  | smart-sunglasses   |
      | fr-FR  | lunettes-de-soleil |
    And product copy_of_product1 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product2    |
    And product "copy_of_product1" should have following shipping information:
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
      | carriers                                | [carrier1,carrier2]  |
    And product copy_of_product1 should have following related products:
      | product  | name            | reference | image url                                             |
      | product2 | Reading glasses | ref2      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And product copy_of_product1 should have following attachments associated:
      | attachment reference | title                       | description                           | file name    | type      | size  |
      | att1                 | en-US:puffin;fr-Fr:macareux | en-US:puffin photo nr1;fr-Fr:macareux | app_icon.png | image/png | 19187 |

    And product copy_of_product1 should have 1 customizable text field
    And product copy_of_product1 should have 0 customizable file fields
    And product copy_of_product1 should have following customization fields:
      | new reference    | type | name[en-US]               | name[fr-FR]                         | is required |
      | customField1Copy | text | text on top of left lense | texte en haut de la lentille gauche | true        |
    And customField1 and customField1Copy have different values

    And product "copy_of_product2" should be disabled
    And product "copy_of_product2" type should be standard
    And product "copy_of_product2" localized "name" should be:
      | locale | value                   |
      | en-US  | copy of Reading glasses |
      | fr-FR  | copie de lunettes       |
    And product "copy_of_product2" localized "description" should be:
      | locale | value               |
      | en-US  | Reading glasses     |
      | fr-FR  | lunettes de lecture |
    And product "copy_of_product2" localized "description_short" should be:
      | locale | value                             |
      | en-US  | Clear Reading glasses             |
      | fr-FR  | Lunettes de lecture transparentes |
    And product copy_of_product2 should be assigned to following categories:
      | id reference | name    | is default |
      | home         | Home    | false      |
      | women        | Women   | true       |
      | clothes      | Clothes | false      |
    And product "copy_of_product2" should have following options:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | true         |
      | online_only         | false        |
      | show_price          | true         |
      | condition           | new          |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "copy_of_product2" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-1 |
      | upc            | 72527273072       |
      | ean13          | 978020137964      |
      | mpn            | mpn2              |
      | reference      | ref2              |
    And product "copy_of_product1" localized "tags" should be:
      | locale | value                        |
      | en-US  | smart,glasses,sunglasses,men |
      | fr-FR  | lunettes,bien,soleil         |
    And product copy_of_product1 should have following suppliers:
      | supplier  | reference                      | currency | price_tax_excluded |
      | supplier1 | my first supplier for product1 | USD      | 10                 |
    And product copy_of_product2 should have following prices information:
      | price            | 200.00          |
      | ecotax           | 0               |
      | tax rules group  | US-AL Rate (4%) |
      | on_sale          | true            |
      | wholesale_price  | 150             |
      | unit_price       | 500             |
      | unity            | lots            |
      | unit_price_ratio | 0.4             |
    And product "copy_of_product2" localized "meta_title" should be:
      | locale | value                     |
      | en-US  | READINGGLASSES meta title |
    And product "copy_of_product2" localized "meta_description" should be:
      | locale | value                      |
      | en-US  | You can read now           |
      | fr-FR  | You can read in french now |
    And product "copy_of_product2" localized "link_rewrite" should be:
      | locale | value               |
      | en-US  | reading-glasses     |
      | fr-FR  | lunettes-de-lecture |
    And product copy_of_product2 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product1    |
    And product "copy_of_product2" should have following shipping information:
      | width                                   | 12                   |
      | height                                  | 8                    |
      | depth                                   | 4                    |
      | weight                                  | 2                    |
      | additional_shipping_cost                | 8                    |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time in stock notes[fr-FR]     | en stock             |
      | delivery time out of stock notes[en-US] | product out of stock |
      | delivery time out of stock notes[fr-FR] | En rupture de stock  |
      | carriers                                | [carrier1,carrier2]  |
    And product copy_of_product2 should have following related products:
      | product  | name             | reference | image url                                             |
      | product1 | smart sunglasses | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
