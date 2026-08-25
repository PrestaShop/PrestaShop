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
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    Given I add new zone "zone2" with following properties:
      | name    | zone2 |
      | enabled | true  |

  Scenario: Adding prices ranges in carrier
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | price                              |
      | zones            | zone1, zone2                       |
    Then I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 100      | 10          |
      | zone1   | 100        | 200      | 20          |
      | zone1   | 200        | 300      | 30          |
      | zone1   | 300        | 400      | 40          |
      | zone2   | 0          | 100      | 15          |
      | zone2   | 100        | 200      | 25          |
    Then carrier "carrier1" should have the following ranges for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 100      | 10          |
      | zone1   | 100        | 200      | 20          |
      | zone1   | 200        | 300      | 30          |
      | zone1   | 300        | 400      | 40          |
      | zone2   | 0          | 100      | 15          |
      | zone2   | 100        | 200      | 25          |

  Scenario: Adding weight ranges in carrier
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | weight                             |
      | zones            | zone1, zone2                       |
    Then I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 100      | 10          |
      | zone1   | 100        | 200      | 20          |
      | zone1   | 200        | 300      | 30          |
      | zone1   | 300        | 400      | 40          |
      | zone2   | 0          | 100      | 15          |
      | zone2   | 100        | 200      | 25          |
    And carrier "carrier1" should have the following ranges for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 100      | 10          |
      | zone1   | 100        | 200      | 20          |
      | zone1   | 200        | 300      | 30          |
      | zone1   | 300        | 400      | 40          |
      | zone2   | 0          | 100      | 15          |
      | zone2   | 100        | 200      | 25          |

  Scenario: Adding overlapping ranges in carrier
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | weight                             |
      | zones            | zone1, zone2                       |
    Then I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 200        | 300      | 30          |
      | zone1   | 0          | 100      | 10          |
      | zone1   | 50         | 200      | 20          |
      | zone1   | 300        | 400      | 40          |
      | zone2   | 0          | 100      | 15          |
      | zone2   | 100        | 200      | 25          |
    Then carrier should throw an error with error code "INVALID_RANGES_OVERLAPPING"

  Scenario: Get ranges for not all shops
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
    Then carrier "carrier1" should have the following ranges for shop "shop1":
      | range_from | range_to | id_zone | range_price |
      | 0          | 100      | 1       | 10          |
    Then carrier should throw an error with error code "INVALID_SHOP_CONSTRAINT"

  Scenario: Set ranges for not all shops
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
    Then I set ranges for carrier "carrier1" with specified properties for shop "shop1":
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 100      | 10          |
    Then carrier should throw an error with error code "INVALID_SHOP_CONSTRAINT"

  Scenario: Set ranges with invalid zone
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
    Then I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone0   | 0          | 100      | 10          |
    Then carrier should throw an error with error code "INVALID_ZONE_ID"

  Scenario: Adding prices ranges in carrier with random sorting of ranges
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | price                              |
      | zones            | zone1, zone2                       |
    Then I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 100      | 10          |
      | zone1   | 300        | 400      | 40          |
      | zone1   | 100        | 200      | 20          |
      | zone1   | 200        | 300      | 30          |
      | zone2   | 100        | 200      | 25          |
      | zone2   | 0          | 100      | 15          |
    Then carrier "carrier1" should have the following ranges for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 100      | 10          |
      | zone1   | 100        | 200      | 20          |
      | zone1   | 200        | 300      | 30          |
      | zone1   | 300        | 400      | 40          |
      | zone2   | 0          | 100      | 15          |
      | zone2   | 100        | 200      | 25          |

  Scenario: Adding prices ranges in carrier with different ranges by zones
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | price                              |
      | zones            | zone1, zone2                       |
    Then I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 100      | 10          |
      | zone1   | 100        | 200      | 40          |
      | zone2   | 0          | 20       | 15          |
      | zone2   | 20         | 50       | 20          |
    Then carrier "carrier1" should have the following ranges for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 100      | 10          |
      | zone1   | 100        | 200      | 40          |
      | zone2   | 0          | 20       | 15          |
      | zone2   | 20         | 50       | 20          |

  Scenario: Adding prices ranges without zone
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | weight                             |
      | zones            |                                    |
    Then carrier should throw an error with error code "INVALID_ZONE_MISSING"

  Scenario: Adding negative ranges in carrier
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | shippingMethod   | weight                             |
      | zones            | zone1, zone2                       |
    Then I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | -5         | 100      | 10          |
    Then carrier should throw an error with error code "INVALID_RANGE_NEGATIVE"
    When I create carrier "carrier2" with specified properties:
      | name             | Carrier 2                          |
      | shippingMethod   | weight                             |
      | zones            | zone1, zone2                       |
    Then I set ranges for carrier "carrier2" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0         | -100      | 10          |
    Then carrier should throw an error with error code "INVALID_RANGE_NEGATIVE"
