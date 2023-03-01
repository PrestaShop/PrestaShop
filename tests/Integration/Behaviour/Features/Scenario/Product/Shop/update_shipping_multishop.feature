# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-shipping-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@clear-cache-after-feature
@product-multishop
@update-shipping-multishop
Feature: Update product shipping information from Back Office (BO) for multiple shops.
  As a BO user I want to be able to update product fields associated with shipping for multiple shops.

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "english" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop group "shopGroup2" with name "test_second_shop_group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "shopGroup2"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "shopGroup2"
    And single shop context is loaded
    Given I add product "product1" with following information:
      | name[en-US] | Last samurai dvd |
      | type        | standard         |
    And product product1 should have following shipping information:
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
      | carriers                                | []      |
    Given carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
    When I update product "product1" with following values:
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
    And I assign product product1 with following carriers:
      | carrier1 |
      | carrier2 |
    Then product product1 should have following shipping information:
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
      | carriers                                | [carrier1,carrier2]         |
    And I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product product1 should have following shipping information for shops "shop1,shop2":
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
      | carriers                                | [carrier1,carrier2]         |
    And product product1 is not associated to shops "shop3,shop4"

  Scenario: I update product shipping information for a specific shop
    When I update product "product1" for shop "shop2" with following values:
      | width                                   | 5           |
      | height                                  | 5           |
      | depth                                   | 5           |
      | weight                                  | 5           |
      | additional_shipping_cost                | 3           |
      | delivery time notes type                | none        |
      | delivery time in stock notes[en-US]     | available   |
      | delivery time in stock notes[fr-FR]     | valide      |
      | delivery time out of stock notes[en-US] | unavailable |
      | delivery time out of stock notes[fr-FR] | disparu     |
    And I assign product product1 with following carriers for shop "shop2":
      | carrier1 |
    Then product product1 should have following shipping information for shops "shop2":
      | width                                   | 5           |
      | height                                  | 5           |
      | depth                                   | 5           |
      | weight                                  | 5           |
      | additional_shipping_cost                | 3           |
      | delivery time notes type                | none        |
      | delivery time in stock notes[en-US]     | available   |
      | delivery time in stock notes[fr-FR]     | valide      |
      | delivery time out of stock notes[en-US] | unavailable |
      | delivery time out of stock notes[fr-FR] | disparu     |
      | carriers                                | [carrier1]  |
    And product product1 should have following shipping information for shops "shop1":
#     dimensions do not depend on multi shop, so they should always be updated no matter which shop is targeted
      | width                                   | 5                           |
      | height                                  | 5                           |
      | depth                                   | 5                           |
      | weight                                  | 5                           |
      | additional_shipping_cost                | 12                          |
      | delivery time notes type                | none                        |
      | delivery time in stock notes[en-US]     | product in stock            |
      | delivery time in stock notes[fr-FR]     | produit en stock            |
      | delivery time out of stock notes[en-US] | product out of stock        |
      | delivery time out of stock notes[fr-FR] | produit en rupture de stock |
      | carriers                                | [carrier1,carrier2]         |
    And product product1 is not associated to shops "shop3,shop4"

  Scenario: I update product shipping information for all associated shops
    When I update product "product1" for all shops with following values:
      | width                                   | 100           |
      | height                                  | 200           |
      | depth                                   | 300           |
      | weight                                  | 1.5           |
      | additional_shipping_cost                | 30            |
      | delivery time notes type                | default       |
      | delivery time in stock notes[en-US]     | available now |
      | delivery time in stock notes[fr-FR]     | ok            |
      | delivery time out of stock notes[en-US] | not-available |
      | delivery time out of stock notes[fr-FR] | no-ok         |
    And I assign product product1 with following carriers for all shops:
      | carrier2 |
    Then product product1 should have following shipping information for shops "shop1,shop2":
      | width                                   | 100           |
      | height                                  | 200           |
      | depth                                   | 300           |
      | weight                                  | 1.5           |
      | additional_shipping_cost                | 30            |
      | delivery time notes type                | default       |
      | delivery time in stock notes[en-US]     | available now |
      | delivery time in stock notes[fr-FR]     | ok            |
      | delivery time out of stock notes[en-US] | not-available |
      | delivery time out of stock notes[fr-FR] | no-ok         |
      | carriers                                | [carrier2]    |
    And product product1 is not associated to shop "shop3,shop4"
