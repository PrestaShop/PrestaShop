@reset-database-before-feature
Feature: Contact
  In order to create customizable contact us form for customers
  As a BO user
  I should be able to add and edit new contact

  Scenario: Add new contact
    Given there is no contact with id 3
    And the last contact is with id 2
    When I add new contact with title "testadas" and messages saving is enabled
    Then I should be able to get contact with id 3 for editing
    And contact with id 3 should have title "testadas"
    And contact with id 3 should have messages saving disabled
