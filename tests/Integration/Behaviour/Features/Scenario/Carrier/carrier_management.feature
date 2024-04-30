# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier --tags carrier-management
@restore-all-tables-before-feature
@carrier-management
Feature: Carrier management
  PrestaShop allows BO users to manage carrier for shipping
  As a BO user
  I must be able to create, edit and delete carriers

  Scenario: Adding new Carrier
    When I create carrier "carrier1" with specified properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    Then carrier "carrier1" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |

  Scenario: Partially editing carrier with name
    When I create carrier "carrier1" with specified properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    When I edit carrier "carrier1" with specified properties:
      | name | Carrier 1 new |
    Then carrier "carrier1" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    Then carrier "carrier1-edited" should have the following properties:
      | name         | Carrier 1 new                      |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |

  Scenario: Partially editing carrier with grade
    When I create carrier "carrier1" with specified properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    When I edit carrier "carrier1" with specified properties:
      | grade | 2 |
    Then carrier "carrier1" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    Then carrier "carrier1-edited" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 2                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |

  Scenario: Partially editing carrier with tracking url
    When I create carrier "carrier1" with specified properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    When I edit carrier "carrier1" with specified properties:
      | trackingUrl | http://prestashop-project.org/track.php?num=@ |
    Then carrier "carrier1" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    Then carrier "carrier1-edited" should have the following properties:
      | name         | Carrier 1                                     |
      | grade        | 1                                             |
      | trackingUrl  | http://prestashop-project.org/track.php?num=@ |
      | position     | 2                                             |
      | active       | 1                                             |
      | delay[en-US] | Shipping delay                                |

  Scenario: Partially editing carrier with position
    When I create carrier "carrier1" with specified properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    When I edit carrier "carrier1" with specified properties:
      | position | 4 |
    Then carrier "carrier1" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    Then carrier "carrier1-edited" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 4                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |

  Scenario: Partially editing carrier with active
    When I create carrier "carrier1" with specified properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    When I edit carrier "carrier1" with specified properties:
      | active | false |
    Then carrier "carrier1" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    Then carrier "carrier1-edited" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | false                              |
      | delay[en-US] | Shipping delay                     |

  Scenario: Partially editing carrier with delay
    When I create carrier "carrier1" with specified properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    When I edit carrier "carrier1" with specified properties:
      | delay[en-US] | Shipping delay new |
    Then carrier "carrier1" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay                     |
    Then carrier "carrier1-edited" should have the following properties:
      | name         | Carrier 1                          |
      | grade        | 1                                  |
      | trackingUrl  | http://example.com/track.php?num=@ |
      | position     | 2                                  |
      | active       | true                               |
      | delay[en-US] | Shipping delay new                 |
