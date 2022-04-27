@restore-all-tables-before-feature
Feature: store
  In order to be able to manage shops in Shop Parameters > Contact > Shops
  As a BO user
  I should be able to toggle the status of a contact
  
  Scenario: Toggle existing store
    Given the store "store1" should have status enabled
      When I toggle "store1"
      Then the store "store1" should have status disabled
      When I toggle "store1"
      Then the store "store1" should have status enabled
      
  Scenario: Enabling and disabling multiple stores in bulk action
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
    
  Scenario: Delete stores
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
  
  Scenario: Delete multiple stores
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
