@reset-database-before-feature
#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s tax
Feature: Manage tax
  As a employee
  I must be able to correctly add, edit and delete tax

  Scenario: Adding new tax
    When I add new tax "tax-1" with following properties:
      | name         | my custom tax 500 |
      | rate         | 0.5               |
      | is_enabled   | 1                 |
    Then tax "tax-1" name in default language should be "my custom tax 500"
    And tax "tax-1" rate should be 0.500
    And tax "tax-1" should be enabled

  Scenario: Adding new tax with empty name should not be allowed
    When I add new tax "tax-2" with empty name
    Then I should get error message 'Tax contains invalid field values'

  Scenario: Editing tax
    When I edit tax "tax-1" with following properties:
      | name         | my custom tax 300 |
      | rate         | 0.15              |
      | is_enabled   | 0                 |
    Then tax "tax-1" name in default language should be "my custom tax 300"
    And tax "tax-1" rate should be 0.150
    And tax "tax-1" should be disabled

  Scenario: Editing tax when providing only changeable property should be allowed
    When I edit tax "tax-1" with following properties:
      | name        | tax for fun       |
    Then tax "tax-1" name in default language should be "tax for fun"
    And tax "tax-1" rate should be 0.150
    And tax "tax-1" should be disabled

  Scenario: Toggling tax status
    Given tax "tax-1" should be disabled
    When I toggle tax "tax-1" status
    Then tax "tax-1" should be enabled

  Scenario: Disabling taxes status in bulk action
    Given taxes with ids: "1,2,3" exists
    And taxes with ids: "1,2,3" should be enabled
    When I disable taxes with ids: "1,2,3"
    Then taxes with ids: "1,2,3" should be disabled

  Scenario: Enabling taxes status in bulk action
    And taxes with ids: "1,2,3" should be disabled
    When I enable taxes with ids: "1,2,3"
    Then taxes with ids: "1,2,3" should be enabled

  Scenario: Deleting tax
    Given tax with id "2" exists
    When I delete tax with id "2"
    Then tax with id "2" should not be found

  Scenario: Deleting taxes in bulk action
    Given taxes with ids: "3,4,5" exists
    When I delete taxes with ids: "3,4,5" in bulk action
    Then taxes with ids: "3,4,5" should not be found
