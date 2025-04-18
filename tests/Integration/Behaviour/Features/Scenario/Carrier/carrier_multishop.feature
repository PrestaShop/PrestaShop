# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier --tags carrier-multishop
@restore-all-tables-before-feature
@reset-downloads-after-feature
@reset-img-after-feature
@restore-shops-after-feature
@carrier-multishop
Feature: Carrier management
  PrestaShop allows BO users to manage carrier for shipping
  As a BO user
  I must be able to create, edit and delete carriers

  Background:
    Given group "visitor" named "Visitor" exists
    Given group "guest" named "Guest" exists
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    And language "en" with locale "en-US" exists
    And language "fr" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    # Prepare shops
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And Shop group test_second_shop_group shares its stock
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"

  Scenario: I add carrier and define a selection of shops, I can also edit them
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 2                                  |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
      | rangeBehavior    | disabled                           |
      | associatedShops  | shop1, shop3                       |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 2                                  |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
      | rangeBehavior    | disabled                           |
      | associatedShops  | shop1, shop3                       |
    Then carrier "carrier1" shouldn't have a logo
    When I edit carrier "carrier1" with specified properties:
      | associatedShops | shop2, shop4 |
    And carrier "carrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 2                                  |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
      | associatedShops  | shop2, shop4                       |
