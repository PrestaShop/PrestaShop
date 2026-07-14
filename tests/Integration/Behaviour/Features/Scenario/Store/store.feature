@restore-all-tables-before-feature
@store
Feature: store management
  In order to manage physical store locations
  As a BO user
  I should be able to add, edit, toggle and delete stores

  Background:
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists

  # ──────────────────────────────────────────────────────────────
  # Toggle
  # ──────────────────────────────────────────────────────────────

  Scenario: Toggle store status
    When I add store "storeToggle" using command with the following properties:
      | name[en-US]     | Toggle Store     |
      | active          | true             |
      | address1[en-US] | 1 rue de la paix |
      | city            | Paris            |
      | latitude        | 48.8566          |
      | longitude       | 2.3522           |
      | country         | France           |
    Then the store "storeToggle" should have status enabled
    When I toggle "storeToggle"
    Then the store "storeToggle" should have status disabled
    When I toggle "storeToggle"
    Then the store "storeToggle" should have status enabled

  # ──────────────────────────────────────────────────────────────
  # Bulk status
  # ──────────────────────────────────────────────────────────────

  Scenario: Bulk enable and disable stores
    When I add store "StorePau" using command with the following properties:
      | name[en-US]     | StorePau               |
      | active          | true                   |
      | address1[en-US] | 1 rue de la republique |
      | city            | Pau                    |
      | latitude        | 43.2951                |
      | longitude       | -0.370797              |
      | country         | France                 |
    And I add store "StoreSerresCastet" using command with the following properties:
      | name[en-US]     | StoreSerresCastet      |
      | active          | true                   |
      | address1[en-US] | 1 rue de la foire      |
      | city            | Serres-Castet          |
      | latitude        | 43.2951                |
      | longitude       | -0.370797              |
      | country         | France                 |
    And I add store "StoreBuros" using command with the following properties:
      | name[en-US]     | StoreBuros             |
      | active          | true                   |
      | address1[en-US] | 1 chemin de carrere    |
      | city            | Buros                  |
      | latitude        | 43.2951                |
      | longitude       | -0.370797              |
      | country         | France                 |
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
    When I add store "StorePau" using command with the following properties:
      | name[en-US]     | StorePau               |
      | active          | true                   |
      | address1[en-US] | 1 rue de la republique |
      | city            | Pau                    |
      | latitude        | 43.2951                |
      | longitude       | -0.370797              |
      | country         | France                 |
    And I add store "StoreSerresCastet" using command with the following properties:
      | name[en-US]     | StoreSerresCastet      |
      | active          | true                   |
      | address1[en-US] | 1 rue de la foire      |
      | city            | Serres-Castet          |
      | latitude        | 43.2951                |
      | longitude       | -0.370797              |
      | country         | France                 |
    And stores "StorePau, StoreSerresCastet" should exist
    When I delete store "StorePau"
    Then stores "StorePau" should be deleted
    And stores "StoreSerresCastet" should exist

  Scenario: Bulk delete stores
    When I add store "StorePau" using command with the following properties:
      | name[en-US]     | StorePau               |
      | active          | true                   |
      | address1[en-US] | 1 rue de la republique |
      | city            | Pau                    |
      | latitude        | 43.2951                |
      | longitude       | -0.370797              |
      | country         | France                 |
    And I add store "StoreSerresCastet" using command with the following properties:
      | name[en-US]     | StoreSerresCastet      |
      | active          | true                   |
      | address1[en-US] | 1 rue de la foire      |
      | city            | Serres-Castet          |
      | latitude        | 43.2951                |
      | longitude       | -0.370797              |
      | country         | France                 |
    And I add store "StoreBuros" using command with the following properties:
      | name[en-US]     | StoreBuros             |
      | active          | true                   |
      | address1[en-US] | 1 chemin de carrere    |
      | city            | Buros                  |
      | latitude        | 43.2951                |
      | longitude       | -0.370797              |
      | country         | France                 |
    And stores "StorePau, StoreSerresCastet, StoreBuros" should exist
    When I delete stores "StorePau, StoreBuros" using bulk action
    Then stores "StorePau, StoreBuros" should be deleted
    And stores "StoreSerresCastet" should exist

  # ──────────────────────────────────────────────────────────────
  # Add via CQRS command
  # ──────────────────────────────────────────────────────────────

  Scenario: Add a store via command with all fields
    When I add store "storeFrance" using command with the following properties:
      | name[en-US]     | My shop              |
      | name[fr-FR]     | Ma boutique          |
      | active          | true                 |
      | address1[en-US] | 1 peace street       |
      | address1[fr-FR] | 1 rue de la paix     |
      | address2[en-US] | building B           |
      | address2[fr-FR] | bâtiment B           |
      | city            | Paris                |
      | postcode        | 75001                |
      | latitude        | 48.856600            |
      | longitude       | 2.352200             |
      | country         | France               |
      | phone           | 0612345678           |
      | fax             | 0112345678           |
      | email           | boutique@example.com |
    Then store "storeFrance" should have the following properties:
      | name[en-US] | My shop              |
      | name[fr-FR] | Ma boutique          |
      | active      | true                 |
      | city        | Paris                |
      | postcode    | 75001                |
      | country     | France               |
      | phone       | 0612345678           |
      | fax         | 0112345678           |
      | email       | boutique@example.com |

  Scenario: Add a store for a country that requires a state
    When I add store "storeUS" using command with the following properties:
      | name[en-US]     | My US Store     |
      | active          | true            |
      | address1[en-US] | 100 Main Street |
      | city            | Montgomery      |
      | postcode        | 36101           |
      | latitude        | 32.361538       |
      | longitude       | -86.279118      |
      | country         | United States   |
      | state           | Alabama         |
    Then store "storeUS" should have the following properties:
      | city      | Montgomery    |
      | country   | United States |
      | state     | Alabama       |

  Scenario: Adding a store with a state for a country that has no states raises an error
    When I add store "storeInvalid" using command with the following properties:
      | name[en-US]     | Invalid Store    |
      | active          | true             |
      | address1[en-US] | 1 rue de la paix |
      | city            | Paris            |
      | latitude        | 48.856600        |
      | longitude       | 2.352200         |
      | country         | France           |
      | state           | Alabama          |
    Then I should get a store constraint error with code "STATE_COUNTRY_MISMATCH"

  # ──────────────────────────────────────────────────────────────
  # Edit via CQRS command
  # ──────────────────────────────────────────────────────────────

  Scenario: Edit a store via command
    When I add store "storeEditable" using command with the following properties:
      | name[en-US]     | Original Name    |
      | name[fr-FR]     | Nom d'origine    |
      | active          | true             |
      | address1[en-US] | 1 peace street   |
      | address1[fr-FR] | 1 rue de la paix |
      | city            | Paris            |
      | postcode        | 75001            |
      | latitude        | 48.856600        |
      | longitude       | 2.352200         |
      | country         | France           |
    When I edit store "storeEditable" with the following properties:
      | name[en-US] | Updated Name |
      | name[fr-FR] | Nom modifié  |
      | city        | Lyon         |
      | postcode    | 69001        |
      | phone       | 0698765432   |
    Then store "storeEditable" should have the following properties:
      | name[en-US] | Updated Name |
      | name[fr-FR] | Nom modifié  |
      | city        | Lyon         |
      | postcode    | 69001        |
      | phone       | 0698765432   |

  Scenario: Edit a store's opening hours
    When I add store "storeHours" using command with the following properties:
      | name[en-US]     | Hours Store      |
      | active          | true             |
      | address1[en-US] | 1 rue de la paix |
      | city            | Paris            |
      | latitude        | 48.856600        |
      | longitude       | 2.352200         |
      | country         | France           |
    When I edit store "storeHours" opening hours with the following schedule:
      | day              | open  | close |
      | Monday[en-US]    | 09:00 | 18:00 |
      | Monday[fr-FR]    | 08:30 | 19:00 |
      | Tuesday[en-US]   | 09:00 | 18:00 |
      | Tuesday[fr-FR]   | 08:30 | 19:00 |
      | Wednesday[en-US] | 09:00 | 18:00 |
      | Wednesday[fr-FR] | 08:30 | 19:00 |
      | Thursday[en-US]  | 09:00 | 18:00 |
      | Thursday[fr-FR]  | 08:30 | 19:00 |
      | Friday[en-US]    | 09:00 | 17:00 |
      | Friday[fr-FR]    | 08:30 | 18:00 |
      | Saturday[en-US]  |       |       |
      | Saturday[fr-FR]  |       |       |
      | Sunday[en-US]    | Closed      |       |
      | Sunday[fr-FR]    | Fermé      |       |
    Then store "storeHours" should have the following opening hours:
      | day              | open  | close |
      | Monday[en-US]    | 09:00 | 18:00 |
      | Monday[fr-FR]    | 08:30 | 19:00 |
      | Tuesday[en-US]   | 09:00 | 18:00 |
      | Tuesday[fr-FR]   | 08:30 | 19:00 |
      | Wednesday[en-US] | 09:00 | 18:00 |
      | Wednesday[fr-FR] | 08:30 | 19:00 |
      | Thursday[en-US]  | 09:00 | 18:00 |
      | Thursday[fr-FR]  | 08:30 | 19:00 |
      | Friday[en-US]    | 09:00 | 17:00 |
      | Friday[fr-FR]    | 08:30 | 18:00 |
      | Saturday[en-US]  |       |       |
      | Saturday[fr-FR]  |       |       |
      | Sunday[en-US]    | Closed      |       |
      | Sunday[fr-FR]    | Fermé      |       |

  Scenario: Editing a store to a country with states without providing a state raises an error
    When I add store "storeCountryEdit" using command with the following properties:
      | name[en-US]     | Country Edit Store |
      | active          | true               |
      | address1[en-US] | 1 rue de la paix   |
      | city            | Paris              |
      | latitude        | 48.856600          |
      | longitude       | 2.352200           |
      | country         | France             |
    When I edit store "storeCountryEdit" with the following properties:
      | country | United States |
    Then I should get a store constraint error with code "INVALID_STATE"
