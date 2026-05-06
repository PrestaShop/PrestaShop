@restore-all-tables-before-feature
Feature: store management
  In order to manage physical store locations
  As a BO user
  I should be able to add, edit, toggle and delete stores

  # ──────────────────────────────────────────────────────────────
  # Toggle
  # ──────────────────────────────────────────────────────────────

  Scenario: Toggle store status
    When I add new store "storeToggle" with following properties:
      | name      | Toggle Store     |
      | enabled   | true             |
      | address1  | 1 rue de la paix |
      | city      | Paris            |
      | latitude  | 48.8566          |
      | longitude | 2.3522           |
      | country   | France           |
    Then the store "storeToggle" should have status enabled
    When I toggle "storeToggle"
    Then the store "storeToggle" should have status disabled
    When I toggle "storeToggle"
    Then the store "storeToggle" should have status enabled

  # ──────────────────────────────────────────────────────────────
  # Bulk status
  # ──────────────────────────────────────────────────────────────

  Scenario: Bulk enable and disable stores
    When I add new store "StorePau" with following properties:
      | name      | StorePau               |
      | enabled   | true                   |
      | address1  | 1 rue de la republique |
      | city      | Pau                    |
      | latitude  | 43.2951                |
      | longitude | -0.370797              |
      | country   | France                 |
    And I add new store "StoreSerresCastet" with following properties:
      | name      | StoreSerresCastet      |
      | enabled   | true                   |
      | address1  | 1 rue de la foire      |
      | city      | Serres-Castet          |
      | latitude  | 43.2951                |
      | longitude | -0.370797              |
      | country   | France                 |
    And I add new store "StoreBuros" with following properties:
      | name      | StoreBuros             |
      | enabled   | true                   |
      | address1  | 1 chemin de carrere    |
      | city      | Buros                  |
      | latitude  | 43.2951                |
      | longitude | -0.370797              |
      | country   | France                 |
    Then stores "StorePau, StoreSerresCastet, StoreBuros" should be enabled
    When I disable multiple stores "StorePau, StoreSerresCastet" using bulk action
    Then stores "StorePau, StoreSerresCastet" should be disabled
    Then stores "StoreBuros" should be enabled
    When I enable multiple stores "StorePau, StoreSerresCastet" using bulk action
    Then stores "StorePau, StoreSerresCastet, StoreBuros" should be enabled

  # ──────────────────────────────────────────────────────────────
  # Delete
  # ──────────────────────────────────────────────────────────────

  Scenario: Delete a store
    When I add new store "StorePau" with following properties:
      | name      | StorePau               |
      | enabled   | true                   |
      | address1  | 1 rue de la republique |
      | city      | Pau                    |
      | latitude  | 43.2951                |
      | longitude | -0.370797              |
      | country   | France                 |
    And I add new store "StoreSerresCastet" with following properties:
      | name      | StoreSerresCastet      |
      | enabled   | true                   |
      | address1  | 1 rue de la foire      |
      | city      | Serres-Castet          |
      | latitude  | 43.2951                |
      | longitude | -0.370797              |
      | country   | France                 |
    And stores "StorePau, StoreSerresCastet" should exist
    When I delete store "StorePau"
    Then stores "StorePau" should be deleted
    And stores "StoreSerresCastet" should exist

  Scenario: Bulk delete stores
    When I add new store "StorePau" with following properties:
      | name      | StorePau               |
      | enabled   | true                   |
      | address1  | 1 rue de la republique |
      | city      | Pau                    |
      | latitude  | 43.2951                |
      | longitude | -0.370797              |
      | country   | France                 |
    And I add new store "StoreSerresCastet" with following properties:
      | name      | StoreSerresCastet      |
      | enabled   | true                   |
      | address1  | 1 rue de la foire      |
      | city      | Serres-Castet          |
      | latitude  | 43.2951                |
      | longitude | -0.370797              |
      | country   | France                 |
    And I add new store "StoreBuros" with following properties:
      | name      | StoreBuros             |
      | enabled   | true                   |
      | address1  | 1 chemin de carrere    |
      | city      | Buros                  |
      | latitude  | 43.2951                |
      | longitude | -0.370797              |
      | country   | France                 |
    And stores "StorePau, StoreSerresCastet, StoreBuros" should exist
    When I delete stores "StorePau, StoreBuros" using bulk action
    Then stores "StorePau, StoreBuros" should be deleted
    And stores "StoreSerresCastet" should exist

  # ──────────────────────────────────────────────────────────────
  # Add via CQRS command
  # ──────────────────────────────────────────────────────────────

  Scenario: Add a store via command with all fields
    When I add store "storeFrance" using command with the following properties:
      | name      | Ma boutique          |
      | active    | true                 |
      | address1  | 1 rue de la paix     |
      | city      | Paris                |
      | postcode  | 75001                |
      | latitude  | 48.856600            |
      | longitude | 2.352200             |
      | country   | France               |
      | phone     | 0612345678           |
      | fax       | 0112345678           |
      | email     | boutique@example.com |
    Then store "storeFrance" should have the following properties:
      | name      | Ma boutique          |
      | active    | true                 |
      | city      | Paris                |
      | postcode  | 75001                |
      | country   | France               |
      | phone     | 0612345678           |
      | fax       | 0112345678           |
      | email     | boutique@example.com |

  Scenario: Add a store for a country that requires a state
    When I add store "storeUS" using command with the following properties:
      | name      | My US Store           |
      | active    | true                  |
      | address1  | 100 Main Street       |
      | city      | Montgomery            |
      | postcode  | 36101                 |
      | latitude  | 32.361538             |
      | longitude | -86.279118            |
      | country   | United States         |
      | state     | Alabama               |
    Then store "storeUS" should have the following properties:
      | city      | Montgomery    |
      | country   | United States |
      | state     | Alabama       |

  Scenario: Adding a store with a state for a country that has no states raises an error
    When I add store "storeInvalid" using command with the following properties:
      | name      | Invalid Store    |
      | active    | true             |
      | address1  | 1 rue de la paix |
      | city      | Paris            |
      | latitude  | 48.856600        |
      | longitude | 2.352200         |
      | country   | France           |
      | state     | Alabama          |
    Then I should get a store constraint error with code "STATE_COUNTRY_MISMATCH"

  # ──────────────────────────────────────────────────────────────
  # Edit via CQRS command
  # ──────────────────────────────────────────────────────────────

  Scenario: Edit a store via command
    When I add store "storeEditable" using command with the following properties:
      | name      | Original Name    |
      | active    | true             |
      | address1  | 1 rue de la paix |
      | city      | Paris            |
      | postcode  | 75001            |
      | latitude  | 48.856600        |
      | longitude | 2.352200         |
      | country   | France           |
    When I edit store "storeEditable" with the following properties:
      | name     | Updated Name |
      | city     | Lyon         |
      | postcode | 69001        |
      | phone    | 0698765432   |
    Then store "storeEditable" should have the following properties:
      | name     | Updated Name |
      | city     | Lyon         |
      | postcode | 69001        |
      | phone    | 0698765432   |

  Scenario: Edit a store's opening hours
    When I add store "storeHours" using command with the following properties:
      | name      | Hours Store      |
      | active    | true             |
      | address1  | 1 rue de la paix |
      | city      | Paris            |
      | latitude  | 48.856600        |
      | longitude | 2.352200         |
      | country   | France           |
    When I edit store "storeHours" opening hours with the following schedule:
      | day       | open  | close |
      | Monday    | 09:00 | 18:00 |
      | Tuesday   | 09:00 | 18:00 |
      | Wednesday | 09:00 | 18:00 |
      | Thursday  | 09:00 | 18:00 |
      | Friday    | 09:00 | 17:00 |
      | Saturday  |       |       |
      | Sunday    |       |       |
    Then store "storeHours" should have the following opening hours:
      | day       | open  | close |
      | Monday    | 09:00 | 18:00 |
      | Tuesday   | 09:00 | 18:00 |
      | Wednesday | 09:00 | 18:00 |
      | Thursday  | 09:00 | 18:00 |
      | Friday    | 09:00 | 17:00 |
      | Saturday  |       |       |
      | Sunday    |       |       |

  Scenario: Editing a store to a country with states without providing a state raises an error
    When I add store "storeCountryEdit" using command with the following properties:
      | name      | Country Edit Store |
      | active    | true               |
      | address1  | 1 rue de la paix   |
      | city      | Paris              |
      | latitude  | 48.856600          |
      | longitude | 2.352200           |
      | country   | France             |
    When I edit store "storeCountryEdit" with the following properties:
      | country | United States |
    Then I should get a store constraint error with code "INVALID_STATE"
