# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-shipping
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-multi-shop
@update-multi-shop-shipping
Feature: Update product shipping information from Back Office (BO) for multiple shops.
  As a BO user I want to be able to update product fields associated with shipping for multiple shops.

  Background:
    Given shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop group "shopGroup2" with name "test_second_shop_group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
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
      | delivery time out of stock notes[en-US] |         |
      | carriers                                | []      |
    Given carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
    When I update product product1 shipping information with following values:
      | width                                   | 10.5                 |
      | height                                  | 6                    |
      | depth                                   | 7                    |
      | weight                                  | 0.5                  |
      | additional_shipping_cost                | 12                   |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
      | carriers                                | [carrier1,carrier2]  |
    Then product product1 should have following shipping information:
      | width                                   | 10.5                 |
      | height                                  | 6                    |
      | depth                                   | 7                    |
      | weight                                  | 0.5                  |
      | additional_shipping_cost                | 12                   |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
      | carriers                                | [carrier1,carrier2]  |
    And I copy product product1 from shop shop1 to shop shop2
    Then product product1 should have following shipping information:
      | width                                   | 10.5                 |
      | height                                  | 6                    |
      | depth                                   | 7                    |
      | weight                                  | 0.5                  |
      | additional_shipping_cost                | 12                   |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
      | carriers                                | [carrier1,carrier2]  |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product shipping information for a specific shop
    When I update product "product1" shipping information for shop "shop2" with following values:
      | width                                   | 1           |
      | height                                  | 2           |
      | depth                                   | 3           |
      | weight                                  | 0.5         |
      | additional_shipping_cost                | 3           |
      | delivery time notes type                | none        |
      | delivery time in stock notes[en-US]     | available   |
      | delivery time out of stock notes[en-US] | unavailable |
      | carriers                                | [carrier1]  |
    Then product product1 should have following shipping information for shops "shop2":
      | width                                   | 1           |
      | height                                  | 2           |
      | depth                                   | 3           |
      | weight                                  | 0.5         |
      | additional_shipping_cost                | 3           |
      | delivery time notes type                | none        |
      | delivery time in stock notes[en-US]     | available   |
      | delivery time out of stock notes[en-US] | unavailable |
      | carriers                                | [carrier1]  |
    But product product1 should have following shipping information for shops "shop1":
      | width                                   | 10.5                 |
      | height                                  | 6                    |
      | depth                                   | 7                    |
      | weight                                  | 0.5                  |
      | additional_shipping_cost                | 12                   |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
      | carriers                                | [carrier1,carrier2]  |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4

  Scenario: I update product shipping information for all associated shops
    When I update product "product1" shipping information for all shops with following values:
      | width                                   | 100           |
      | height                                  | 200           |
      | depth                                   | 300           |
      | weight                                  | 1.5           |
      | additional_shipping_cost                | 30            |
      | delivery time notes type                | default       |
      | delivery time in stock notes[en-US]     | available now |
      | delivery time out of stock notes[en-US] | not-available |
      | carriers                                | [carrier2]    |
    Then product product1 should have following shipping information for shops "shop1,shop2":
      | width                                   | 100           |
      | height                                  | 200           |
      | depth                                   | 300           |
      | weight                                  | 1.5           |
      | additional_shipping_cost                | 30            |
      | delivery time notes type                | default       |
      | delivery time in stock notes[en-US]     | available now |
      | delivery time out of stock notes[en-US] | not-available |
      | carriers                                | [carrier2]    |
    And product product1 is not associated to shop shop3
    And product product1 is not associated to shop shop4
