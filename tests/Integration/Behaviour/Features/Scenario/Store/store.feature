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
      | latitude  | 1.0                    |
      | longitude | 1.0                    |
    And I add new store "StoreSerresCastet" with following properties:
      | name      | StoreSerresCastet      |
      | enabled   | true                   |
      | address1  | 1 rue de la foire      |
      | city      | Serres-Castet          |
      | latitude  | 2.0                    |
      | longitude | 2.0                    |
    Then stores "StorePau, StoreSerresCastet" should be enabled
    When I disable multiple stores "StorePau, StoreSerresCastet" using bulk action
    Then stores "StorePau, StoreSerresCastet" should be disabled
    When I enable multiple stores "StorePau, StoreSerresCastet" using bulk action
    Then stores "StorePau, StoreSerresCastet" should be enabled
