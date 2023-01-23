#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s authorization_server
@restore-all-tables-before-feature
Feature: Authorization server management
  As an employee
  I must be able to add, edit and delete manufacturers

  Scenario: Adding new authorized application
    When I add new authorized application "Best ERP" with following properties:
      | name        | My best ERP              |
      | description | Manage my store entities |
    Then authorized application "Best ERP" should have the following details:
      | name        | My best ERP              |
      | description | Manage my store entities |
    When I add new already exist authorized application "Existing ERP" with following properties:
      | name        | My best ERP              |
      | description | Manage my store entities |
    Then I should get error that authorized application with this name already exists

  Scenario: Editing authorized application
    When I add new authorized application "Best ERP updatable" with following properties:
      | name        | My best ERP updatable    |
      | description | Manage my store entities |
    And I update authorized application "Best ERP updatable" with the following details:
      | name        | My best ERP updated              |
      | description | Manage my store entities updated |
    Then authorized application "Best ERP updatable" should have the following details:
      | name        | My best ERP updated              |
      | description | Manage my store entities updated |
    When I update non-existent authorized application
    Then I should get error that authorized application does not exist
    When I update already exist authorized application "Best ERP updatable" with following properties:
      | name        | My best ERP              |
      | description | Manage my store entities |
    Then I should get error that authorized application with this name already exists
