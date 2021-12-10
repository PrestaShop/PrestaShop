#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s manufacturer
@restore-all-tables-before-feature
Feature: Zones management
  As an employee
  I must be able to add, edit and delete zones

  Scenario: Adding new zone
    When I add new zone "sun" with following properties:
      | name    | big-star |
      | enabled | true     |
    Then zone "sun" name should be "big-star"
    And zone "sun" should be enabled

  Scenario: Editing zone
    When I edit zone "sun" with following properties:
      | name    | earth |
      | enabled | false |
    Then zone "sun" name should be "earth"
    And zone "sun" should be disabled

  Scenario: Enable and disable zone status
    Given zone "sun" is disabled
    When I toggle status of zone "sun"
    Then zone "sun" should be enabled
    When I toggle status of zone "sun"
    Then zone "sun" should be disabled

  Scenario: Enabling and disabling multiple zones in bulk action
    When I add new zone "mars" with following properties:
      | name    | somewhere |
      | enabled | false     |
    And I add new zone "jupiter" with following properties:
      | name    | in-space  |
      | enabled | false     |
    Then zones: "mars, jupiter" should be disabled
    When I enable multiple zones: "mars, jupiter" using bulk action
    Then zones: "mars, jupiter" should be enabled
    When I disable multiple zones: "mars, jupiter" using bulk action
    Then zones: "mars, jupiter" should be disabled

  Scenario: Deleting zone
    When I delete zone "sun"
    Then zone "sun" should be deleted

  Scenario: Deleting multiple zones in bulk action
    When I delete zones: "mars, jupiter" using bulk action
    Then zones: "mars, jupiter" should be deleted
