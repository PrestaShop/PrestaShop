@reset-database-before-feature
Feature: Duplicate order cart
  In order to create order with existing order cart duplicate
  As a Back Office (BO) user
  I need to be able to duplicate chosen order cart

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And order 1 has customer 1

  Scenario:
    When I duplicate order 1 cart
    Then there is duplicated cart 6 for cart 1


