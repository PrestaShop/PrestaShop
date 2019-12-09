@reset-database-before-feature
Feature: Duplicate order cart
  In order to create order with existing order cart duplicate
  As a Back Office (BO) user
  I need to be able to duplicate chosen order cart

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And there is existing order with id 1
    And order with id 1 has customer with id 1
    And there is cart with id 1

  Scenario:
    When I duplicate order with id 1 cart
    Then there is duplicated cart with id 6 for cart with id 1


