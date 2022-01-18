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