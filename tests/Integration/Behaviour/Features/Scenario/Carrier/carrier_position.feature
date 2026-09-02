# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier --tags carrier-position
@restore-all-tables-before-feature
@clear-cache-before-feature
@carrier-position
Feature: Carrier position
  PrestaShop allows BO users to order the carriers
  As a BO user
  I must be able to position a carrier, and the other carriers must be reordered accordingly

  Background:
    Given group "visitor" named "Visitor" exists
    Given group "guest" named "Guest" exists
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    And language "en" with locale "en-US" exists
    And language with iso code "en" is the default one

  Scenario: Carriers created without position are appended at the end of the list
    When I create carrier "carrier1" with specified properties:
      | name           | Carrier 1                          |
      | grade          | 1                                  |
      | trackingUrl    | http://example.com/track.php?num=@ |
      | active         | true                               |
      | group_access   | visitor, guest                     |
      | delay[en-US]   | Shipping delay                     |
      | shippingMethod | weight                             |
      | zones          | zone1                              |
      | rangeBehavior  | disabled                           |
    And I create carrier "carrier2" with specified properties:
      | name           | Carrier 2                          |
      | grade          | 1                                  |
      | trackingUrl    | http://example.com/track.php?num=@ |
      | active         | true                               |
      | group_access   | visitor, guest                     |
      | delay[en-US]   | Shipping delay                     |
      | shippingMethod | weight                             |
      | zones          | zone1                              |
      | rangeBehavior  | disabled                           |
    Then the carriers list should be ordered as following:
      | reference |
      | carrier1  |
      | carrier2  |
    And no carriers should share the same position

  Scenario: Creating a carrier at a given position moves the carriers that follow
    When I create carrier "carrier1" with specified properties:
      | name           | Carrier 1                          |
      | grade          | 1                                  |
      | trackingUrl    | http://example.com/track.php?num=@ |
      | active         | true                               |
      | group_access   | visitor, guest                     |
      | delay[en-US]   | Shipping delay                     |
      | shippingMethod | weight                             |
      | zones          | zone1                              |
      | rangeBehavior  | disabled                           |
    And I create carrier "carrier2" with specified properties:
      | name           | Carrier 2                          |
      | grade          | 1                                  |
      | trackingUrl    | http://example.com/track.php?num=@ |
      | active         | true                               |
      | group_access   | visitor, guest                     |
      | delay[en-US]   | Shipping delay                     |
      | shippingMethod | weight                             |
      | zones          | zone1                              |
      | rangeBehavior  | disabled                           |
    And I create carrier "carrier3" with specified properties:
      | name           | Carrier 3                          |
      | grade          | 1                                  |
      | trackingUrl    | http://example.com/track.php?num=@ |
      | active         | true                               |
      | group_access   | visitor, guest                     |
      | delay[en-US]   | Shipping delay                     |
      | shippingMethod | weight                             |
      | zones          | zone1                              |
      | rangeBehavior  | disabled                           |
      | position       | 0                                  |
    Then carrier "carrier3" should be at position 0 in the carriers list
    And the carriers list should be ordered as following:
      | reference |
      | carrier3  |
      | carrier1  |
      | carrier2  |
    And no carriers should share the same position

  Scenario: Editing the position of a carrier moves the other ones
    When I create carrier "carrier1" with specified properties:
      | name           | Carrier 1                          |
      | grade          | 1                                  |
      | trackingUrl    | http://example.com/track.php?num=@ |
      | active         | true                               |
      | group_access   | visitor, guest                     |
      | delay[en-US]   | Shipping delay                     |
      | shippingMethod | weight                             |
      | zones          | zone1                              |
      | rangeBehavior  | disabled                           |
    And I create carrier "carrier2" with specified properties:
      | name           | Carrier 2                          |
      | grade          | 1                                  |
      | trackingUrl    | http://example.com/track.php?num=@ |
      | active         | true                               |
      | group_access   | visitor, guest                     |
      | delay[en-US]   | Shipping delay                     |
      | shippingMethod | weight                             |
      | zones          | zone1                              |
      | rangeBehavior  | disabled                           |
    Then the carriers list should be ordered as following:
      | reference |
      | carrier1  |
      | carrier2  |
    When I edit carrier "carrier2" with specified properties:
      | position | 0 |
    Then carrier "carrier2" should be at position 0 in the carriers list
    And the carriers list should be ordered as following:
      | reference |
      | carrier2  |
      | carrier1  |
    And no carriers should share the same position
