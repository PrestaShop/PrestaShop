# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags update-discount-delivery-conditions
@restore-all-tables-before-feature
@restore-languages-after-feature
@update-discount-delivery-conditions
Feature: Update discount delivery conditions
  PrestaShop allows BO users to update delivery conditions on discounts
  As a BO user
  I must be able to remove or switch delivery conditions after they have been set

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given language with iso code "en" is the default one
    And country "france" with iso code "FR" exists
    And country "spain" with iso code "ES" exists
    Given group "visitor" named "Visitor" exists
    Given group "guest" named "Guest" exists

  Scenario: Remove country delivery condition by switching to none
    When I create a "free_shipping" discount "discount_countries_to_none" with following properties:
      | name[en-US] | Free shipping FR+ES |
      | countries   | france,spain        |
    Then discount "discount_countries_to_none" should have the following properties:
      | countries | spain,france |
    When I update discount "discount_countries_to_none" with the following properties:
      | countries |  |
    Then discount "discount_countries_to_none" should have the following properties:
      | countries |  |

  Scenario: Remove carrier delivery condition by switching to none
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
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
    When I create a "free_shipping" discount "discount_carriers_to_none" with following properties:
      | name[en-US] | Free shipping carrier1 |
      | carriers    | carrier1               |
    Then discount "discount_carriers_to_none" should have the following properties:
      | carriers | carrier1 |
    When I update discount "discount_carriers_to_none" with the following properties:
      | carriers |  |
    Then discount "discount_carriers_to_none" should have the following properties:
      | carriers |  |

  Scenario: Switch from country condition to carrier condition clears countries
    Given I add new zone "zone2" with following properties:
      | name    | zone2 |
      | enabled | true  |
    Given I create carrier "carrier2" with specified properties:
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
      | zones            | zone2                              |
      | rangeBehavior    | disabled                           |
    When I create a "free_shipping" discount "discount_countries_to_carriers" with following properties:
      | name[en-US] | Free shipping countries |
      | countries   | france                  |
    Then discount "discount_countries_to_carriers" should have the following properties:
      | countries | france |
    When I update discount "discount_countries_to_carriers" with the following properties:
      | carriers  | carrier2 |
      | countries |          |
    Then discount "discount_countries_to_carriers" should have the following properties:
      | carriers  | carrier2 |
      | countries |          |

  Scenario: Switch from carrier condition to country condition clears carriers
    Given I add new zone "zone3" with following properties:
      | name    | zone3 |
      | enabled | true  |
    Given I create carrier "carrier3" with specified properties:
      | name             | Carrier 3                          |
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
      | zones            | zone3                              |
      | rangeBehavior    | disabled                           |
    When I create a "free_shipping" discount "discount_carriers_to_countries" with following properties:
      | name[en-US] | Free shipping carrier3 |
      | carriers    | carrier3               |
    Then discount "discount_carriers_to_countries" should have the following properties:
      | carriers | carrier3 |
    When I update discount "discount_carriers_to_countries" with the following properties:
      | countries | spain |
      | carriers  |       |
    Then discount "discount_carriers_to_countries" should have the following properties:
      | countries | spain |
      | carriers  |       |
