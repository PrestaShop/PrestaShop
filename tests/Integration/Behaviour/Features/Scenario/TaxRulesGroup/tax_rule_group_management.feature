@restore-all-tables-before-feature
#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s tax_rules_group
Feature: Manage tax
  As an employee
  I must be able to add, edit and delete tax rules group

  Scenario: Adding new tax rules group
    When I add a new tax rules group "sales-tax-rules-group" with the following properties:
      | name       | My Sales Tax Rules Group |
      | is_enabled | true                     |
    Then tax rules group "sales-tax-rules-group" name should be "My Sales Tax Rules Group"
    And tax rules group "sales-tax-rules-group" should be enabled

  Scenario: Editing tax rules group
    When I edit the tax rules group "sales-tax-rules-group" with the following properties:
      | name       | The Sales Tax Rules Group |
      | is_enabled | false                     |
    Then tax rules group "sales-tax-rules-group" name should be "The Sales Tax Rules Group"
    And tax rules group "sales-tax-rules-group" should be disabled

  Scenario: It is possible to modify only the name of a tax rules group, without modifying anything else
    When I edit the tax rules group "sales-tax-rules-group" with the following properties:
      | name | Funny Sales Tax Rules Group |
    Then tax rules group "sales-tax-rules-group" name should be "Funny Sales Tax Rules Group"
    And tax rules group "sales-tax-rules-group" should be disabled

  Scenario: Enabling tax rules group status
    Given tax rules group "sales-tax-rules-group" is disabled
    When I enable tax rules group "sales-tax-rules-group"
    Then tax rules group "sales-tax-rules-group" should be enabled

  Scenario: Deleting tax rules group right after disabling its status
    When I disable tax rules group "sales-tax-rules-group"
    Then tax rules group "sales-tax-rules-group" should be disabled
    When I delete tax rules group "sales-tax-rules-group"
    Then tax rules group "sales-tax-rules-group" should be deleted

  Scenario: Disabling multiple taxes in bulk action
    When I add a new tax rules group "beard-tax-rules-group" with the following properties:
      | name       | Beard Tax Rules Group |
      | is_enabled | true                  |
    And I add a new tax rules group "state-tax-rules-group" with the following properties:
      | name       | State Tax Rules Group |
      | is_enabled | true                  |
    And I add a new tax rules group "pvm-tax-rules-group" with the following properties:
      | name       | PVM Tax Rules Group |
      | is_enabled | false               |
    When I disable tax rules groups: "beard-tax-rules-group, state-tax-rules-group, pvm-tax-rules-group"
    Then tax rules groups: "beard-tax-rules-group, state-tax-rules-group" should be disabled

  Scenario: Deleting multiple taxes right after their status was enabled
    When I enable tax rules groups: "beard-tax-rules-group, state-tax-rules-group, pvm-tax-rules-group"
    Then tax rules groups: "beard-tax-rules-group, state-tax-rules-group, pvm-tax-rules-group" should be enabled
    When I bulk delete tax rules groups: "beard-tax-rules-group, state-tax-rules-group, pvm-tax-rules-group"
    Then tax rules groups: "beard-tax-rules-group, state-tax-rules-group, pvm-tax-rules-group" should be deleted

