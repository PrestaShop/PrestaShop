# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-with-carrier-trigger
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-with-carrier-trigger
Feature: Add discount with carrier trigger
  PrestaShop allows BO users to update discounts conditions
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given language with iso code "en" is the default one
    Given group "visitor" named "Visitor" exists
    Given group "guest" named "Guest" exists

  Scenario: Create discount with conditions on carriers
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    Given I add new zone "zone2" with following properties:
      | name    | zone2 |
      | enabled | true  |
    Given I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
      | rangeBehavior    | disabled                           |
    And I create carrier "carrier2" with specified properties:
      | name             | Carrier 2                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
      | rangeBehavior    | disabled                           |
    When I create a "free_shipping" discount "discount_with_carriers" with following properties:
      | name[en-US] | Promotion |
    Then discount "discount_with_carriers" should have the following properties:
      | name[en-US] | Promotion     |
      | type        | free_shipping |
    Then discount "discount_with_carriers" should have the following properties:
      | name[en-US]              | Promotion     |
      | type                     | free_shipping |
      | minimum_product_quantity | 0             |
    And discount "discount_with_carriers" should have no product conditions
    When I update discount "discount_with_carriers" with conditions based on carriers "carrier1,carrier2"
    Then discount "discount_with_carriers" should have the following properties:
      | name[en-US] | Promotion         |
      | carriers    | carrier1,carrier2 |
