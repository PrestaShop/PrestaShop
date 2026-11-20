# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shop --tags shop-group
@restore-all-tables-before-feature
@clear-cache-before-feature
@shop-group
Feature: Shop group management (BO)
  As a BO user
  I want to add, edit and delete shop groups

  Background:
    Given shop group "default_group" with name "Default" exists

  Scenario: Add a shop group with every property
    When I add a shop group "group_sharing" with the following properties:
      | name           | Group with sharing |
      | color          | #ff0000            |
      | share_customer | true               |
      | share_stock    | true               |
      | share_order    | true               |
      | active         | true               |
    Then shop group "group_sharing" should have the following properties:
      | name           | Group with sharing |
      | color          | #ff0000            |
      | share_customer | true               |
      | share_stock    | true               |
      | share_order    | true               |
      | active         | true               |

  Scenario: Add a shop group without sharing options
    When I add a shop group "group_simple" with the following properties:
      | name | Simple group |
    Then shop group "group_simple" should have the following properties:
      | name           | Simple group |
      | color          |              |
      | share_customer | false        |
      | share_stock    | false        |
      | share_order    | false        |
      | active         | true         |

  Scenario: Shop group name must be valid
    When I add a shop group "group_invalid_name" with the following properties:
      | name | invalid<name> |
    Then I should get error that shop group name is invalid
    When I add a shop group "group_empty_name" with the following properties:
      | name |  |
    Then I should get error that shop group name is invalid

  Scenario: Shop group color must be valid
    When I add a shop group "group_invalid_color" with the following properties:
      | name  | Invalid color group |
      | color | not a color         |
    Then I should get error that shop group color is invalid

  Scenario: Orders cannot be shared without sharing customers and stock
    When I add a shop group "group_share_order_only" with the following properties:
      | name           | Shared orders only |
      | share_customer | true               |
      | share_order    | true               |
    Then I should get error that shop group cannot share orders without sharing customers and stock

  Scenario: Edit name and color of a shop group
    When I edit shop group "group_simple" with the following properties:
      | name  | Renamed group |
      | color | blue          |
    Then shop group "group_simple" should have the following properties:
      | name           | Renamed group |
      | color          | blue          |
      | share_customer | false         |
      | active         | true          |

  Scenario: Sharing options can be changed while the store has a single shop
    Given shop group "group_simple" should have the following properties:
      | sharing_options_locked | false |
    When I edit shop group "group_simple" with the following properties:
      | share_customer | true |
      | share_stock    | true |
      | share_order    | true |
    Then shop group "group_simple" should have the following properties:
      | share_customer | true |
      | share_stock    | true |
      | share_order    | true |

  Scenario: Unsharing customers of a group sharing its orders is refused
    When I edit shop group "group_simple" with the following properties:
      | share_customer | false |
    Then I should get error that shop group cannot share orders without sharing customers and stock
    And shop group "group_simple" should have the following properties:
      | share_customer | true |
      | share_order    | true |

  Scenario: An empty shop group can be disabled and enabled
    When I edit shop group "group_simple" with the following properties:
      | active | false |
    Then shop group "group_simple" should have the following properties:
      | active | false |
    When I edit shop group "group_simple" with the following properties:
      | active | true |
    Then shop group "group_simple" should have the following properties:
      | active | true |

  Scenario: A shop group containing shops cannot be disabled
    When I edit shop group "default_group" with the following properties:
      | active | false |
    Then I should get error that shop group with shops cannot be disabled
    And shop group "default_group" should have the following properties:
      | active | true |

  Scenario: A shop group containing shops cannot be deleted
    When I delete shop group "default_group"
    Then I should get error that shop group with shops cannot be deleted
    And shop group "default_group" should have the following properties:
      | name | Default |

  Scenario: Delete an empty shop group
    When I delete shop group "group_simple"
    Then shop group "group_simple" should not exist

  Scenario: Deleting a shop group that does not exist
    Given shop group "unknown_group" does not exist
    When I delete shop group "unknown_group"
    Then I should get error that shop group was not found

  Scenario: Sharing options are locked once the store has more than one shop
    Given I add a shop "second_shop" with name "Second shop" and color "red" for the group "group_sharing"
    And shop group "group_sharing" should have the following properties:
      | sharing_options_locked | true |
    When I edit shop group "group_sharing" with the following properties:
      | share_order | false |
    Then I should get error that shop group sharing options are locked
    And shop group "group_sharing" should have the following properties:
      | share_order | true |

  Scenario: Other properties can still be edited when the sharing options are sent unchanged
    When I edit shop group "group_sharing" with the following properties:
      | name           | Renamed sharing group |
      | share_customer | true                  |
      | share_stock    | true                  |
      | share_order    | true                  |
    Then shop group "group_sharing" should have the following properties:
      | name        | Renamed sharing group |
      | share_order | true                  |

  Scenario: The shop tree only lists the shops the employee has access to
    Given shop "shop1" with name "test_shop" exists
    And I add a shop group "empty_group" with name "Empty group"
    Then the shop tree should list the following groups and shops:
      | Default               | test_shop   |
      | Renamed sharing group | Second shop |
      | Empty group           |             |
    When I am an employee with access to shops "shop1" only
    Then the shop tree should list the following groups and shops:
      | Default | test_shop |
    And the shop tree should not list the groups "Renamed sharing group, Empty group"
