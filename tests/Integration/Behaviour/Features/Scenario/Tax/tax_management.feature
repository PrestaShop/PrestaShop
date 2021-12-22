@restore-all-tables-before-feature
#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s tax
Feature: Manage tax
  As an employee
  I must be able to add, edit and delete taxes

  Scenario: Adding new tax
    When I add new tax "sales-tax" with following properties:
      | name         | my sales tax 500 |
      | rate         | 0.5              |
      | is_enabled   | true             |
    Then tax "sales-tax" name in default language should be "my sales tax 500"
    And tax "sales-tax" rate should be 0.500
    And tax "sales-tax" should be enabled

  Scenario: Editing tax
    When I edit tax "sales-tax" with following properties:
      | name         | my sales tax 300 |
      | rate         | 0.15             |
      | is_enabled   | false            |
    Then tax "sales-tax" name in default language should be "my sales tax 300"
    And tax "sales-tax" rate should be 0.150
    And tax "sales-tax" should be disabled

  Scenario: It is possible to modify only the name of a tax, without modifying anything else
    When I edit tax "sales-tax" with following properties:
      | name        | tax for fun       |
    Then tax "sales-tax" name in default language should be "tax for fun"
    And tax "sales-tax" rate should be 0.150
    And tax "sales-tax" should be disabled

  Scenario: Enabling tax status
    Given tax "sales-tax" is disabled
    When I enable tax "sales-tax"
    Then tax "sales-tax" should be enabled

  Scenario: Deleting tax right after disabling its status
    When I disable tax "sales-tax"
    Then tax "sales-tax" should be disabled
    When I delete tax "sales-tax"
    Then tax "sales-tax" should be deleted

  Scenario: Disabling multiple taxes in bulk action
    When I add new tax "beard-tax" with following properties:
      | name         | Beard tax         |
      | rate         | 0.1               |
      | is_enabled   | true              |
    And I add new tax "state-tax" with following properties:
      | name         | State tax         |
      | rate         | 15.23             |
      | is_enabled   | true              |
    And I add new tax "pvm-tax" with following properties:
      | name         | PVM               |
      | rate         | 99.9              |
      | is_enabled   | false             |
    When I disable taxes: "beard-tax, state-tax, pvm-tax"
    Then taxes: "beard-tax, state-tax" should be disabled

  Scenario: Deleting multiple taxes right after their status was enabled
    When I enable taxes: "beard-tax, state-tax, pvm-tax"
    Then taxes: "beard-tax, state-tax, pvm-tax" should be enabled
    When I delete taxes: "beard-tax, state-tax, pvm-tax" in bulk action
    Then taxes: "beard-tax, state-tax, pvm-tax" should be deleted

