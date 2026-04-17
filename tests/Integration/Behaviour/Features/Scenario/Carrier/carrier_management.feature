# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier --tags carrier-management
@restore-all-tables-before-feature
@clear-cache-before-feature
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
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    Given I add new zone "zone2" with following properties:
      | name    | zone2 |
      | enabled | true  |
    And language "en" with locale "en-US" exists
    And language "fr" with locale "fr-FR" exists
    And language with iso code "en" is the default one

  Scenario: Adding new Carrier
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
      | rangeBehavior    | disabled                           |
    Then carrier "carrier1" should have the following properties:
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
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
      | zones            | zone1                              |
      | ordersCount      | 0                                  |
    Then carrier "carrier1" shouldn't have a logo

  Scenario: Partially editing carrier with name and with an order linked
    Given email sending is disabled
    Given the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | zones            | zone2                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | taxRuleGroup     | US-AL Rate (4%)                    |
      | rangeBehavior    | disabled                           |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "carrier1"
    Then order "bo_order1" has Tracking number "TEST1234"
    When I edit carrier "carrier1" with specified properties I get a new carrier referenced as "newCarrier1":
      | name | Carrier 1 new |
    Then carrier "carrier1" should have the following properties:
      | name        | Carrier 1 |
      | ordersCount | 1         |
    Then carrier "newCarrier1" should have the following properties:
      | name        | Carrier 1 new |
      | ordersCount | 0             |

  Scenario: Partially editing carrier with name and without an order linked
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties I get a similar carrier called "newCarrier1":
      | name | Carrier 1 new |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1 new                      |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 6                                  |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    Then carrier "newCarrier1" should have the following properties:
      | name             | Carrier 1 new                      |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 6                                  |
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

  Scenario: Partially editing carrier with grade
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | grade | 2 |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 2                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 7                                  |
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

  Scenario: Partially editing carrier with tracking url
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | trackingUrl | http://prestashop-project.org/track.php?num=@ |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1                                     |
      | grade            | 1                                             |
      | trackingUrl      | http://prestashop-project.org/track.php?num=@ |
      | position         | 8                                             |
      | active           | 1                                             |
      | max_width        | 1454                                          |
      | max_height       | 1234                                          |
      | max_depth        | 1111                                          |
      | max_weight       | 3864                                          |
      | group_access     | visitor, guest                                |
      | delay[en-US]     | Shipping delay                                |
      | delay[fr-FR]     | Délai de livraison                            |
      | shippingHandling | false                                         |
      | isFree           | true                                          |
      | shippingMethod   | weight                                        |
      | rangeBehavior    | disabled                                      |

  Scenario: Partially editing carrier with position (and forced position on creation)
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
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
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
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | position | 4 |
    Then carrier "carrier1" should have the following properties:
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
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with active
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | active | false |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 9                                  |
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
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with delay
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | delay[en-US] | Shipping delay new         |
      | delay[fr-FR] | Délai de livraison nouveau |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 10                                 |
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
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with width height depth weight
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | max_width  | 3333 |
      | max_height | 4444 |
      | max_depth  | 5555 |
      | max_weight | 6666 |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 11                                 |
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
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with group_access
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | group_access | visitor |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | position         | 12                                 |
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
      | rangeBehavior    | disabled                           |

  Scenario: Partially editing carrier with handling shipping
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | shippingHandling | true |
    Then carrier "carrier1" should have the following properties:
      | name             | Carrier 1 |
      | shippingHandling | true      |

  Scenario: Partially editing carrier with free shipping
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | isFree | true |
    Then carrier "carrier1" should have the following properties:
      | name   | Carrier 1 |
      | isFree | true      |

  Scenario: Partially editing carrier with shipping method
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | shippingMethod | price |
    Then carrier "carrier1" should have the following properties:
      | name           | Carrier 1 |
      | shippingMethod | price     |

  Scenario: Partially editing carrier with invalid shipping method
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | true                               |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | shippingMethod | invalid |
    Then carrier should throw an error with error code "INVALID_SHIPPING_METHOD"

  # @debug
  # Scenario: Partially editing carrier with tax rule group
  #   When I create carrier "carrier1" with specified properties:
  #     | name             | Carrier 1                          |
  #     | grade            | 1                                  |
  #     | trackingUrl      | http://example.com/track.php?num=@ |
  #     | position         | 2                                  |
  #     | active           | true                               |
  #     | max_width        | 1454                               |
  #     | max_height       | 1234                               |
  #     | max_depth        | 1111                               |
  #     | max_weight       | 3864                               |
  #     | group_access     | visitor, guest                     |
  #     | delay[en-US]     | Shipping delay                     |
  #     | delay[fr-FR]     | Délai de livraison                 |
  #     | shippingHandling | false                              |
  #     | isFree           | false                              |
  #     | shippingMethod   | weight                             |
  #     | taxRuleGroup     | US-AL Rate (4%)                    |
  #     | rangeBehavior    | disabled                           |
  #   When I edit carrier "carrier1" with specified properties:
  #     | taxRuleGroup | US-AZ Rate (6.6%)              |
  #   Then carrier "carrier1" should have the following properties:
  #     | name         | Carrier 1                       |
  #     | taxRuleGroup | US-AZ Rate (6.6%)               |

  Scenario: Partially editing carrier with range behavior
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | rangeBehavior | highest_range |
    Then carrier "carrier1" should have the following properties:
      | name          | Carrier 1     |
      | rangeBehavior | highest_range |

  Scenario: Partially editing carrier with invalid range behavior
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | true                               |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | rangeBehavior | invalid |
    Then carrier should throw an error with error code "INVALID_RANGE_BEHAVIOR"

  Scenario: Partially editing carrier with additional fees and is free already true
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | shippingHandling | true |
    Then carrier "carrier1" should have the following properties:
      | shippingHandling | true  |
      | isFree           | false |

  Scenario: Partially editing carrier with is free and additional fees already true
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | true                               |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | isFree | true |
    Then carrier "carrier1" should have the following properties:
      | shippingHandling | false |
      | isFree           | true  |

  Scenario: Partially editing carrier with is free and additional fees at true
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | true                               |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | isFree           | true |
      | shippingHandling | true |
    Then carrier should throw an error with error code "INVALID_HAS_ADDITIONAL_HANDLING_FEE_WITH_FREE_SHIPPING"

  Scenario: Upload logo for carrier
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
      | logoPathName     | logo.jpg                           |
    Then carrier "carrier1" should have a logo

  Scenario: Upload logo for carrier then edit this carrier to delete logo.
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone2                              |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
      | logoPathName     | logo.jpg                           |
    Then carrier "carrier1" should have a logo
    When I edit carrier "carrier1" with specified properties:
      | logoPathName |  |
    Then carrier "carrier1" shouldn't have a logo

  Scenario: Partially editing carrier with zones
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | zones            | zone1, zone2                       |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | zones     | zone1     |
    Then carrier "carrier1" should have the following properties:
      | name      | Carrier 1 |
      | zones     | zone1     |

  Scenario: Add a new carrier without any zone
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | zones            |                                    |
      | rangeBehavior    | disabled                           |
    Then carrier should throw an error with error code "INVALID_ZONE_MISSING"

  Scenario: Add a new carrier with a wrong grade
    When I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | grade            | 10                                 |
      | trackingUrl      | http://example.com/track.php?num=@ |
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
      | zones            | zone1                              |
      | rangeBehavior    | disabled                           |
    Then carrier should throw an error with error code "INVALID_GRADE"

  Scenario: Edit a new carrier and delete all zone
    When I create carrier "carrier1" with specified properties:
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
      | delay[fr-FR]     | Délai de livraison                 |
      | zones            | zone1, zone2                       |
      | shippingHandling | false                              |
      | isFree           | false                              |
      | shippingMethod   | weight                             |
      | rangeBehavior    | disabled                           |
    When I edit carrier "carrier1" with specified properties:
      | zones     |     |
    Then carrier should throw an error with error code "INVALID_ZONE_MISSING"
