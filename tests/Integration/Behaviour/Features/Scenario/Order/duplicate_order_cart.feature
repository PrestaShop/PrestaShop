@reset-database-before-feature
Feature: Duplicate order cart from Back Office
  In order to duplicate order cart
  As a Back Office (BO) user
  I need to be able to call DuplicateOrderCartHandler and see changes in the database

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And there is existing order with id 1
    And order with id 1 has customer with id 1
    And there is cart with id 1 for order with id 1

  Scenario:
    When I duplicate order with id 1 cart
    Then customer with id 1 has empty cart


