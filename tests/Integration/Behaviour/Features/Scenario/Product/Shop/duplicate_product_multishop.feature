# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags duplicate-product-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@restore-taxes-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multishop
@duplicate-product-multishop
Feature: Copy product from shop to shop.
  As a BO user I want to be able to duplicate products for specific shop, group shop and all shops

  Scenario: This are pre-requisite steps but it is only done once, that is why we don't use Background here
    Given I enable multishop feature
    And language "english" with locale "en-US" exists
    And language "french" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And Shop group test_second_shop_group shares its stock
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    Given manufacturer studioDesign named "Studio Design" exists
    And carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
    # Prepare a few data
    And I add new tax "us-tax-state-1" with following properties:
      | name       | US Tax (6%) |
      | rate       | 6           |
      | is_enabled | true        |
    And I add the tax rule group "us-tax-group-multiple-states" for the tax "us-tax-state-1" with the following conditions:
      | name    | US Tax group |
      | country | US           |
      | state   | AK           |
    And I add product "productForRedirection" with following information:
      | name[en-US] | dumb sunglasses   |
      | name[fr-FR] | lunettes de nuage |
      | type        | standard          |
    And I add product "productForRedirection2" with following information:
      | name[en-US] | moonglasses      |
      | name[fr-FR] | lunettes de lune |
      | type        | standard         |

  Scenario: I duplicate a product all its direct fields are correctly copied
    When I add product "productWithFields" to shop shop2 with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I update product "productWithFields" for shop shop2 with following values:
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
    And I copy product productWithFields from shop shop2 to shop shop1
    And I copy product productWithFields from shop shop2 to shop shop3
    And I update product "productWithFields" for shop shop3 with following values:
      | name[en-US]                             | smart sunglasses3           |
      | name[fr-FR]                             | lunettes de soleil3         |
      | description[en-US]                      | nice sunglasses3            |
      | description[fr-FR]                      | belles lunettes3            |
      | description_short[en-US]                | Simple & nice sunglasses3   |
      | description_short[fr-FR]                | lunettes simples et belles3 |
      | price                                   | 103.00                      |
      | ecotax                                  | 3                           |
      | tax rules group                         | US Tax group                |
      | on_sale                                 | false                       |
      | wholesale_price                         | 73                          |
      | unit_price                              | 1030                        |
      | unity                                   | bag of twenty               |
      | meta_title[en-US]                       | SUNGLASSES meta title3      |
      | meta_description[en-US]                 | Its so smart3               |
      | meta_description[fr-FR]                 | lel joke3                   |
      | link_rewrite[en-US]                     | smart-sunglasses3           |
      | link_rewrite[fr-FR]                     | lunettes-de-soleil3         |
      | redirect_type                           | 302-product                 |
      | redirect_target                         | productForRedirection2      |
      | delivery time in stock notes[en-US]     | product in stock3           |
      | delivery time in stock notes[fr-FR]     | en stock3                   |
      | delivery time out of stock notes[en-US] | product out of stock3       |
      | delivery time out of stock notes[fr-FR] | En rupture de stock3        |
      | active                                  | false                       |
    Then product productWithFields is associated to shop shop1
    And product productWithFields is associated to shop shop2
    And product productWithFields is associated to shop shop3
    And product productWithFields is not associated to shop shop4
    And default shop for product productWithFields is shop2
    #
    # Duplicate for shop 1
    #
    When I duplicate product productWithFields to a productWithFieldsCopy for shop shop1
    # Even if the initial product was active the copy is disabled by default
    Then product "productWithFieldsCopy" should be disabled for shops "shop1"
    And product "productWithFieldsCopy" type should be standard for shop shop1
    And product "productWithFieldsCopy" localized "name" for shops "shop1" should be:
      | locale | value                       |
      | en-US  | copy of smart sunglasses    |
      | fr-FR  | copie de lunettes de soleil |
    And product "productWithFieldsCopy" localized "description" for shops "shop1" should be:
      | locale | value           |
      | en-US  | nice sunglasses |
      | fr-FR  | belles lunettes |
    And product "productWithFieldsCopy" localized "description_short" for shops "shop1" should be:
      | locale | value                      |
      | en-US  | Simple & nice sunglasses   |
      | fr-FR  | lunettes simples et belles |
    And product "productWithFieldsCopy" should have following options for shops shop1:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "productWithFieldsCopy" should have following details for shops shop1:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    And product productWithFieldsCopy should have following prices information for shops shop1:
      | price                   | 100.00          |
      | ecotax                  | 0               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 500             |
      | unit_price_tax_included | 520             |
      | unit_price_ratio        | 0.2             |
      | unity                   | bag of ten      |
    And product "productWithFieldsCopy" localized "meta_title" for shops shop1 should be:
      | locale | value                 |
      | en-US  | SUNGLASSES meta title |
    And product "productWithFieldsCopy" localized "meta_description" for shops shop1 should be:
      | locale | value        |
      | en-US  | Its so smart |
      | fr-FR  | lel joke     |
    And product "productWithFieldsCopy" localized "link_rewrite" for shops shop1 should be:
      | locale | value              |
      | en-US  | smart-sunglasses   |
      | fr-FR  | lunettes-de-soleil |
    And product productWithFieldsCopy should have following seo options for shops shop1:
      | redirect_type   | 301-product           |
      | redirect_target | productForRedirection |
    And product "productWithFieldsCopy" should have following shipping information for shops "shop1":
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
    And product productWithFieldsCopy is associated to shop shop1
    And product productWithFieldsCopy is not associated to shop shop2
    And product productWithFieldsCopy is not associated to shop shop3
    And product productWithFieldsCopy is not associated to shop shop4
    And default shop for product productWithFieldsCopy is shop1
    #
    # Duplicate for shop 3
    #
    When I duplicate product productWithFields to a productWithFieldsCopy3 for shop shop3
    Then product "productWithFieldsCopy3" should be disabled for shops "shop3"
    And product "productWithFieldsCopy3" type should be standard for shop shop3
    And product "productWithFieldsCopy3" localized "name" for shops "shop3" should be:
      | locale | value                        |
      | en-US  | copy of smart sunglasses3    |
      | fr-FR  | copie de lunettes de soleil3 |
    And product "productWithFieldsCopy3" localized "description" for shops "shop3" should be:
      | locale | value            |
      | en-US  | nice sunglasses3 |
      | fr-FR  | belles lunettes3 |
    And product "productWithFieldsCopy3" localized "description_short" for shops "shop3" should be:
      | locale | value                       |
      | en-US  | Simple & nice sunglasses3   |
      | fr-FR  | lunettes simples et belles3 |
    And product "productWithFieldsCopy3" should have following options for shops shop3:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "productWithFieldsCopy3" should have following details for shops shop3:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    And product productWithFieldsCopy3 should have following prices information for shops shop3:
      | price                   | 103.00        |
      | ecotax                  | 3.0           |
      | tax rules group         | US Tax group  |
      | on_sale                 | false         |
      | wholesale_price         | 73            |
      | unit_price              | 1030          |
      | unit_price_tax_included | 1091.80       |
      # 103 / 1030
      | unit_price_ratio        | 0.1           |
      | unity                   | bag of twenty |
    And product "productWithFieldsCopy3" localized "meta_title" for shops shop3 should be:
      | locale | value                  |
      | en-US  | SUNGLASSES meta title3 |
    And product "productWithFieldsCopy3" localized "meta_description" for shops shop3 should be:
      | locale | value         |
      | en-US  | Its so smart3 |
      | fr-FR  | lel joke3     |
    And product "productWithFieldsCopy3" localized "link_rewrite" for shops shop3 should be:
      | locale | value               |
      | en-US  | smart-sunglasses3   |
      | fr-FR  | lunettes-de-soleil3 |
    And product productWithFieldsCopy3 should have following seo options for shops shop3:
      | redirect_type   | 302-product            |
      | redirect_target | productForRedirection2 |
    And product "productWithFieldsCopy3" should have following shipping information for shops "shop3":
      | width                                   | 10.5                  |
      | height                                  | 6                     |
      | depth                                   | 7                     |
      | weight                                  | 0.5                   |
      | additional_shipping_cost                | 12                    |
      | delivery time notes type                | specific              |
      | delivery time in stock notes[en-US]     | product in stock3     |
      | delivery time in stock notes[fr-FR]     | en stock3             |
      | delivery time out of stock notes[en-US] | product out of stock3 |
      | delivery time out of stock notes[fr-FR] | En rupture de stock3  |
      | carriers                                | []                    |
    And productWithFields and productWithFieldsCopy3 have different values
    And productWithFieldsCopy and productWithFieldsCopy3 have different values
    And product productWithFieldsCopy3 is associated to shop shop3
    And product productWithFieldsCopy3 is not associated to shop shop1
    And product productWithFieldsCopy3 is not associated to shop shop2
    And product productWithFieldsCopy3 is not associated to shop shop4
    And default shop for product productWithFieldsCopy3 is shop3

  Scenario: I duplicate a product for all shops all its associated data is copied (based on created product in previous scenario)
    When I duplicate product productWithFields to a productWithFieldsOnAllShops for all shops
    # Shop1 and shop2 have the same values
    Then product "productWithFieldsOnAllShops" should be disabled for shops "shop1,shop2"
    And product "productWithFieldsOnAllShops" type should be standard for shops "shop1,shop2"
    And product "productWithFieldsOnAllShops" localized "name" for shops "shop1,shop2" should be:
      | locale | value                       |
      | en-US  | copy of smart sunglasses    |
      | fr-FR  | copie de lunettes de soleil |
    And product "productWithFieldsOnAllShops" localized "description" for shops "shop1,shop2" should be:
      | locale | value           |
      | en-US  | nice sunglasses |
      | fr-FR  | belles lunettes |
    And product "productWithFieldsOnAllShops" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value                      |
      | en-US  | Simple & nice sunglasses   |
      | fr-FR  | lunettes simples et belles |
    And product "productWithFieldsOnAllShops" should have following options for shops "shop1,shop2":
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "productWithFieldsOnAllShops" should have following details for shops "shop1,shop2":
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    And product productWithFieldsOnAllShops should have following prices information for shops "shop1,shop2":
      | price                   | 100.00          |
      | ecotax                  | 0               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 500             |
      | unit_price_tax_included | 520             |
      | unit_price_ratio        | 0.2             |
      | unity                   | bag of ten      |
    And product "productWithFieldsOnAllShops" localized "meta_title" for shops "shop1,shop2" should be:
      | locale | value                 |
      | en-US  | SUNGLASSES meta title |
    And product "productWithFieldsOnAllShops" localized "meta_description" for shops "shop1,shop2" should be:
      | locale | value        |
      | en-US  | Its so smart |
      | fr-FR  | lel joke     |
    And product "productWithFieldsOnAllShops" localized "link_rewrite" for shops "shop1,shop2" should be:
      | locale | value              |
      | en-US  | smart-sunglasses   |
      | fr-FR  | lunettes-de-soleil |
    And product productWithFieldsOnAllShops should have following seo options for shops "shop1,shop2":
      | redirect_type   | 301-product           |
      | redirect_target | productForRedirection |
    And product "productWithFieldsOnAllShops" should have following shipping information for shops "shop1,shop2":
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
    # Values for shop3 are customized
    Then product "productWithFieldsOnAllShops" should be disabled for shops "shop3"
    And product "productWithFieldsOnAllShops" type should be standard for shop shop3
    And product "productWithFieldsOnAllShops" localized "name" for shops "shop3" should be:
      | locale | value                        |
      | en-US  | copy of smart sunglasses3    |
      | fr-FR  | copie de lunettes de soleil3 |
    And product "productWithFieldsOnAllShops" localized "description" for shops "shop3" should be:
      | locale | value            |
      | en-US  | nice sunglasses3 |
      | fr-FR  | belles lunettes3 |
    And product "productWithFieldsOnAllShops" localized "description_short" for shops "shop3" should be:
      | locale | value                       |
      | en-US  | Simple & nice sunglasses3   |
      | fr-FR  | lunettes simples et belles3 |
    And product "productWithFieldsOnAllShops" should have following options for shops shop3:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "productWithFieldsOnAllShops" should have following details for shops shop3:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    And product productWithFieldsOnAllShops should have following prices information for shops shop3:
      | price                   | 103.00        |
      | ecotax                  | 3.0           |
      | tax rules group         | US Tax group  |
      | on_sale                 | false         |
      | wholesale_price         | 73            |
      | unit_price              | 1030          |
      | unit_price_tax_included | 1091.80       |
      # 103 / 1030
      | unit_price_ratio        | 0.1           |
      | unity                   | bag of twenty |
    And product "productWithFieldsOnAllShops" localized "meta_title" for shops shop3 should be:
      | locale | value                  |
      | en-US  | SUNGLASSES meta title3 |
    And product "productWithFieldsOnAllShops" localized "meta_description" for shops shop3 should be:
      | locale | value         |
      | en-US  | Its so smart3 |
      | fr-FR  | lel joke3     |
    And product "productWithFieldsOnAllShops" localized "link_rewrite" for shops shop3 should be:
      | locale | value               |
      | en-US  | smart-sunglasses3   |
      | fr-FR  | lunettes-de-soleil3 |
    And product productWithFieldsOnAllShops should have following seo options for shops shop3:
      | redirect_type   | 302-product            |
      | redirect_target | productForRedirection2 |
    And product "productWithFieldsOnAllShops" should have following shipping information for shops "shop3":
      | width                                   | 10.5                  |
      | height                                  | 6                     |
      | depth                                   | 7                     |
      | weight                                  | 0.5                   |
      | additional_shipping_cost                | 12                    |
      | delivery time notes type                | specific              |
      | delivery time in stock notes[en-US]     | product in stock3     |
      | delivery time in stock notes[fr-FR]     | en stock3             |
      | delivery time out of stock notes[en-US] | product out of stock3 |
      | delivery time out of stock notes[fr-FR] | En rupture de stock3  |
      | carriers                                | []                    |
    And productWithFields and productWithFieldsOnAllShops have different values
    And productWithFieldsCopy and productWithFieldsOnAllShops have different values
    And product productWithFieldsOnAllShops is associated to shop shop1
    And product productWithFieldsOnAllShops is associated to shop shop2
    And product productWithFieldsOnAllShops is associated to shop shop3
    And product productWithFieldsOnAllShops is not associated to shop shop4
    # The default shop is the same as the initial one
    And default shop for product productWithFieldsOnAllShops is shop2

  Scenario: I duplicate a product for a shop group all its associated data is copied (based on created product in previous scenario)
    When I duplicate product productWithFields to a productWithFieldsDefaultGroup for shop group default_shop_group
    # Shop1 and shop2 have the same values
    Then product "productWithFieldsDefaultGroup" should be disabled for shops "shop1,shop2"
    And product "productWithFieldsDefaultGroup" type should be standard for shops "shop1,shop2"
    And product "productWithFieldsDefaultGroup" localized "name" for shops "shop1,shop2" should be:
      | locale | value                       |
      | en-US  | copy of smart sunglasses    |
      | fr-FR  | copie de lunettes de soleil |
    And product "productWithFieldsDefaultGroup" localized "description" for shops "shop1,shop2" should be:
      | locale | value           |
      | en-US  | nice sunglasses |
      | fr-FR  | belles lunettes |
    And product "productWithFieldsDefaultGroup" localized "description_short" for shops "shop1,shop2" should be:
      | locale | value                      |
      | en-US  | Simple & nice sunglasses   |
      | fr-FR  | lunettes simples et belles |
    And product "productWithFieldsDefaultGroup" should have following options for shops "shop1,shop2":
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "productWithFieldsDefaultGroup" should have following details for shops "shop1,shop2":
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    And product productWithFieldsDefaultGroup should have following prices information for shops "shop1,shop2":
      | price                   | 100.00          |
      | ecotax                  | 0               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 500             |
      | unit_price_tax_included | 520             |
      | unit_price_ratio        | 0.2             |
      | unity                   | bag of ten      |
    And product "productWithFieldsDefaultGroup" localized "meta_title" for shops "shop1,shop2" should be:
      | locale | value                 |
      | en-US  | SUNGLASSES meta title |
    And product "productWithFieldsDefaultGroup" localized "meta_description" for shops "shop1,shop2" should be:
      | locale | value        |
      | en-US  | Its so smart |
      | fr-FR  | lel joke     |
    And product "productWithFieldsDefaultGroup" localized "link_rewrite" for shops "shop1,shop2" should be:
      | locale | value              |
      | en-US  | smart-sunglasses   |
      | fr-FR  | lunettes-de-soleil |
    And product productWithFieldsDefaultGroup should have following seo options for shops "shop1,shop2":
      | redirect_type   | 301-product           |
      | redirect_target | productForRedirection |
    And product "productWithFieldsDefaultGroup" should have following shipping information for shops "shop1,shop2":
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
    And productWithFields and productWithFieldsDefaultGroup have different values
    And product productWithFieldsDefaultGroup is associated to shop shop1
    And product productWithFieldsDefaultGroup is associated to shop shop2
    And product productWithFieldsDefaultGroup is not associated to shop shop3
    And product productWithFieldsDefaultGroup is not associated to shop shop4
    # The default shop is the same as the initial one because it is part of the group
    And default shop for product productWithFieldsDefaultGroup is shop2
    #
    # Now copy to other group
    #
    When I duplicate product productWithFields to a productWithFieldsSecondGroup for shop group test_second_shop_group
    Then product "productWithFieldsSecondGroup" should be disabled for shops "shop3"
    And product "productWithFieldsSecondGroup" type should be standard for shop shop3
    And product "productWithFieldsSecondGroup" localized "name" for shops "shop3" should be:
      | locale | value                        |
      | en-US  | copy of smart sunglasses3    |
      | fr-FR  | copie de lunettes de soleil3 |
    And product "productWithFieldsSecondGroup" localized "description" for shops "shop3" should be:
      | locale | value            |
      | en-US  | nice sunglasses3 |
      | fr-FR  | belles lunettes3 |
    And product "productWithFieldsSecondGroup" localized "description_short" for shops "shop3" should be:
      | locale | value                       |
      | en-US  | Simple & nice sunglasses3   |
      | fr-FR  | lunettes simples et belles3 |
    And product "productWithFieldsSecondGroup" should have following options for shops shop3:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "productWithFieldsSecondGroup" should have following details for shops shop3:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    And product productWithFieldsSecondGroup should have following prices information for shops shop3:
      | price                   | 103.00        |
      | ecotax                  | 3.0           |
      | tax rules group         | US Tax group  |
      | on_sale                 | false         |
      | wholesale_price         | 73            |
      | unit_price              | 1030          |
      | unit_price_tax_included | 1091.80       |
      # 103 / 1030
      | unit_price_ratio        | 0.1           |
      | unity                   | bag of twenty |
    And product "productWithFieldsSecondGroup" localized "meta_title" for shops shop3 should be:
      | locale | value                  |
      | en-US  | SUNGLASSES meta title3 |
    And product "productWithFieldsSecondGroup" localized "meta_description" for shops shop3 should be:
      | locale | value         |
      | en-US  | Its so smart3 |
      | fr-FR  | lel joke3     |
    And product "productWithFieldsSecondGroup" localized "link_rewrite" for shops shop3 should be:
      | locale | value               |
      | en-US  | smart-sunglasses3   |
      | fr-FR  | lunettes-de-soleil3 |
    And product productWithFieldsSecondGroup should have following seo options for shops shop3:
      | redirect_type   | 302-product            |
      | redirect_target | productForRedirection2 |
    And product "productWithFieldsSecondGroup" should have following shipping information for shops "shop3":
      | width                                   | 10.5                  |
      | height                                  | 6                     |
      | depth                                   | 7                     |
      | weight                                  | 0.5                   |
      | additional_shipping_cost                | 12                    |
      | delivery time notes type                | specific              |
      | delivery time in stock notes[en-US]     | product in stock3     |
      | delivery time in stock notes[fr-FR]     | en stock3             |
      | delivery time out of stock notes[en-US] | product out of stock3 |
      | delivery time out of stock notes[fr-FR] | En rupture de stock3  |
      | carriers                                | []                    |
    And productWithFields and productWithFieldsSecondGroup have different values
    And productWithFieldsCopy and productWithFieldsSecondGroup have different values
    And product productWithFieldsSecondGroup is associated to shop shop3
    And product productWithFieldsSecondGroup is not associated to shop shop1
    And product productWithFieldsSecondGroup is not associated to shop shop2
    And product productWithFieldsSecondGroup is not associated to shop shop4
    # The default shop is shop3 as it's the first associated one in the second group
    And default shop for product productWithFieldsSecondGroup is shop3

  Scenario: I duplicate a product all its categories are correctly copied
    Given category "home" in default language named "Home" exists
    And category "home" is the default one
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    And I edit home category "home" with following details:
      | associated shops | shop1,shop2,shop3,shop4 |
    And I edit category "clothes" with following details:
      | associated shops | shop1,shop2,shop3,shop4 |
    And I edit category "men" with following details:
      | associated shops | shop1,shop2,shop3,shop4 |
    And I add product "productWithCategories" to shop shop1 with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I assign product productWithCategories to following categories:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    And I copy product productWithCategories from shop shop1 to shop shop2
    And I copy product productWithCategories from shop shop1 to shop shop3
    And I copy product productWithCategories from shop shop1 to shop shop4
    # Duplicate on one shop
    When I duplicate product productWithCategories to a productWithCategoriesCopyShop for shop shop1
    Then product productWithCategoriesCopyShop should be assigned to following categories for shop shop1:
      | id reference | name    | is default |
      | home         | Home    | false      |
      | men          | Men     | false      |
      | clothes      | Clothes | true       |
    # Duplicate on one shop group
    When I duplicate product productWithCategories to a productWithCategoriesCopyShopGroup for shop group test_second_shop_group
    Then product productWithCategoriesCopyShopGroup should be assigned to following categories for shops "shop3,shop4":
      | id reference | name    | is default |
      | home         | Home    | false      |
      | men          | Men     | false      |
      | clothes      | Clothes | true       |
    # Duplicate on all shops
    When I duplicate product productWithCategories to a productWithCategoriesCopyAllShops for all shops
    Then product productWithCategoriesCopyAllShops should be assigned to following categories for shops "shop1,shop2,shop3,shop4":
      | id reference | name    | is default |
      | home         | Home    | false      |
      | men          | Men     | false      |
      | clothes      | Clothes | true       |

  Scenario: I duplicate a product its carriers are copied
    Given I add product "productWithCarriers" to shop shop1 with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I assign product productWithCarriers with following carriers:
      | carrier1 |
      | carrier2 |
    And I copy product productWithCarriers from shop shop1 to shop shop2
    And I copy product productWithCarriers from shop shop1 to shop shop3
    And I copy product productWithCarriers from shop shop1 to shop shop4
    # Duplicate to one shop
    When I duplicate product productWithCarriers to a productWithCarriersCopyShop for shop shop1
    And product "productWithCarriersCopyShop" should have following shipping information for shop "shop1":
      | carriers | [carrier1,carrier2] |
    # Duplicate to one shop group
    When I duplicate product productWithCarriers to a productWithCarriersCopyShopGroup for shop group test_second_shop_group
    And product "productWithCarriersCopyShopGroup" should have following shipping information for shops "shop3,shop4":
      | carriers | [carrier1,carrier2] |
    # Duplicate to all shops
    When I duplicate product productWithCarriers to a productWithCarriersCopyAllShops for all shops
    And product "productWithCarriersCopyAllShops" should have following shipping information for shops "shop1,shop2,shop3,shop4":
      | carriers | [carrier1,carrier2] |

  Scenario: I duplicate a product its stock is copied
    When I add product "productWithStock" to shop shop1 with following information:
      | name[en-US] | smart sunglasses   |
      | name[fr-FR] | lunettes de soleil |
      | type        | standard           |
    And I copy product productWithStock from shop shop1 to shop shop2
    And I copy product productWithStock from shop shop1 to shop shop3
    And I copy product productWithStock from shop shop1 to shop shop4
    When I update product "productWithStock" stock for shop shop1 with following information:
      | delta_quantity | 10     |
      | location       | shelf1 |
    And I update product "productWithStock" stock for shop shop1 with following information:
      | delta_quantity | -9 |
    And I update product "productWithStock" stock for shop shop2 with following information:
      | delta_quantity | 12     |
      | location       | shelf2 |
    And I update product "productWithStock" stock for shop shop2 with following information:
      | delta_quantity | -10 |
    # Shop3 and shop4 share the same stock
    And I update product "productWithStock" stock for shop shop3 with following information:
      | delta_quantity | 15           |
      | location       | shared shelf |
    And I update product "productWithStock" stock for shop shop4 with following information:
      | delta_quantity | -12 |
    # Check initial quantity for each shop
    Then product "productWithStock" should have following stock information for shop shop1:
      | quantity | 1      |
      | location | shelf1 |
    And product "productWithStock" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | -9             |
      | Puff Daddy | 10             |
    And product "productWithStock" should have following stock information for shop shop2:
      | quantity | 2      |
      | location | shelf2 |
    And product "productWithStock" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puff Daddy | -10            |
      | Puff Daddy | 12             |
    And product "productWithStock" should have following stock information for shops "shop3,shop4":
      | quantity | 3            |
      | location | shared shelf |
    And product "productWithStock" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | -12            |
      | Puff Daddy | 15             |
    # Duplicate to one shop
    When I duplicate product productWithStock to a productWithStockCopy1 for shop shop1
    Then product "productWithStockCopy1" should have following stock information for shop shop1:
      | quantity | 1      |
      | location | shelf1 |
    And product "productWithStockCopy1" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 1              |
    # Duplicate to shop group
    When I duplicate product productWithStock to a productWithStockCopyShopGroup1 for shop group default_shop_group
    Then product "productWithStockCopyShopGroup1" should have following stock information for shop shop1:
      | quantity | 1      |
      | location | shelf1 |
    And product "productWithStockCopyShopGroup1" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 1              |
    And product "productWithStockCopyShopGroup1" should have following stock information for shop shop2:
      | quantity | 2      |
      | location | shelf2 |
    And product "productWithStockCopyShopGroup1" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 2              |
    # Duplicate to shop group sharing stock, check that the stock are correctly shared by modifying it
    When I duplicate product productWithStock to a productWithStockCopyShopGroup2 for shop group test_second_shop_group
    Then product "productWithStockCopyShopGroup2" should have following stock information for shops "shop3,shop4":
      | quantity | 3            |
      | location | shared shelf |
    And product "productWithStockCopyShopGroup2" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 3              |
    When I update product "productWithStockCopyShopGroup2" stock for shop shop4 with following information:
      | delta_quantity | 39 |
    Then product "productWithStockCopyShopGroup2" should have following stock information for shops "shop3,shop4":
      | quantity | 42           |
      | location | shared shelf |
    And product "productWithStockCopyShopGroup2" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 39             |
      | Puff Daddy | 3              |
    # Duplicate for all shops
    When I duplicate product productWithStock to a productWithStockCopyAllShops for all shops
    Then product "productWithStockCopyAllShops" should have following stock information for shop shop1:
      | quantity | 1      |
      | location | shelf1 |
    And product "productWithStockCopyAllShops" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 1              |
    And product "productWithStockCopyAllShops" should have following stock information for shop shop2:
      | quantity | 2      |
      | location | shelf2 |
    And product "productWithStockCopyAllShops" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 2              |
    And product "productWithStockCopyAllShops" should have following stock information for shops "shop3,shop4":
      | quantity | 3            |
      | location | shared shelf |
    And product "productWithStockCopyAllShops" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 3              |

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
    And I copy product productWithCombinationAndStock from shop shop1 to shop shop2
    And I copy product productWithCombinationAndStock from shop shop1 to shop shop3
    And I copy product productWithCombinationAndStock from shop shop1 to shop shop4
    # Update stock for shop1
    And I update combination "productWithCombinationsRed" stock for shop shop1 with following details:
      | delta quantity | 10        |
      | location       | redshelf1 |
    And I update combination "productWithCombinationsRed" stock for shop shop1 with following details:
      | delta quantity | -9 |
    And I update combination "productWithCombinationsBlue" stock for shop shop1 with following details:
      | delta quantity | 20         |
      | location       | blueshelf1 |
    And I update combination "productWithCombinationsBlue" stock for shop shop1 with following details:
      | delta quantity | -9 |
    # Update stock for shop2
    And I update combination "productWithCombinationsRed" stock for shop shop2 with following details:
      | delta quantity | 12        |
      | location       | redshelf2 |
    And I update combination "productWithCombinationsRed" stock for shop shop2 with following details:
      | delta quantity | -10 |
    And I update combination "productWithCombinationsBlue" stock for shop shop2 with following details:
      | delta quantity | 22         |
      | location       | blueshelf2 |
    And I update combination "productWithCombinationsBlue" stock for shop shop2 with following details:
      | delta quantity | -10 |
    # Update stock for shop3 and shop4
    And I update combination "productWithCombinationsRed" stock for shop shop3 with following details:
      | delta quantity | 15        |
      | location       | redshelf3 |
    And I update combination "productWithCombinationsRed" stock for shop shop4 with following details:
      | delta quantity | -12 |
    And I update combination "productWithCombinationsBlue" stock for shop shop3 with following details:
      | delta quantity | 25         |
      | location       | blueshelf4 |
    And I update combination "productWithCombinationsBlue" stock for shop shop4 with following details:
      | delta quantity | -12 |
    # Check initial stock before duplicating for shop1
    Then combination "productWithCombinationsRed" should have following stock details for shop shop1:
      | combination stock detail   | value     |
      | quantity                   | 1         |
      | location                   | redshelf1 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRed" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | -9             |
      | Puff Daddy | 10             |
    And combination "productWithCombinationsBlue" should have following stock details for shop shop1:
      | combination stock detail   | value      |
      | quantity                   | 11         |
      | location                   | blueshelf1 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlue" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | -9             |
      | Puff Daddy | 20             |
    # Check initial stock before duplicating for shop2
    Then combination "productWithCombinationsRed" should have following stock details for shop shop2:
      | combination stock detail   | value     |
      | quantity                   | 2         |
      | location                   | redshelf2 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRed" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puff Daddy | -10            |
      | Puff Daddy | 12             |
    And combination "productWithCombinationsBlue" should have following stock details for shop shop2:
      | combination stock detail   | value      |
      | quantity                   | 12         |
      | location                   | blueshelf2 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlue" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puff Daddy | -10            |
      | Puff Daddy | 22             |
    # Check initial stock before duplicating for shop3 and shop4
    Then combination "productWithCombinationsRed" should have following stock details for shops "shop3,shop4":
      | combination stock detail   | value     |
      | quantity                   | 3         |
      | location                   | redshelf3 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRed" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | -12            |
      | Puff Daddy | 15             |
    And combination "productWithCombinationsBlue" should have following stock details for shops "shop3,shop4":
      | combination stock detail   | value      |
      | quantity                   | 13         |
      | location                   | blueshelf4 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlue" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | -12            |
      | Puff Daddy | 25             |
    # Duplicate to one shop
    When I duplicate product productWithCombinationAndStock to a productWithCombinationAndStockCopy1 for shop shop1
    Then product "productWithCombinationAndStockCopy1" should have the following combinations for shop shop1:
      | id reference                     | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRedCopy1  | Color - Red      |           | [Color:Red]  | 0               | 1        | true       |
      | productWithCombinationsBlueCopy1 | Color - Blue     |           | [Color:Blue] | 0               | 11       | false      |
    And product "productWithCombinationAndStockCopy1" should have no combinations for shops "shop2"
    And product "productWithCombinationAndStockCopy1" should have no combinations for shops "shop3"
    And product "productWithCombinationAndStockCopy1" should have no combinations for shops "shop4"
    And productWithCombinationsRed and productWithCombinationsRedCopy1 have different values
    And productWithCombinationsBlue and productWithCombinationsBlueCopy1 have different values
    And combination "productWithCombinationsRedCopy1" should have following stock details for shop shop1:
      | combination stock detail   | value     |
      | quantity                   | 1         |
      | location                   | redshelf1 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRedCopy1" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 1              |
    And combination "productWithCombinationsBlueCopy1" should have following stock details for shop shop1:
      | combination stock detail   | value      |
      | quantity                   | 11         |
      | location                   | blueshelf1 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlueCopy1" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 11             |
    # Duplicate to shop group
    When I duplicate product productWithCombinationAndStock to a productWithCombinationAndStockCopyShopGroup1 for shop group default_shop_group
    Then product "productWithCombinationAndStockCopyShopGroup1" should have the following combinations for shop shop1:
      | id reference                              | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRedCopyShopGroup1  | Color - Red      |           | [Color:Red]  | 0               | 1        | true       |
      | productWithCombinationsBlueCopyShopGroup1 | Color - Blue     |           | [Color:Blue] | 0               | 11       | false      |
    And product "productWithCombinationAndStockCopyShopGroup1" should have the following combinations for shop shop2:
      | combination reference                     | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRedCopyShopGroup1  | Color - Red      |           | [Color:Red]  | 0               | 2        | true       |
      | productWithCombinationsBlueCopyShopGroup1 | Color - Blue     |           | [Color:Blue] | 0               | 12       | false      |
    And product "productWithCombinationAndStockCopyShopGroup1" should have no combinations for shops "shop3"
    And product "productWithCombinationAndStockCopyShopGroup1" should have no combinations for shops "shop4"
    And productWithCombinationsRed and productWithCombinationsRedCopyShopGroup1 have different values
    And productWithCombinationsBlue and productWithCombinationsBlueCopyShopGroup1 have different values
    ## Check values for shop1
    And combination "productWithCombinationsRedCopyShopGroup1" should have following stock details for shop shop1:
      | combination stock detail   | value     |
      | quantity                   | 1         |
      | location                   | redshelf1 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRedCopyShopGroup1" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 1              |
    And combination "productWithCombinationsBlueCopyShopGroup1" should have following stock details for shop shop1:
      | combination stock detail   | value      |
      | quantity                   | 11         |
      | location                   | blueshelf1 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlueCopyShopGroup1" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 11             |
    ## Check values for shop2
    And combination "productWithCombinationsRedCopyShopGroup1" should have following stock details for shop shop2:
      | combination stock detail   | value     |
      | quantity                   | 2         |
      | location                   | redshelf2 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRedCopyShopGroup1" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 2              |
    And combination "productWithCombinationsBlueCopyShopGroup1" should have following stock details for shop shop2:
      | combination stock detail   | value      |
      | quantity                   | 12         |
      | location                   | blueshelf2 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlueCopyShopGroup1" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 12             |
    # Duplicate to shop group sharing stock, check that the stock are correctly shared by modifying it
    When I duplicate product productWithCombinationAndStock to a productWithCombinationAndStockCopyShopGroup2 for shop group test_second_shop_group
    Then product "productWithCombinationAndStockCopyShopGroup2" should have the following combinations for shops "shop3,shop4":
      | id reference                              | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRedCopyShopGroup2  | Color - Red      |           | [Color:Red]  | 0               | 3        | true       |
      | productWithCombinationsBlueCopyShopGroup2 | Color - Blue     |           | [Color:Blue] | 0               | 13       | false      |
    And product "productWithCombinationAndStockCopyShopGroup2" should have no combinations for shops "shop1"
    And product "productWithCombinationAndStockCopyShopGroup2" should have no combinations for shops "shop2"
    And productWithCombinationsRed and productWithCombinationsRedCopyShopGroup2 have different values
    And productWithCombinationsBlue and productWithCombinationsBlueCopyShopGroup2 have different values
    ## Check values for shop3 and shop4
    Then combination "productWithCombinationsRedCopyShopGroup2" should have following stock details for shops "shop3,shop4":
      | combination stock detail   | value     |
      | quantity                   | 3         |
      | location                   | redshelf3 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRedCopyShopGroup2" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 3              |
    And combination "productWithCombinationsBlueCopyShopGroup2" should have following stock details for shops "shop3,shop4":
      | combination stock detail   | value      |
      | quantity                   | 13         |
      | location                   | blueshelf4 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlueCopyShopGroup2" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 13             |
    ## Check that stock are shared on both shops
    When I update combination "productWithCombinationsBlueCopyShopGroup2" stock for shop shop4 with following details:
      | delta quantity | 10 |
    Then combination "productWithCombinationsBlueCopyShopGroup2" should have following stock details for shops "shop3,shop4":
      | combination stock detail   | value      |
      | quantity                   | 23         |
      | location                   | blueshelf4 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlueCopyShopGroup2" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 10             |
      | Puff Daddy | 13             |
    # Duplicate for all shops
    When I duplicate product productWithCombinationAndStock to a productWithCombinationAndStockCopyAllShops for all shops
    Then product "productWithCombinationAndStockCopyAllShops" should have the following combinations for shop shop1:
      | id reference                            | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRedCopyAllShops  | Color - Red      |           | [Color:Red]  | 0               | 1        | true       |
      | productWithCombinationsBlueCopyAllShops | Color - Blue     |           | [Color:Blue] | 0               | 11       | false      |
    And product "productWithCombinationAndStockCopyAllShops" should have the following combinations for shop shop2:
      | combination reference                   | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRedCopyAllShops  | Color - Red      |           | [Color:Red]  | 0               | 2        | true       |
      | productWithCombinationsBlueCopyAllShops | Color - Blue     |           | [Color:Blue] | 0               | 12       | false      |
    And product "productWithCombinationAndStockCopyAllShops" should have the following combinations for shops "shop3,shop4":
      | combination reference                   | combination name | reference | attributes   | impact on price | quantity | is default |
      | productWithCombinationsRedCopyAllShops  | Color - Red      |           | [Color:Red]  | 0               | 3        | true       |
      | productWithCombinationsBlueCopyAllShops | Color - Blue     |           | [Color:Blue] | 0               | 13       | false      |
    ## Check values for shop1
    And combination "productWithCombinationsRedCopyAllShops" should have following stock details for shop shop1:
      | combination stock detail   | value     |
      | quantity                   | 1         |
      | location                   | redshelf1 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRedCopyAllShops" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 1              |
    And combination "productWithCombinationsBlueCopyAllShops" should have following stock details for shop shop1:
      | combination stock detail   | value      |
      | quantity                   | 11         |
      | location                   | blueshelf1 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlueCopyAllShops" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 11             |
    ## Check values for shop2
    And combination "productWithCombinationsRedCopyAllShops" should have following stock details for shop shop2:
      | combination stock detail   | value     |
      | quantity                   | 2         |
      | location                   | redshelf2 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRedCopyAllShops" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 2              |
    And combination "productWithCombinationsBlueCopyAllShops" should have following stock details for shop shop2:
      | combination stock detail   | value      |
      | quantity                   | 12         |
      | location                   | blueshelf2 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlueCopyAllShops" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puff Daddy | 12             |
    ## Check values for shop3 and shop4
    And combination "productWithCombinationsRedCopyAllShops" should have following stock details for shops "shop3,shop4":
      | combination stock detail   | value     |
      | quantity                   | 3         |
      | location                   | redshelf3 |
      | minimal quantity           | 1         |
      | low stock threshold        | 0         |
      | low stock alert is enabled | false     |
      | available date             |           |
    And combination "productWithCombinationsRedCopyAllShops" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 3              |
    And combination "productWithCombinationsBlueCopyAllShops" should have following stock details for shops "shop3,shop4":
      | combination stock detail   | value      |
      | quantity                   | 13         |
      | location                   | blueshelf4 |
      | minimal quantity           | 1          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             |            |
    And combination "productWithCombinationsBlueCopyAllShops" last stock movements for shops "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 13             |

  Scenario: I duplicate a product with customization, the fields are copied along with their multishop translations
    Given I add product "productWithCustomizationFields" to shop "shop1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And I copy product productWithCustomizationFields from shop shop1 to shop shop2
    And I copy product productWithCustomizationFields from shop shop1 to shop shop3
    And I copy product productWithCustomizationFields from shop shop1 to shop shop4
    And I update product productWithCustomizationFields with following customization fields for shop shop1:
      | reference    | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1 | text | field1 shop1 | champ1 shop1 | true        |
      | customField2 | file | field2 shop1 | champ2 shop1 | false       |
    And I update product productWithCustomizationFields with following customization fields for shop shop2:
      | reference    | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1 | text | field1 shop2 | champ1 shop2 | true        |
      | customField2 | file | field2 shop2 | champ2 shop2 | false       |
    And I update product productWithCustomizationFields with following customization fields for shop shop3:
      | reference    | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1 | text | field1 shop3 | champ1 shop3 | true        |
      | customField2 | file | field2 shop3 | champ2 shop3 | false       |
    And I update product productWithCustomizationFields with following customization fields for shop shop4:
      | reference    | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1 | text | field1 shop4 | champ1 shop4 | true        |
      | customField2 | file | field2 shop4 | champ2 shop4 | false       |
    # The customizability is always the same for all shops since they share the same fields
    Then product "productWithCustomizationFields" should require customization for shops "shop1,shop2,shop3,shop4"
    And product productWithCustomizationFields should have 1 customizable text field for shops "shop1,shop2,shop3,shop4"
    And product productWithCustomizationFields should have 1 customizable file field for shops "shop1,shop2,shop3,shop4"
    And product productWithCustomizationFields should have following customization fields for shop "shop1":
      | reference    | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1 | text | field1 shop1 | champ1 shop1 | true        |
      | customField2 | file | field2 shop1 | champ2 shop1 | false       |
    And product productWithCustomizationFields should have following customization fields for shop "shop2":
      | reference    | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1 | text | field1 shop2 | champ1 shop2 | true        |
      | customField2 | file | field2 shop2 | champ2 shop2 | false       |
    And product productWithCustomizationFields should have following customization fields for shop "shop3":
      | reference    | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1 | text | field1 shop3 | champ1 shop3 | true        |
      | customField2 | file | field2 shop3 | champ2 shop3 | false       |
    And product productWithCustomizationFields should have following customization fields for shop "shop4":
      | reference    | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1 | text | field1 shop4 | champ1 shop4 | true        |
      | customField2 | file | field2 shop4 | champ2 shop4 | false       |
    # Copy for all shops
    When I duplicate product productWithCustomizationFields to a productWithCustomizationFieldsCopyAllShops for all shops
    Then product "productWithCustomizationFieldsCopyAllShops" should require customization for shops "shop1,shop2,shop3,shop4"
    And product productWithCustomizationFieldsCopyAllShops should have 1 customizable text field for shops "shop1,shop2,shop3,shop4"
    And product productWithCustomizationFieldsCopyAllShops should have 1 customizable file field for shops "shop1,shop2,shop3,shop4"
    And product productWithCustomizationFieldsCopyAllShops should have following customization fields for shop "shop1":
      | new reference    | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1Copy | text | field1 shop1 | champ1 shop1 | true        |
      | customField2Copy | file | field2 shop1 | champ2 shop1 | false       |
    And product productWithCustomizationFieldsCopyAllShops should have following customization fields for shop "shop2":
      | reference        | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1Copy | text | field1 shop2 | champ1 shop2 | true        |
      | customField2Copy | file | field2 shop2 | champ2 shop2 | false       |
    And product productWithCustomizationFieldsCopyAllShops should have following customization fields for shop "shop3":
      | reference        | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1Copy | text | field1 shop3 | champ1 shop3 | true        |
      | customField2Copy | file | field2 shop3 | champ2 shop3 | false       |
    And product productWithCustomizationFieldsCopyAllShops should have following customization fields for shop "shop4":
      | reference        | type | name[en-US]  | name[fr-FR]  | is required |
      | customField1Copy | text | field1 shop4 | champ1 shop4 | true        |
      | customField2Copy | file | field2 shop4 | champ2 shop4 | false       |
    And customField1 and customField1Copy have different values
    And customField2 and customField2Copy have different values
