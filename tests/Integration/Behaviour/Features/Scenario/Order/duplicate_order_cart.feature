@reset-database-before-feature
Feature: Duplicate order cart from Back Office
  In order to duplicate order cart
  As a Back Office (BO) user
  I need to be able to call DuplicateOrderCartHandler and see changes in the database

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And there is existing order with id 1
    And order with id 1 has 1 cart

  Scenario:
    When I duplicate order with id 1 cart
    Then Order with id 1 has 2 carts


