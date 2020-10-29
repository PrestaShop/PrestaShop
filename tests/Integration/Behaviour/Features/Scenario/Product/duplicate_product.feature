# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags duplicate-product
@reset-database-before-feature
@duplicate-product
Feature: Duplicate product from Back Office (BO).
  As an employee I want to be able to duplicate product

  Background:
    Given category "home" in default language named "Home" exists
    And shop "shop1" with name "test_shop" exists
    And single shop shop1 context is loaded
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    And manufacturer studioDesign named "Studio Design" exists

  Scenario: I duplicate product
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    Given I add product "product1" with following information:
      | name       | en-US:smart sunglasses ;fr-FR:lunettes de soleil |
      | is_virtual | false                                           |
    And I add product "product2" with following information:
      | name       | en-US:Reading glasses ;fr-FR:lunettes |
      | is_virtual | false                                |
    And I update product "product1" basic information with following values:
      | description       | en-US:nice sunglasses ;fr-FR:belles lunettes                    |
      | description_short | en-US:Simple & nice sunglasses;fr-FR:lunettes simples et belles |
    And I assign product product1 to following categories:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    And I update product "product1" options with following information:
      | visibility          | catalog           |
      | available_for_order | false             |
      | online_only         | true              |
      | show_price          | false             |
      | condition           | used              |
      | isbn                | 978-3-16-148410-0 |
      | upc                 | 72527273070       |
      | ean13               | 978020137962      |
      | mpn                 | mpn1              |
      | reference           | ref1              |
      | manufacturer        | studioDesign      |
    And I update product "product1" tags with following values:
      | tags | en-US:smart,glasses,sunglasses,men ;fr-FR:lunettes,bien,soleil |
    And I update product "product1" prices with following information:
      | price           | 100.00          |
      | ecotax          | 0               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 70              |
      | unit_price      | 900             |
      | unity           | bag of ten      |
    And I update product product1 SEO information with following values:
      | meta_title       | en-US:SUNGLASSES meta title                       |
      | meta_description | en-US:Its so smart, almost magical ;fr-FR:lel joke |
      | link_rewrite     | en-US:smart-sunglasses ;fr-FR:lunettes-de-soleil   |
      | redirect_type    | 301-product                                       |
      | redirect_target  | product2                                          |
    And carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
    And I update product product1 shipping information with following values:
      | width                            | 10.5                                                 |
      | height                           | 6                                                    |
      | depth                            | 7                                                    |
      | weight                           | 0.5                                                  |
      | additional_shipping_cost         | 12                                                   |
      | delivery time notes type         | specific                                             |
      | delivery time in stock notes     | en-US:product in stock ;fr-FR:en stock                |
      | delivery time out of stock notes | en-US:product out of stock ;fr-FR:En rupture de stock |
      | carriers                         | [carrier1,carrier2]                                  |
    And I add new supplier supplier1 with following properties:
      | name             | my supplier 1            |
      | address          | Donelaicio st. 1         |
      | city             | Kaunas                   |
      | country          | Lithuania                |
      | enabled          | true                     |
      | description      | en-US:just a supplier    |
      | meta title       | en-US:my supplier nr one |
      | meta description | en-US:                   |
      | meta keywords    | en-US:sup,1              |
      | shops            | [shop1]                  |
    And I set product product1 default supplier to supplier1 and following suppliers:
      | reference         | supplier reference | product supplier reference     | currency | price tax excluded |
      | product1supplier1 | supplier1          | my first supplier for product1 | USD      | 10                 |
    And I set following related products to product product1:
      | product2 |
    And I update product product1 with following customization fields:
      | reference    | type | name                                                                      | is required |
      | customField1 | text | en-US:text on top of left lense ;fr-FR:texte en haut de la lentille gauche | true        |
    And I add new attachment "att1" with following properties:
      | description | en-US:puffin photo nr1 ;fr-FR:macareux |
      | name        | en-US:puffin ;fr-FR:macareux           |
      | file_name   | app_icon.png                          |
    And I associate attachment "att1" with product product1
#todo: add specific prices & priorities, test combinations, packs
    When I duplicate product product1 to a copy_of_product1
    Then product "product1" should have following values:
      | active | false |
    And product "copy_of_product1" type should be standard
    And product "copy_of_product1" localized "name" should be "en-US:copy of smart sunglasses; fr-FR:copy of lunettes de soleil"
    And product "copy_of_product1" localized "description" should be "en-US:nice sunglasses; fr-FR:belles lunettes"
    And product "copy_of_product1" localized "description_short" should be "en-US:Simple & nice sunglasses; fr-FR:lunettes simples et belles"
    And product copy_of_product1 should be assigned to following categories:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    And product "copy_of_product1" should have following options information:
      | visibility          | catalog           |
      | available_for_order | false             |
      | online_only         | true              |
      | show_price          | false             |
      | condition           | used              |
      | isbn                | 978-3-16-148410-0 |
      | upc                 | 72527273070       |
      | ean13               | 978020137962      |
      | mpn                 | mpn1              |
      | reference           | ref1              |
    And manufacturer "studioDesign" should be assigned to product copy_of_product1
    And product "copy_of_product1" localized "tags" should be "en-US:smart,glasses,sunglasses,men ;fr-FR:lunettes,bien,soleil"
    And product copy_of_product1 should have following prices information:
      | price            | 100.00          |
      | ecotax           | 0               |
      | tax rules group  | US-AL Rate (4%) |
      | on_sale          | true            |
      | wholesale_price  | 70              |
      | unit_price       | 900             |
      | unity            | bag of ten      |
      | unit_price_ratio | 0.111111        |
    And product "copy_of_product1" localized "meta_title" should be "en-US:SUNGLASSES meta title"
    And product "copy_of_product1" localized "meta_description" should be "en-US:Its so smart, almost magical ;fr-FR:lel joke"
    And product "copy_of_product1" localized "link_rewrite" should be "en-US:smart-sunglasses ;fr-FR:lunettes-de-soleil"
    And product copy_of_product1 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product2    |
    And product copy_of_product1 redirect target should be product2
    And product "copy_of_product1" should have following shipping information:
      | width                            | 10.5                                                 |
      | height                           | 6                                                    |
      | depth                            | 7                                                    |
      | weight                           | 0.5                                                  |
      | additional_shipping_cost         | 12                                                   |
      | delivery time notes type         | specific                                             |
      | delivery time in stock notes     | en-US:product in stock ;fr-FR:en stock                |
      | delivery time out of stock notes | en-US:product out of stock ;fr-FR:En rupture de stock |
      | carriers                         | [carrier1,carrier2]                                  |
    And product copy_of_product1 should have following suppliers:
      | product supplier reference     | currency | price tax excluded |
      | my first supplier for product1 | USD      | 10                 |
    And product copy_of_product1 should have following related products:
      | product2 |
    And product copy_of_product1 should have following customization fields:
      | reference    | type | name                                                                      | is required |
      | customField1 | text | en-US:text on top of left lense ;fr-FR:texte en haut de la lentille gauche | true        |
    And product copy_of_product1 should have following attachments associated: "[att1]"
