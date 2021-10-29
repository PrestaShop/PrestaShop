# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags duplicate-product
@reset-database-before-feature
@duplicate-product
@reset-downloads-after-feature
@clear-cache-after-feature
Feature: Duplicate product from Back Office (BO).
  As an employee I want to be able to duplicate product

  Background:
    Given category "home" in default language named "Home" exists
    And category "home" is the default one
    And shop "shop1" with name "test_shop" exists
    And single shop shop1 context is loaded
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    And manufacturer studioDesign named "Studio Design" exists
    And language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    And carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
    Given I add product "product1" with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I add product "product2" with following information:
      | name[en-US] | Reading glasses |
      | name[fr-FR] | lunettes        |
      | type        | standard        |
    And I update product "product1" basic information with following values:
      | description[en-US]       | nice sunglasses            |
      | description[fr-FR]       | belles lunettes            |
      | description_short[en-US] | Simple & nice sunglasses   |
      | description_short[fr-FR] | lunettes simples et belles |
    And I assign product product1 to following categories:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    And I update product "product1" options with following values:
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | manufacturer        | studioDesign |
    And I update product "product1" details with following values:
      | isbn      | 978-3-16-148410-0 |
      | upc       | 72527273070       |
      | ean13     | 978020137962      |
      | mpn       | mpn1              |
      | reference | ref1              |
    And I update product "product1" tags with following values:
      | tags[en-US] | smart,glasses,sunglasses,men |
      | tags[fr-FR] | lunettes,bien,soleil         |
    And I update product "product1" prices with following information:
      | price           | 100.00          |
      | ecotax          | 0               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 70              |
      | unit_price      | 500             |
      | unity           | bag of ten      |
    And I update product product1 SEO information with following values:
      | meta_title[en-US]       | SUNGLASSES meta title |
      | meta_description[en-US] | Its so smart          |
      | meta_description[fr-FR] | lel joke              |
      | link_rewrite[en-US]     | smart-sunglasses      |
      | link_rewrite[fr-FR]     | lunettes-de-soleil    |
      | redirect_type           | 301-product           |
      | redirect_target         | product2              |
    And I update product product1 shipping information with following values:
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
    And I add new supplier supplier1 with following properties:
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
    When I set product product1 suppliers:
      | reference         | supplier reference | product supplier reference     | currency | price tax excluded |
      | product1supplier1 | supplier1          | my first supplier for product1 | USD      | 10                 |
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
    When I associate product product1 with following attachments: "[att1]"
    And I enable product "product1"
    When I update product product1 SEO information with following values:
      | redirect_type   | 301-product |
      | redirect_target | product2    |
    And product product1 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product2    |

  Scenario: I duplicate product
#todo: add specific prices & priorities, test combinations, packs
    When I duplicate product product1 to a copy_of_product1
    And product "copy_of_product1" should be disabled
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
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | false      |
      | men          | Men         | Men         | false      |
      | clothes      | Clothes     | Clothes     | true       |
    And product "copy_of_product1" should have following options:
      | product option      | value        |
      | active              | false        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "copy_of_product1" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    And product "copy_of_product1" localized "tags" should be:
      | locale | value                        |
      | en-US  | smart,glasses,sunglasses,men |
      | fr-FR  | lunettes,bien,soleil         |
    And product copy_of_product1 should have following suppliers:
      | product supplier reference     | currency | price tax excluded |
      | my first supplier for product1 | USD      | 10                 |
    And product copy_of_product1 should have following prices information:
      | price            | 100.00          |
      | ecotax           | 0               |
      | tax rules group  | US-AL Rate (4%) |
      | on_sale          | true            |
      # wholesale_price = 10, because of assigned product supplier 'price tax excluded'.
      | wholesale_price  | 10              |
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
      | product2 |
    And product copy_of_product1 should have following attachments associated:
      | attachment reference | title                       | description                           | file name    | type      | size  |
      | att1                 | en-US:puffin;fr-Fr:macareux | en-US:puffin photo nr1;fr-Fr:macareux | app_icon.png | image/png | 19187 |
    And product copy_of_product1 should have identical customization fields to product1
    And product copy_of_product1 should have 1 customizable text field
    And product copy_of_product1 should have 0 customizable file fields
#@todo: assert stock info
#@todo: add tests for other type of products Pack, Virtual, Combinations
