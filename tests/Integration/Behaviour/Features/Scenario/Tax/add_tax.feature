@reset-database-before-feature
Feature: Add tax
  As a employee
  I must be able to correctly add tax

  Scenario: Adding tax
    When I add new tax "test1" with following properties:
      | name         | test1 |
      | rate         | 0.5   |
    Then tax "test1" name in default language should be "test1"
    And tax "test1" rate should be 0.500
