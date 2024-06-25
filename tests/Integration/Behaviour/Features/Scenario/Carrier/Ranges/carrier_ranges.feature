# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier --tags carrier-ranges
@restore-all-tables-before-feature
@carrier-ranges
Feature: Carrier ranges
  PrestaShop allows BO users to manage carrier ranges for shipping
  As a BO user
  I must be able to set carriers ranges

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given group "visitor" named "Visitor" exists
    Given group "guest" named "Guest" exists
    And language "en" with locale "en-US" exists
    And language with iso code "en" is the default one

  Scenario: Adding prices ranges in carrier
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
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | price                              |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then I set ranges for carrier "carrier1" called "newCarrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | 1       | 0          | 100      | 10          |
      | 1       | 100        | 200      | 20          |
      | 1       | 200        | 300      | 30          |
      | 1       | 300        | 400      | 40          |
      | 2       | 0          | 100      | 15          |
      | 2       | 100        | 200      | 25          |
    Then carrier "newCarrier1" should have the following ranges for all shops:
      | id_zone | range_from | range_to | range_price |
      | 1       | 0          | 100      | 10          |
      | 1       | 100        | 200      | 20          |
      | 1       | 200        | 300      | 30          |
      | 1       | 300        | 400      | 40          |
      | 2       | 0          | 100      | 15          |
      | 2       | 100        | 200      | 25          |

  Scenario: Adding weight ranges in carrier
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
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then I set ranges for carrier "carrier1" called "newCarrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | 1       | 0          | 100      | 10          |
      | 1       | 100        | 200      | 20          |
      | 1       | 200        | 300      | 30          |
      | 1       | 300        | 400      | 40          |
      | 2       | 0          | 100      | 15          |
      | 2       | 100        | 200      | 25          |
    Then carrier "newCarrier1" should have the following ranges for all shops:
      | id_zone | range_from | range_to | range_price |
      | 1       | 0          | 100      | 10          |
      | 1       | 100        | 200      | 20          |
      | 1       | 200        | 300      | 30          |
      | 1       | 300        | 400      | 40          |
      | 2       | 0          | 100      | 15          |
      | 2       | 100        | 200      | 25          |

  Scenario: Adding overlapping ranges in carrier
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
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then I set ranges for carrier "carrier1" called "newCarrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | 1       | 200        | 300      | 30          |
      | 1       | 0          | 100      | 10          |
      | 1       | 50         | 200      | 20          |
      | 1       | 300        | 400      | 40          |
      | 2       | 0          | 100      | 15          |
      | 2       | 100        | 200      | 25          |
    Then carrier edit should throw an error with error code "INVALID_RANGES_OVERLAPPING"

  Scenario: Get ranges for not all shops
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
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then carrier "carrier1" should have the following ranges for shop "shop1":
      | range_from | range_to | id_zone | range_price |
      | 0          | 100      | 1       | 10          |
    Then carrier edit should throw an error with error code "INVALID_SHOP_CONSTRAINT"

  Scenario: Set ranges for not all shops
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
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then I set ranges for carrier "carrier1" called "newCarrier1" with specified properties for shop "shop1":
      | id_zone | range_from | range_to | range_price |
      | 1       | 0          | 100      | 10          |
    Then carrier edit should throw an error with error code "INVALID_SHOP_CONSTRAINT"

  Scenario: Set ranges with invalid zone
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
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then I set ranges for carrier "carrier1" called "newCarrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | 0       | 0          | 100      | 10          |
    Then carrier edit should throw an error with error code "INVALID_ZONE_ID"

  Scenario: Adding prices ranges in carrier with random sorting of ranges
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
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | price                              |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then I set ranges for carrier "carrier1" called "newCarrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | 1       | 0          | 100      | 10          |
      | 1       | 300        | 400      | 40          |
      | 1       | 100        | 200      | 20          |
      | 1       | 200        | 300      | 30          |
      | 2       | 100        | 200      | 25          |
      | 2       | 0          | 100      | 15          |
    Then carrier "newCarrier1" should have the following ranges for all shops:
      | id_zone | range_from | range_to | range_price |
      | 1       | 0          | 100      | 10          |
      | 1       | 100        | 200      | 20          |
      | 1       | 200        | 300      | 30          |
      | 1       | 300        | 400      | 40          |
      | 2       | 0          | 100      | 15          |
      | 2       | 100        | 200      | 25          |

  Scenario: Adding prices ranges in carrier with different ranges by zones
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
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | price                              |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then I set ranges for carrier "carrier1" called "newCarrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | 1       | 0          | 100      | 10          |
      | 1       | 100        | 200      | 40          |
      | 2       | 0          | 20       | 15          |
      | 2       | 20         | 50       | 20          |
    Then carrier "newCarrier1" should have the following ranges for all shops:
      | id_zone | range_from | range_to | range_price |
      | 1       | 0          | 100      | 10          |
      | 1       | 100        | 200      | 40          |
      | 2       | 0          | 20       | 15          |
      | 2       | 20         | 50       | 20          |
