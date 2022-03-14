#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s state
@restore-all-tables-before-feature
Feature: Zones management
  As an employee
  I must be able to add, edit and delete states

  Scenario: Adding new state
    When I define an uncreated state "StateNotFound"
    When I add new state "StateNormandy" with following properties:
      | name    | Normandy      |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| NRM           |
    Then state "StateNormandy" name should be "Normandy"
    Then state "StateNormandy" country should be "United States"
    Then state "StateNormandy" zone should be "Europe"
    And state "StateNormandy" should be enabled

  Scenario: Editing state
    When I edit state "StateNormandy" with following properties:
      | name    | Britain       |
      | enabled | false         |
      | country | Italy         |
      | zone    | South America |
    Then state "StateNormandy" name should be "Britain"
    Then state "StateNormandy" country should be "Italy"
    Then state "StateNormandy" zone should be "South America"
    And state "StateNormandy" should be disabled

  Scenario: Enable and disable state status
    Given state "StateNormandy" is disabled
    When I toggle status of state "StateNormandy"
    Then state "StateNormandy" should be enabled
    When I toggle status of state "StateNormandy"
    Then state "StateNormandy" should be disabled

  Scenario: Enabling and disabling multiple states in bulk action
    When I add new state "StateBrittany" with following properties:
      | name    | Brittany      |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| BZH           |
    And I add new state "StateCorsica" with following properties:
      | name    | Corse         |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| CRS           |
    Then states "StateBrittany, StateCorsica" should be enabled
    When I enable multiple states "StateBrittany, StateCorsica" using bulk action
    Then states "StateBrittany, StateCorsica" should be enabled
    When I disable multiple states "StateBrittany, StateCorsica" using bulk action
    Then states "StateBrittany, StateCorsica" should be disabled

  Scenario: Deleting state
    When I delete state "StateNormandy"
    Then state "StateNormandy" should be deleted

  Scenario: Deleting not found state
    When I delete state "StateNotFound"
    Then I should get an error that the state has not been found

  Scenario: Deleting multiple states in bulk action
    When I add new state "StateBrittany" with following properties:
      | name    | Brittany      |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| BZH           |
    And I add new state "StateCorsica" with following properties:
      | name    | Corse         |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| CRS           |
    When I delete states "StateBrittany, StateCorsica" using bulk action
    Then states "StateBrittany, StateCorsica" should be deleted

  Scenario: Deleting multiple states in bulk action with existing in first
    When I add new state "StateBrittany" with following properties:
      | name    | Brittany      |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| BZH           |
    When I delete states "StateBrittany, StateNotFound" using bulk action
    Then I should get an error that the state has not been found
    And state "StateBrittany" should be deleted

  Scenario: Deleting multiple states in bulk action with existing in last
    When I add new state "StateBrittany" with following properties:
      | name    | Brittany      |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| BZH           |
    When I delete states "StateNotFound, StateBrittany" using bulk action
    Then I should get an error that the state has not been found
    And state "StateBrittany" should not be deleted
