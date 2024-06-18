# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier --tags carrier-ranges
@restore-all-tables-before-feature
@carrier-ranges
Feature: Carrier ranges
  PrestaShop allows BO users to manage carrier ranges for shipping
  As a BO user
  I must be able to set carriers ranges

  Background:
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
    Then I set ranges for carrier "carrier1" with specified properties for "all" shops:
      | range_from | range_to | id_zone | range_price |
      | 0          | 100      | 1       | 10          |
      | 0          | 100      | 2       | 15          |
      | 100        | 200      | 1       | 20          |
      | 200        | 300      | 1       | 30          |
      | 200        | 300      | 2       | 35          |
      | 300        | 400      | 1       | 40          |
    Then carrier "carrier1" should have the following ranges for "all" shops:
      | range_from | range_to | id_zone | range_price |
      | 0          | 100      | 1       | 10          |
      | 0          | 100      | 2       | 15          |
      | 100        | 200      | 1       | 20          |
      | 200        | 300      | 1       | 30          |
      | 200        | 300      | 2       | 35          |
      | 300        | 400      | 1       | 40          |

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
    Then I set ranges for carrier "carrier1" with specified properties for "all" shops:
      | range_from | range_to | id_zone | range_price |
      | 0          | 100      | 1       | 10          |
      | 0          | 100      | 2       | 15          |
      | 100        | 200      | 1       | 20          |
      | 200        | 300      | 1       | 30          |
      | 200        | 300      | 2       | 35          |
      | 300        | 400      | 1       | 40          |
    Then carrier "carrier1" should have the following ranges for "all" shops:
      | range_from | range_to | id_zone | range_price |
      | 0          | 100      | 1       | 10          |
      | 0          | 100      | 2       | 15          |
      | 100        | 200      | 1       | 20          |
      | 200        | 300      | 1       | 30          |
      | 200        | 300      | 2       | 35          |
      | 300        | 400      | 1       | 40          |

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
    Then I set ranges for carrier "carrier1" with specified properties for "all" shops:
      | range_from | range_to | id_zone | range_price |
      | 0          | 100      | 1       | 10          |
      | 0          | 100      | 2       | 15          |
      | 90         | 200      | 1       | 20          |
      | 200        | 300      | 1       | 30          |
      | 200        | 300      | 2       | 35          |
      | 300        | 400      | 1       | 40          |
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
    Then carrier "carrier1" should have the following ranges for "1" shop:
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
    Then I set ranges for carrier "carrier1" with specified properties for "1" shops:
      | range_from | range_to | id_zone | range_price |
      | 0          | 100      | 1       | 10          |
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
    Then I set ranges for carrier "carrier1" with specified properties for "all" shops:
      | range_from | range_to | id_zone | range_price |
      | 0          | 100      | 0       | 10          |
    Then carrier edit should throw an error with error code "INVALID_ZONE_ID"