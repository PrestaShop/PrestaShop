# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags product-management-shop-collection
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multishop
@product-management-shop-collection
Feature: Edit product with specific list of shops.
  As a BO user I want to be able to edit a product for specific shops.

  Background:
    Given I enable multishop feature
    And language with iso code "en" is the default one
    And language "english" with locale "en-US" exists
    And language "french" with locale "fr-FR" exists
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And manufacturer studioDesign named "Studio Design" exists
    And manufacturer graphicCorner named "Graphic Corner" exists
    And following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And language "french" with locale "fr-FR" exists
    And I add product "product" to shop "shop1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And default shop for product product is shop1
    And I set following shops for product "product":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    Then product product is associated to shops "shop1,shop2,shop3,shop4"
    And product "product" should have no images

  Scenario: I can update product information for specific shops
    #
    ## First check that the content is the same for all shops and have the default values
    #
    Then product "product" should have following options for shops "shop1,shop2,shop3,shop4":
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    Then product product should have following prices information for shops "shop1,shop2,shop3,shop4":
      | price           | 0               |
      | ecotax          | 0               |
      | tax rules group | US-FL Rate (6%) |
      | on_sale         | false           |
      | wholesale_price | 0               |
      | unit_price      | 0               |
      | unity           |                 |
    And product "product" should have following seo options for shops "shop1,shop2,shop3,shop4":
      | redirect_type   | default |
      | redirect_target |         |
    And product product should have following shipping information for shops "shop1,shop2,shop3,shop4":
      | width                                   | 0       |
      | height                                  | 0       |
      | depth                                   | 0       |
      | weight                                  | 0       |
      | additional_shipping_cost                | 0       |
      | delivery time notes type                | default |
      | delivery time in stock notes[en-US]     |         |
      | delivery time in stock notes[fr-FR]     |         |
      | delivery time out of stock notes[en-US] |         |
      | delivery time out of stock notes[fr-FR] |         |
    And product "product" should have following stock information for shops "shop1,shop2,shop3,shop4":
      | pack_stock_type     | default |
      | minimal_quantity    | 1       |
      | low_stock_threshold | 0       |
      | low_stock_alert     | false   |
      | available_date      |         |
    And product "product" should have following details:
      | product detail | value |
      | isbn           |       |
      | upc            |       |
      | ean13          |       |
      | mpn            |       |
      | reference      |       |
    # Product status
    And product "product" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product" should not be indexed for shops "shop1,shop2,shop3,shop4"
    ## Multilang fields
    Then product "product" localized "name" for shops "shop1,shop2,shop3,shop4" should be:
      | locale | value       |
      | en-US  | magic staff |
      | fr-FR  |             |
    And product "product" localized "description" for shops "shop1,shop2,shop3,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product" localized "description_short" for shops "shop1,shop2,shop3,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    Then product "product" localized "meta_title" for shops "shop1,shop2,shop3,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product" localized "meta_description" for shops "shop1,shop2,shop3,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product" localized "link_rewrite" for shops "shop1,shop2,shop3,shop4" should be:
      | locale | value       |
      | en-US  | magic-staff |
      | fr-FR  |             |
    And product "product" localized "available_now_labels" for shops "shop1,shop2,shop3,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product" localized "available_later_labels" for shops "shop1,shop2,shop3,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    #
    ## Now update fields for shop2 and shop3
    #
    When I update product "product" for shops "shop2,shop3" with following values:
      # Basic information
      | name[en-US]                             | cool magic staff                |
      | name[fr-FR]                             | baton magique cool              |
      | description[en-US]                      | such a cool magic staff         |
      | description[fr-FR]                      | tellement cool ce baton magique |
      | description_short[en-US]                | cool magic staff                |
      | description_short[fr-FR]                | baton magique cool              |
      # Options
      | visibility                              | search                          |
      | available_for_order                     | false                           |
      | online_only                             | true                            |
      | show_price                              | false                           |
      | condition                               | used                            |
      | show_condition                          | true                            |
      | manufacturer                            | studioDesign                    |
      # Prices
      | price                                   | 100.99                          |
      | ecotax                                  | 0                               |
      | tax rules group                         | US-AL Rate (4%)                 |
      | on_sale                                 | true                            |
      | wholesale_price                         | 70                              |
      | unit_price                              | 10                              |
      | unity                                   | bag of ten                      |
      # SEO
      | meta_title[en-US]                       | magic staff meta title          |
      | meta_description[en-US]                 | magic staff meta description    |
      | link_rewrite[en-US]                     | magic-staff                     |
      | meta_title[fr-FR]                       | titre baton magique cool        |
      | meta_description[fr-FR]                 | description baton magique cool  |
      | link_rewrite[fr-FR]                     | baton-magique-cool              |
      | redirect_type                           | 404                             |
      | redirect_target                         |                                 |
      # Shipping
      | width                                   | 10.5                            |
      | height                                  | 6                               |
      | depth                                   | 7                               |
      | weight                                  | 0.5                             |
      | additional_shipping_cost                | 12                              |
      | delivery time notes type                | specific                        |
      | delivery time in stock notes[en-US]     | product in stock                |
      | delivery time in stock notes[fr-FR]     | produit en stock                |
      | delivery time out of stock notes[en-US] | product out of stock            |
      | delivery time out of stock notes[fr-FR] | produit en rupture de stock     |
      # Stock
      | pack_stock_type                         | products_only                   |
      | minimal_quantity                        | 24                              |
      | low_stock_threshold                     | 0                               |
      | low_stock_alert                         | false                           |
      | available_date                          | 1969-09-16                      |
      | available_now_labels[en-US]             | get it now                      |
      | available_now_labels[fr-FR]             | chope le maintenant             |
      | available_later_labels[en-US]           | too late bro                    |
      | available_later_labels[fr-FR]           | trop tard mec                   |
      # Details common to all shops
      | isbn                                    | 978-3-16-148410-0               |
      | upc                                     | 72527273070                     |
      | ean13                                   | 978020137962                    |
      | mpn                                     | mpn1                            |
      | reference                               | ref1                            |
      # Status
      | active                                  | true                            |
    #
    ## Now check the content for shop2 and shop3 match with the provided udpates
    #
    Then product "product" should have following options for shops "shop2,shop3":
      | product option      | value        |
      | visibility          | search       |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    Then product product should have following prices information for shops "shop2,shop3":
      | price           | 100.99          |
      | ecotax          | 0               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 70              |
      | unit_price      | 10              |
      | unity           | bag of ten      |
    And product "product" should have following seo options for shops "shop2,shop3":
      | redirect_type   | 404 |
      | redirect_target |     |
    And product product should have following shipping information for shops "shop2,shop3":
      | width                                   | 10.5                        |
      | height                                  | 6                           |
      | depth                                   | 7                           |
      | weight                                  | 0.5                         |
      | additional_shipping_cost                | 12                          |
      | delivery time notes type                | specific                    |
      | delivery time in stock notes[en-US]     | product in stock            |
      | delivery time in stock notes[fr-FR]     | produit en stock            |
      | delivery time out of stock notes[en-US] | product out of stock        |
      | delivery time out of stock notes[fr-FR] | produit en rupture de stock |
    And product "product" should have following stock information for shops "shop2,shop3":
      | pack_stock_type     | products_only |
      | minimal_quantity    | 24            |
      | low_stock_threshold | 0             |
      | low_stock_alert     | false         |
      | available_date      | 1969-09-16    |
    And product "product" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    # Product status
    And product "product" should be enabled for shops "shop2,shop3"
    And product "product" should be indexed for shops "shop2,shop3"
    ## Multilang fields
    Then product "product" localized "name" for shops "shop2,shop3" should be:
      | locale | value              |
      | en-US  | cool magic staff   |
      | fr-FR  | baton magique cool |
    And product "product" localized "description" for shops "shop2,shop3" should be:
      | locale | value                           |
      | en-US  | such a cool magic staff         |
      | fr-FR  | tellement cool ce baton magique |
    And product "product" localized "description_short" for shops "shop2,shop3" should be:
      | locale | value              |
      | en-US  | cool magic staff   |
      | fr-FR  | baton magique cool |
    Then product "product" localized "meta_title" for shops "shop2,shop3" should be:
      | locale | value                    |
      | en-US  | magic staff meta title   |
      | fr-FR  | titre baton magique cool |
    And product "product" localized "meta_description" for shops "shop2,shop3" should be:
      | locale | value                          |
      | en-US  | magic staff meta description   |
      | fr-FR  | description baton magique cool |
    And product "product" localized "link_rewrite" for shops "shop2,shop3" should be:
      | locale | value              |
      | en-US  | magic-staff        |
      | fr-FR  | baton-magique-cool |
    And product "product" localized "available_now_labels" for shops "shop2,shop3" should be:
      | locale | value               |
      | en-US  | get it now          |
      | fr-FR  | chope le maintenant |
    And product "product" localized "available_later_labels" for shops "shop2,shop3" should be:
      | locale | value         |
      | en-US  | too late bro  |
      | fr-FR  | trop tard mec |
    #
    ## Now check the content for shop1 and shop4, it should still be the default values everywhere
    ## except for the values that are common to all shops
    #
    Then product "product" should have following options for shops "shop1,shop4":
      | product option      | value        |
      | visibility          | both         |
      | available_for_order | true         |
      | online_only         | false        |
      | show_price          | true         |
      | condition           | new          |
      | show_condition      | false        |
      | manufacturer        | studioDesign |
    Then product product should have following prices information for shops "shop1,shop4":
      | price           | 0               |
      | ecotax          | 0               |
      | tax rules group | US-FL Rate (6%) |
      | on_sale         | false           |
      | wholesale_price | 0               |
      | unit_price      | 0               |
      | unity           |                 |
    And product "product" should have following seo options for shops "shop1,shop4":
      | redirect_type   | default |
      | redirect_target |         |
    And product product should have following shipping information for shops "shop1,shop4":
      | width                                   | 10.5     |
      | height                                  | 6        |
      | depth                                   | 7        |
      | weight                                  | 0.5      |
      | additional_shipping_cost                | 0        |
      | delivery time notes type                | specific |
      | delivery time in stock notes[en-US]     |          |
      | delivery time in stock notes[fr-FR]     |          |
      | delivery time out of stock notes[en-US] |          |
      | delivery time out of stock notes[fr-FR] |          |
    And product "product" should have following stock information for shops "shop1,shop4":
      | pack_stock_type     | default |
      | minimal_quantity    | 1       |
      | low_stock_threshold | 0       |
      | low_stock_alert     | false   |
      | available_date      |         |
    And product "product" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    # Product status
    And product "product" should be disabled for shops "shop1,shop4"
    And product "product" should not be indexed for shops "shop1,shop4"
    ## Multilang fields
    Then product "product" localized "name" for shops "shop1,shop4" should be:
      | locale | value       |
      | en-US  | magic staff |
      | fr-FR  |             |
    And product "product" localized "description" for shops "shop1,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product" localized "description_short" for shops "shop1,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    Then product "product" localized "meta_title" for shops "shop1,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product" localized "meta_description" for shops "shop1,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product" localized "link_rewrite" for shops "shop1,shop4" should be:
      | locale | value       |
      | en-US  | magic-staff |
      | fr-FR  |             |
    And product "product" localized "available_now_labels" for shops "shop1,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product" localized "available_later_labels" for shops "shop1,shop4" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |

  Scenario: I can update product stock for specific shops
    Given product "product" should have following stock information for shops "shop1,shop2,shop3,shop4":
      | out_of_stock_type | default |
      | quantity          | 0       |
      | location          |         |
    When I update product "product" stock for shops "shop2,shop3" with following information:
      | out_of_stock_type | not_available |
      | delta_quantity    | 69            |
      | location          | upa           |
    Then product "product" should have following stock information for shops "shop2,shop3":
      | out_of_stock_type | not_available |
      | location          | upa           |
      | quantity          | 69            |
    And product "product" last stock movements for shops "shop2,shop3" should be:
      | employee   | delta_quantity |
      | Puffin Mummy | 69             |
    And product "product" should have following stock information for shops "shop1,shop4":
      | out_of_stock_type | default |
      | quantity          | 0       |
      | location          |         |
    And product "product" should have no stock movements for shops "shop1,shop4"
    # Update different shops and check the appropriate stock and movements for each one
    When I update product "product" stock for shops "shop1,shop2" with following information:
      | delta_quantity | 12      |
      | location       | nowhere |
    # Shop1
    Then product "product" should have following stock information for shop shop1:
      | location | nowhere |
      | quantity | 12      |
    And product "product" last stock movements for shop shop1 should be:
      | employee   | delta_quantity |
      | Puffin Mummy | 12             |
    # Shop2
    And product "product" should have following stock information for shop shop2:
      | location | nowhere |
      | quantity | 81      |
    And product "product" last stock movements for shop shop2 should be:
      | employee   | delta_quantity |
      | Puffin Mummy | 12             |
      | Puffin Mummy | 69             |
    # Shop3
    And product "product" should have following stock information for shop shop3:
      | location | upa |
      | quantity | 69  |
    And product "product" last stock movements for shop shop3 should be:
      | employee   | delta_quantity |
      | Puffin Mummy | 69             |
    # Shop4
    And product "product" should have following stock information for shop shop4:
      | location |   |
      | quantity | 0 |
    And product "product" should have no stock movements for shop shop4

  Scenario: I can upload and edit images for specific shops
    When I add new image "image1" named "app_icon.png" to product "product" for shops "shop2,shop3"
    Then image "image1" should have same file as "app_icon.png"
    When I add new image "image2" named "logo.jpg" to product "product" for shops "shop1,shop2"
    Then image "image2" should have same file as "logo.jpg"
    When I add new image "image3" named "logo.jpg" to product "product" for shops "shop3,shop4"
    Then image "image3" should have same file as "logo.jpg"
    And images "[image1, image2,image3]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |
    # This always step returns all the images but the cover may vary depending on associations
    # Shop1 only has image2 associated so it is the cover
    And product "product" should have following images for shop "shop1":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      | shops       |
      | image1          | false    |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop2,shop3 |
      | image2          | true     |               |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1,shop2 |
      | image3          | false    |               |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop3,shop4 |
    # Shop2 and Shop3 both have image1 associated first so it is their cover
    And product "product" should have following images for shop "shop2,shop3":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      | shops       |
      | image1          | true     |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop2,shop3 |
      | image2          | false    |               |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1,shop2 |
      | image3          | false    |               |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop3,shop4 |
    # Shop4 has image1 has cover even if it is not associated because it is the global cover as the first uploaded image
    And product "product" should have following images for shop "shop4":
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      | shops       |
      | image1          | false    |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop2,shop3 |
      | image2          | false    |               |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1,shop2 |
      | image3          | true     |               |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop3,shop4 |
    # Update an image content for two shops only
    When I update image "image3" with following information for shops "shop3,shop4":
      | file          | app_icon.png       |
      | legend[en-US] | preston is alive   |
      | legend[fr-FR] | preston est vivant |
      | position      | 2                  |
      | cover         | true               |
    # All the data are common to all shops, the only multishop info for now (aside from association) is the cover
    Then image "image3" should have same file as "app_icon.png"
    And product "product" should have following images for shop "shop1":
      | image reference | is cover | legend[en-US]    | legend[fr-FR]      | position | image url                            | thumbnail url                                      | shops       |
      | image1          | false    |                  |                    | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop2,shop3 |
      | image3          | false    | preston is alive | preston est vivant | 2        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop3,shop4 |
      | image2          | true     |                  |                    | 3        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1,shop2 |
    And product "product" should have following images for shop "shop2":
      | image reference | is cover | legend[en-US]    | legend[fr-FR]      | position | image url                            | thumbnail url                                      | shops       |
      | image1          | true     |                  |                    | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop2,shop3 |
      | image3          | false    | preston is alive | preston est vivant | 2        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop3,shop4 |
      | image2          | false    |                  |                    | 3        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1,shop2 |
    And product "product" should have following images for shop "shop3":
      | image reference | is cover | legend[en-US]    | legend[fr-FR]      | position | image url                            | thumbnail url                                      | shops       |
      | image1          | false    |                  |                    | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop2,shop3 |
      | image3          | true     | preston is alive | preston est vivant | 2        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop3,shop4 |
      | image2          | false    |                  |                    | 3        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1,shop2 |
    And product "product" should have following images for shop "shop4":
      | image reference | is cover | legend[en-US]    | legend[fr-FR]      | position | image url                            | thumbnail url                                      | shops       |
      | image1          | false    |                  |                    | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop2,shop3 |
      | image3          | true     | preston is alive | preston est vivant | 2        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop3,shop4 |
      | image2          | false    |                  |                    | 3        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1,shop2 |

  Scenario: I can duplicate a product for specific shops (we don't fully check all fields)
    Given I update product "product" for shops "shop2,shop3" with following values:
      # Basic information
      | name[en-US]              | cool magic staff                |
      | name[fr-FR]              | baton magique cool              |
      | description[en-US]       | such a cool magic staff         |
      | description[fr-FR]       | tellement cool ce baton magique |
      | description_short[en-US] | cool magic staff                |
      | description_short[fr-FR] | baton magique cool              |
      # Prices
      | price                    | 100.99                          |
      | ecotax                   | 0                               |
      | tax rules group          | US-AL Rate (4%)                 |
      | on_sale                  | true                            |
      | wholesale_price          | 70                              |
      | unit_price               | 10                              |
      | unity                    | bag of ten                      |
      # SEO
      | link_rewrite[en-US]      | cool-magic-staff                |
      | link_rewrite[fr-FR]      | baton-magique-cool              |
    # Add name in french because we cannot validate "copie de " the space is trimmed for comparison
    And I update product "product" for shops "shop1,shop4" with following values:
      | name[fr-FR] | baton magique |
    Given I add new image "image1" named "app_icon.png" to product "product" for all shops
    # Duplicate product for shop1 and shop3 only
    When I duplicate product product to a productCopy for shops shop1,shop3
    Then product productCopy is associated to shops "shop1,shop3"
    And product productCopy is not associated to shops "shop2,shop4"
    And default shop for product productCopy is shop1
    And product "productCopy" should be disabled for shops "shop1,shop3"
    # Shop1
    And product productCopy should have following prices information for shop shop1:
      | price           | 0               |
      | ecotax          | 0               |
      | tax rules group | US-FL Rate (6%) |
      | on_sale         | false           |
      | wholesale_price | 0               |
      | unit_price      | 0               |
      | unity           |                 |
    And product "productCopy" localized "name" for shop "shop1" should be:
      | locale | value                  |
      | en-US  | copy of magic staff    |
      | fr-FR  | copie de baton magique |
    And product "productCopy" localized "description" for shop "shop1" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "productCopy" localized "description_short" for shop "shop1" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "productCopy" localized "link_rewrite" for shop "shop1" should be:
      | locale | value                  |
      | en-US  | copy-of-magic-staff    |
      | fr-FR  | copie-de-baton-magique |
    # Shop3
    And product productCopy should have following prices information for shop shop3:
      | price           | 100.99          |
      | ecotax          | 0               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 70              |
      | unit_price      | 10              |
      | unity           | bag of ten      |
    And product "productCopy" localized "name" for shop "shop3" should be:
      | locale | value                       |
      | en-US  | copy of cool magic staff    |
      | fr-FR  | copie de baton magique cool |
    And product "productCopy" localized "description" for shop "shop3" should be:
      | locale | value                           |
      | en-US  | such a cool magic staff         |
      | fr-FR  | tellement cool ce baton magique |
    And product "productCopy" localized "description_short" for shop "shop3" should be:
      | locale | value              |
      | en-US  | cool magic staff   |
      | fr-FR  | baton magique cool |
    And product "productCopy" localized "link_rewrite" for shop "shop3" should be:
      | locale | value                       |
      | en-US  | copy-of-cool-magic-staff    |
      | fr-FR  | copie-de-baton-magique-cool |
    # Images have been duplicated as well
    And product "productCopy" should have following images for shops "shop1,shop3":
      | new image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                                | thumbnail url                                          | shops       |
      | image1Copy          | true     |               |               | 1        | http://myshop.com/img/p/{image1Copy}.jpg | http://myshop.com/img/p/{image1Copy}-small_default.jpg | shop1,shop3 |
    And image1 and image1Copy have different values
    # Duplicate without default shop and check that it is updated
    When I duplicate product product to a otherProductCopy for shops shop2,shop3
    Then product otherProductCopy is associated to shops "shop2,shop3"
    And product otherProductCopy is not associated to shops "shop1,shop4"
    And default shop for product otherProductCopy is shop2
    And product "otherProductCopy" should be disabled for shops "shop2,shop3"

  Scenario: I can edit categories for specific shops
    Given category "home" in default language named "Home" exists
    And category "home" is the default one
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    And category "women" in default language named "Women" exists
    And category "accessories" in default language named "Accessories" exists
    And I edit home category "home" with following details:
      | associated shops | shop1,shop2,shop3,shop4 |
    And I edit category "clothes" with following details:
      | associated shops | shop1,shop2,shop3,shop4 |
    And I edit category "accessories" with following details:
      | associated shops | shop1,shop2,shop3,shop4 |
    # Men is only associated in shop1
    And I edit category "men" with following details:
      | associated shops | shop1 |
    # Women is only associated in shop2 and is the default category
    And I edit category "women" with following details:
      | associated shops | shop2 |
    And I set "women" as default category for shop shop2
    Given product "product" should be assigned to following categories for shops "shop1,shop2,shop3,shop4":
      | id reference | name | is default |
      | home         | Home | true       |
    # Now we assign categories for specific shops
    When I assign product product to following categories for shops shop1,shop3:
      | categories       | [home, men, clothes] |
      | default category | men                  |
    # Category association is shared for all shops, the only thing that may vary is if the category is in the shop, the position and the default category
    # Shop1 has the new update categories
    Then product "product" should be assigned to following categories for shop shop1:
      | id reference | name    | is default |
      | home         | Home    | false      |
      | men          | Men     | true       |
      | clothes      | Clothes | false      |
    # The other shops have a new category clothes but keeps home as default, they don't have men because it is only on shop1
    And product "product" should be assigned to following categories for shops "shop2,shop3,shop4":
      | id reference | name    | is default |
      | home         | Home    | true       |
      | clothes      | Clothes | false      |

  Scenario: I can edit customization fields for specific shops
      # New customization field creation is shared to all shops by default even if only one is selected
    When I update product product with following customization fields for shop shop1:
      | reference    | type | name[en-US] | name[fr-FR]    | is required |
      | customField1 | text | front-text  | texte-devant   | true        |
      | customField2 | text | back-text   | texte-derriere | false       |
    Then product product should have 2 customizable text field for shops "shop1,shop2,shop3,shop4"
    And product product should have 0 customizable file field for shops "shop1,shop2,shop3,shop4"
    And product product should have following customization fields for shops "shop1,shop2,shop3,shop4":
      | reference    | type | name[en-US] | name[fr-FR]    | is required |
      | customField1 | text | front-text  | texte-devant   | true        |
      | customField2 | text | back-text   | texte-derriere | false       |
    # New customization field will have same wording for all shops, only selected shops have their wordings updated
    When I update product product with following customization fields for shops shop2,shop3:
      | reference    | type | name[en-US] | name[fr-FR]     | is required |
      | customField1 | text | front-text2 | texte-devant2   | false       |
      | customField2 | text | back-text2  | texte-derriere2 | false       |
      | customField3 | file | back image  | image derriere  | false       |
    And product product should have 2 customizable text field for shops "shop1,shop2,shop3,shop4"
    And product product should have 1 customizable file field for shops "shop1,shop2,shop3,shop4"
    And product product should have following customization fields for shops "shop2,shop3":
      | reference    | type | name[en-US] | name[fr-FR]     | is required |
      | customField1 | text | front-text2 | texte-devant2   | false       |
      | customField2 | text | back-text2  | texte-derriere2 | false       |
      | customField3 | file | back image  | image derriere  | false       |
    And product product should have following customization fields for shops "shop1,shop4":
      | reference    | type | name[en-US] | name[fr-FR]    | is required |
      | customField1 | text | front-text  | texte-devant   | false       |
      | customField2 | text | back-text   | texte-derriere | false       |
      | customField3 | file | back image  | image derriere | false       |
    # Remove all removes for all shops
    When I remove all customization fields from product product
    Then product "product" should not be customizable for shops "shop1,shop2,shop3,shop4"
    Then product product should have 0 customizable text fields for shops "shop1,shop2,shop3,shop4"
    And product product should have 0 customizable file fields for shops "shop1,shop2,shop3,shop4"

  Scenario: Delete one product for specific shops
    When I delete product "product" from shops "shop1,shop3"
    Then product product is associated to shops "shop2,shop4"
    And product product is not associated to shops "shop1,shop3"
    When I delete product "product" from shops "shop2,shop4"
    And product product should not exist anymore

  Scenario: Bulk delete for specific shops
    Given I add product "product2" to shop "shop1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And default shop for product product2 is shop1
    And I set following shops for product "product2":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    Then product product2 is associated to shops "shop1,shop2,shop3,shop4"
    And I add product "product3" to shop "shop1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And default shop for product product3 is shop1
    And I set following shops for product "product3":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    Then product product3 is associated to shops "shop1,shop2,shop3,shop4"
    # Now start bulk deleting
    When I bulk delete following products from shops "shop1,shop3":
      | reference |
      | product   |
      | product3  |
    Then product product is associated to shops "shop2,shop4"
    And product product is not associated to shops "shop1,shop3"
    And product product2 is associated to shops "shop1,shop2,shop3,shop4"
    And product product3 is associated to shops "shop2,shop4"
    And product product3 is not associated to shops "shop1,shop3"
    When I bulk delete following products from shops "shop2,shop4":
      | reference |
      | product2  |
      | product3  |
    Then product product is associated to shops "shop2,shop4"
    And product product is not associated to shops "shop1,shop3"
    And product product2 is associated to shops "shop1,shop3"
    And product product2 is not associated to shops "shop2,shop4"
    And product product3 should not exist anymore

  Scenario: I update product statuses for specific shops
    Given product "product" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product" should not be indexed for shops "shop1,shop2,shop3,shop4"
    Given I add product "product2" to shop "shop1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And default shop for product product2 is shop1
    And I set following shops for product "product2":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    Then product product2 is associated to shops "shop1,shop2,shop3,shop4"
    Given product "product2" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product2" should not be indexed for shops "shop1,shop2,shop3,shop4"
    And I add product "product3" to shop "shop1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And default shop for product product3 is shop1
    And I set following shops for product "product3":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    Then product product3 is associated to shops "shop1,shop2,shop3,shop4"
    Given product "product3" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product3" should not be indexed for shops "shop1,shop2,shop3,shop4"
    # Now update status and check indexes
    When I bulk change status to be enabled for following products for shops "shop2,shop4":
      | reference |
      | product   |
      | product3  |
    Then product "product" should be enabled for shops "shop2,shop4"
    And product "product" should be indexed for shops "shop2,shop4"
    And product "product" should be disabled for shops "shop1,shop3"
    And product "product" should not be indexed for shops "shop1,shop3"
    And product "product2" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product2" should not be indexed for shops "shop1,shop2,shop3,shop4"
    Then product "product3" should be enabled for shops "shop2,shop4"
    And product "product3" should be indexed for shops "shop2,shop4"
    And product "product3" should be disabled for shops "shop1,shop3"
    And product "product3" should not be indexed for shops "shop1,shop3"
