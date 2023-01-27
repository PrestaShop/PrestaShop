# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags duplicate-multi-shop-product
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@restore-taxes-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multi-shop
@duplicate-multi-shop-product
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
