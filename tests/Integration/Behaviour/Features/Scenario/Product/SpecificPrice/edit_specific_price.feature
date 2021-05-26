# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags edit-specific-price
@reset-database-before-feature
@clear-cache-before-feature
@edit-specific-price
@specific-prices
Feature: Edit existing Specific Price from Back Office (BO).
  As an employee I want to be able to edit existing specific price for a product

  Scenario: I edit specific price reduction
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have 0 specific prices
    And I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | 12.56  |
      | includes tax    | true   |
      | price           | 45.78  |
      | from quantity   | 1      |
      | from            |        |
      | to              |        |
    And product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |
    When I edit specific price "price1" with following details:
      | reduction type  | percentage |
      | reduction value | 10         |
    Then specific price price1 should have following details:
      | specific price detail | value      |
      | reduction type        | percentage |
      | reduction value       | 10         |
      | includes tax          | true       |
      | price                 | 45.78      |
      | from quantity         | 1          |
      | from                  |            |
      | to                    |            |
