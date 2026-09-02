@restore-all-tables-before-feature
#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s tax_rules_group
Feature: Manage tax rules within a tax rules group
  As an employee
  I must be able to add, edit and delete tax rules within a tax rules group

  Background:
    Given I add a new tax rules group "test-group" with the following properties:
      | name       | Test Tax Rules Group |
      | is_enabled | true                 |

  Scenario: Adding a tax rule to a group for a specific country
    When I add a tax rule "us-rule" to group "test-group" with the following properties:
      | country     | 8   |
      | tax         | 1   |
      | behavior    | 0   |
      | zipcode     |     |
      | description | US tax rule |
    Then tax rule "us-rule" should exist in group "test-group"
    And tax rule "us-rule" country should be 8
    And tax rule "us-rule" tax should be 1
    And tax rule "us-rule" behavior should be 0
    And tax rule "us-rule" description should be "US tax rule"

  Scenario: Adding a tax rule with a zipcode range
    When I add a tax rule "paris-rule" to group "test-group" with the following properties:
      | country     | 6     |
      | tax         | 1     |
      | behavior    | 0     |
      | zipcode     | 75000-75015 |
      | description | Paris zip range |
    Then tax rule "paris-rule" should exist in group "test-group"
    And tax rule "paris-rule" zipcode from should be "75000"
    And tax rule "paris-rule" zipcode to should be "75015"

  Scenario: Adding a tax rule with empty zipcode stores 0/0
    When I add a tax rule "no-zip-rule" to group "test-group" with the following properties:
      | country     | 6   |
      | tax         | 1   |
      | behavior    | 0   |
      | zipcode     |     |
      | description |     |
    Then tax rule "no-zip-rule" should exist in group "test-group"
    And tax rule "no-zip-rule" zipcode from should be "0"
    And tax rule "no-zip-rule" zipcode to should be "0"

  Scenario: Adding a tax rule with combine behavior
    When I add a tax rule "combine-rule" to group "test-group" with the following properties:
      | country     | 6   |
      | tax         | 1   |
      | behavior    | 1   |
      | zipcode     |     |
      | description |     |
    Then tax rule "combine-rule" should exist in group "test-group"
    And tax rule "combine-rule" behavior should be 1

  Scenario: Editing a tax rule
    When I add a tax rule "editable-rule" to group "test-group" with the following properties:
      | country     | 6   |
      | tax         | 1   |
      | behavior    | 0   |
      | zipcode     |     |
      | description | Original |
    When I edit tax rule "editable-rule" with the following properties:
      | description | Updated description |
      | behavior    | 2                   |
    Then tax rule "editable-rule" description should be "Updated description"
    And tax rule "editable-rule" behavior should be 2

  Scenario: Deleting a tax rule
    When I add a tax rule "deletable-rule" to group "test-group" with the following properties:
      | country     | 6   |
      | tax         | 1   |
      | behavior    | 0   |
      | zipcode     |     |
      | description |     |
    When I delete tax rule "deletable-rule"
    Then tax rule "deletable-rule" should not exist

  Scenario: Bulk deleting tax rules
    When I add a tax rule "bulk-rule-1" to group "test-group" with the following properties:
      | country     | 6   |
      | tax         | 1   |
      | behavior    | 0   |
      | zipcode     |     |
      | description | Rule 1 |
    And I add a tax rule "bulk-rule-2" to group "test-group" with the following properties:
      | country     | 8   |
      | tax         | 1   |
      | behavior    | 0   |
      | zipcode     |     |
      | description | Rule 2 |
    When I bulk delete tax rules "bulk-rule-1, bulk-rule-2" from group "test-group"
    Then tax rule "bulk-rule-1" should not exist
    And tax rule "bulk-rule-2" should not exist
