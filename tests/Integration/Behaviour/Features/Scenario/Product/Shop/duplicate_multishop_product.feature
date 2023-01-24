# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags duplicate-multi-shop-product
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multi-shop
@duplicate-multi-shop-product
Feature: Copy product from shop to shop.
  As a BO user I want to be able to duplicate products for specific shop, group shop and all shops

  Background:
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
    And I copy product productWithFields from shop shop1 to shop shop2
    Then product productWithFields is associated to shop shop1
    Then product productWithFields is associated to shop shop2
    And product productWithFields is not associated to shop shop3
    And product productWithFields is not associated to shop shop4
    And default shop for product productWithFields is shop1
    When I duplicate product productWithFields to a productWithFieldsCopy for shop shop2
    # Even if the initial product was active the copy is disabled by default
    Then product "productWithFieldsCopy" should be disabled for shops "shop2"
    And product "productWithFieldsCopy" type should be standard for shop shop2
    And product "productWithFieldsCopy" localized "name" for shops "shop2" should be:
      | locale | value                       |
      | en-US  | copy of smart sunglasses    |
      | fr-FR  | copie de lunettes de soleil |
    And product "productWithFieldsCopy" localized "description" for shops "shop2" should be:
      | locale | value           |
      | en-US  | nice sunglasses |
      | fr-FR  | belles lunettes |
    And product "productWithFieldsCopy" localized "description_short" for shops "shop2" should be:
      | locale | value                      |
      | en-US  | Simple & nice sunglasses   |
      | fr-FR  | lunettes simples et belles |
    And product "productWithFieldsCopy" should have following options for shops shop2:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    And product "productWithFieldsCopy" should have following details for shops shop2:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    And product productWithFieldsCopy should have following prices information for shops shop2:
      | price                   | 100.00          |
      | ecotax                  | 0               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 500             |
      | unit_price_tax_included | 520             |
      | unit_price_ratio        | 0.2             |
      | unity                   | bag of ten      |
    And product "productWithFieldsCopy" localized "meta_title" for shops shop2 should be:
      | locale | value                 |
      | en-US  | SUNGLASSES meta title |
    And product "productWithFieldsCopy" localized "meta_description" for shops shop2 should be:
      | locale | value        |
      | en-US  | Its so smart |
      | fr-FR  | lel joke     |
    And product "productWithFieldsCopy" localized "link_rewrite" for shops shop2 should be:
      | locale | value              |
      | en-US  | smart-sunglasses   |
      | fr-FR  | lunettes-de-soleil |
    And product productWithFieldsCopy should have following seo options for shops shop2:
      | redirect_type   | 301-product           |
      | redirect_target | productForRedirection |
    And product "productWithFieldsCopy" should have following shipping information for shops "shop2":
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
    And product productWithFieldsCopy is associated to shop shop2
    And product productWithFieldsCopy is not associated to shop shop1
    And product productWithFieldsCopy is not associated to shop shop3
    And product productWithFieldsCopy is not associated to shop shop4
    And default shop for product productWithFieldsCopy is shop2
