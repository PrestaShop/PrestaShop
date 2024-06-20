# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier --tags carrier-management
@restore-all-tables-before-feature
@reset-downloads-after-feature
@reset-img-after-feature
@carrier-management
Feature: Carrier management
  PrestaShop allows BO users to manage carrier for shipping
  As a BO user
  I must be able to create, edit and delete carriers

  Background:
    Given group "visitor" named "Visitor" exists
    Given group "guest" named "Guest" exists
    And language "en" with locale "en-US" exists
    And language "fr" with locale "fr-FR" exists
    And language with iso code "en" is the default one

  Scenario: Adding new Carrier
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then carrier "carrier1" shouldn't have a logo

  Scenario: Partially editing carrier with name
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | name | Carrier 1 new |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then carrier "newCarrier1" should have the following properties:
      | name             | Carrier 1 new                      |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with grade
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | grade | 2 |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                             |
    Then carrier "newCarrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 2                                  |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with tracking url
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | trackingUrl | http://prestashop-project.org/track.php?num=@ |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then carrier "newCarrier1" should have the following properties:
      | name             | Carrier 1                                     |
      | grade            | 1                                             |
      | trackingUrl      | http://prestashop-project.org/track.php?num=@ |
      | position         | 2                                             |
      | active           | 1                                             |
      | max_width        | 1454                                          |
      | max_height       | 1234                                          |
      | max_depth        | 1111                                          |
      | max_weight       | 3864                                          |
      | group_access     | visitor, guest                                |
      | delay[en-US]     | Shipping delay                                |
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                                         |
      | isFree           | true                                          |
      | shippingMethod   | weight                                        |
      | taxRuleGroup     | US-AL Rate (4%)                               |
      | rangeBehavior    | disabled                                      |

  Scenario: Partially editing carrier with position
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | position | 4 |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then carrier "newCarrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 4                                  |
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
      | shippingMethod   | weight                            |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with active
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | active | false |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then carrier "newCarrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 2                                  |
      | active           | false                              |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with delay
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | delay[en-US] | Shipping delay new         |
      | delay[fr-FR] | Délai de livraison nouveau |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then carrier "newCarrier1" should have the following properties:
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
      | delay[en-US]     | Shipping delay new                 |
      | delay[fr-FR]     | Délai de livraison nouveau         |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with width height depth weight
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | max_width  | 3333 |
      | max_height | 4444 |
      | max_depth  | 5555 |
      | max_weight | 6666 |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then carrier "newCarrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 2                                  |
      | active           | true                               |
      | max_width        | 3333                               |
      | max_height       | 4444                               |
      | max_depth        | 5555                               |
      | max_weight       | 6666                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with group_access
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | group_access | visitor |
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    Then carrier "newCarrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 2                                  |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor                            |
      | delay[en-US]     | Shipping delay                     |
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with handling shipping
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
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | shippingHandling | true                               |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1                          |
      | shippingHandling | false                              |
    Then carrier "newCarrier1" should have the following properties:
      | name             | Carrier 1                          |
      | shippingHandling | true                               |

  Scenario: Partially editing carrier with free shipping
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
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | isFree | true                               |
    Then carrier "carrier1" should have the following properties:
      | name   | Carrier 1                          |
      | isFree | false                              |
    Then carrier "newCarrier1" should have the following properties:
      | name   | Carrier 1                          |
      | isFree | true                               |

  Scenario: Partially editing carrier with shipping method
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
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | shippingMethod | price |
    Then carrier "carrier1" should have the following properties:
      | name           | Carrier 1                      |
      | shippingMethod | weight                         |
    Then carrier "newCarrier1" should have the following properties:
      | name           | Carrier 1                       |
      | shippingMethod | price                           |

  Scenario: Partially editing carrier with invalid shipping method
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
      | shippingHandling | true                               |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | shippingMethod   | invalid                            |
    Then carrier edit should throw an error with error code "INVALID_SHIPPING_METHOD"

  Scenario: Partially editing carrier with tax rule group
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
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | taxRuleGroup | US-AZ Rate (6.6%)              |
    Then carrier "carrier1" should have the following properties:
      | name         | Carrier 1                      |
      | taxRuleGroup | US-AL Rate (4%)                |
    Then carrier "newCarrier1" should have the following properties:
      | name         | Carrier 1                       |
      | taxRuleGroup | US-AZ Rate (6.6%)               |

  Scenario: Partially editing carrier with range behavior
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
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | rangeBehavior  | highest_range |
    Then carrier "carrier1" should have the following properties:
      | name           | Carrier 1                      |
      | rangeBehavior  | disabled                       |
    Then carrier "newCarrier1" should have the following properties:
      | name           | Carrier 1                       |
      | rangeBehavior  | highest_range                   |

  Scenario: Partially editing carrier with invalid range behavior
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
      | shippingHandling | true                               |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | rangeBehavior    | invalid                            |
    Then carrier edit should throw an error with error code "INVALID_RANGE_BEHAVIOR"

  Scenario: Partially editing carrier with additional fees and is free already true
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | shippingHandling  | true                              |
    Then carrier "newCarrier1" should have the following properties:
      | shippingHandling | true                              |
      | isFree           | false                             |

  Scenario: Partially editing carrier with is free and additional fees already true
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
      | shippingHandling | true                               |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | isFree  | true                              |
    Then carrier "newCarrier1" should have the following properties:
      | shippingHandling | false                            |
      | isFree           | true                             |

  Scenario: Partially editing carrier with is free and additional fees at true
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
      | shippingHandling | true                               |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | isFree            | true                              |
      | shippingHandling  | true                              |
    Then carrier edit should throw an error with error code "INVALID_HAS_ADDITIONAL_HANDLING_FEE_WITH_FREE_SHIPPING"

  Scenario: Upload logo for carrier
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
      | logoPathName     | logo.jpg                           |
    Then carrier "carrier1" should have a logo

  Scenario: Upload logo for carrier then edit this carrier to delete logo.
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
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
      | logoPathName     | logo.jpg                           |
    Then carrier "carrier1" should have a logo
    When I edit carrier "carrier1" called "newCarrier1" with specified properties:
      | logoPathName |  |
    Then carrier "carrier1" should have a logo
    Then carrier "newCarrier1" shouldn't have a logo
