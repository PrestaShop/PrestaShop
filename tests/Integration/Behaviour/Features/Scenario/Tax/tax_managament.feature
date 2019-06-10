@reset-database-before-feature
Feature: Manage tax
  As a employee
  I must be able to correctly add, edit and delete tax

  Scenario: Adding new tax
    When I add new tax "tax-1" with following properties:
      | name         | test1 |
      | rate         | 0.5   |
    Then tax "tax-1" name in default language should be "test1"
    And tax "tax-1" rate should be 0.500

  Scenario: Editing tax
    When I edit tax "tax-1" with following properties:
      | name         | test1edited |
      | rate         | 0.15        |
    Then tax "tax-1" name in default language should be "test1edited"
    And tax "tax-1" rate should be 0.150

  Scenario: Deleting tax
    Given Tax with id "2" exists
    When I delete tax with id "2"
    Then Tax with id "2" should not exist
