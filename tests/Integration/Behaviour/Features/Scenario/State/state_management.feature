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

  Scenario: Partial Editing state
    ## Name
    When I edit state "StateNormandy" with following properties:
      | name    | Britain       |
    Then state "StateNormandy" name should be "Britain"
    Then state "StateNormandy" country should be "United States"
    Then state "StateNormandy" zone should be "Europe"
    And state "StateNormandy" should be enabled
    ## Enabled
    When I edit state "StateNormandy" with following properties:
      | enabled | false          |
    Then state "StateNormandy" name should be "Britain"
    Then state "StateNormandy" country should be "United States"
    Then state "StateNormandy" zone should be "Europe"
    And state "StateNormandy" should be disabled
    ## Country
    When I edit state "StateNormandy" with following properties:
      | country | Italy          |
    Then state "StateNormandy" name should be "Britain"
    Then state "StateNormandy" country should be "Italy"
    Then state "StateNormandy" zone should be "Europe"
    And state "StateNormandy" should be disabled
    ## Zone
    When I edit state "StateNormandy" with following properties:
      | zone | South America  |
    Then state "StateNormandy" name should be "Britain"
    Then state "StateNormandy" country should be "Italy"
    Then state "StateNormandy" zone should be "South America"
    And state "StateNormandy" should be disabled

  Scenario: Editing state
    When I edit state "StateNormandy" with following properties:
      | name    | Tasmania      |
      | enabled | true          |
      | country | Australia     |
      | zone    | Oceania       |
    Then state "StateNormandy" name should be "Tasmania"
    Then state "StateNormandy" country should be "Australia"
    Then state "StateNormandy" zone should be "Oceania"
    And state "StateNormandy" should be enabled

  Scenario: Enable and disable state status
    Given state "StateNormandy" is enabled
    When I toggle status of state "StateNormandy"
    Then state "StateNormandy" should be disabled
    When I toggle status of state "StateNormandy"
    Then state "StateNormandy" should be enabled

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

  Scenario: Bulk assigning states to a new zone
    When I add new state "StateAlsace" with following properties:
      | name    | Alsace        |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| ALS           |
    And I add new state "StateLorraine" with following properties:
      | name    | Lorraine      |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| LOR           |
    And I add new state "StateProvence" with following properties:
      | name    | Provence      |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| PRV           |
    Then state "StateAlsace" zone should be "Europe"
    And state "StateLorraine" zone should be "Europe"
    And state "StateProvence" zone should be "Europe"
    When I bulk update states "StateAlsace, StateLorraine, StateProvence" to zone "North America"
    Then state "StateAlsace" zone should be "North America"
    And state "StateLorraine" zone should be "North America"
    And state "StateProvence" zone should be "North America"

  Scenario: Bulk assigning a single state to a new zone
    When I add new state "StateSavoy" with following properties:
      | name    | Savoy         |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| SAV           |
    Then state "StateSavoy" zone should be "Europe"
    When I bulk update states "StateSavoy" to zone "Asia"
    Then state "StateSavoy" zone should be "Asia"

  Scenario: Bulk assigning states from different zones to a common zone
    When I add new state "StateAuvergne" with following properties:
      | name    | Auvergne      |
      | enabled | true          |
      | country | United States |
      | zone    | Europe        |
      | iso_code| AUV           |
    And I add new state "StateLimousin" with following properties:
      | name    | Limousin      |
      | enabled | true          |
      | country | United States |
      | zone    | Asia          |
      | iso_code| LIM           |
    And I add new state "StatePoitou" with following properties:
      | name    | Poitou        |
      | enabled | true          |
      | country | United States |
      | zone    | North America |
      | iso_code| POI           |
    Then state "StateAuvergne" zone should be "Europe"
    And state "StateLimousin" zone should be "Asia"
    And state "StatePoitou" zone should be "North America"
    When I bulk update states "StateAuvergne, StateLimousin, StatePoitou" to zone "South America"
    Then state "StateAuvergne" zone should be "South America"
    And state "StateLimousin" zone should be "South America"
    And state "StatePoitou" zone should be "South America"
