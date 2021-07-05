# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags edit-specific-price
@reset-database-before-feature
@clear-cache-before-feature
@edit-specific-price
@specific-prices
Feature: Edit existing Specific Price from Back Office (BO).
  As an employee I want to be able to edit existing specific price for a product

  Background:
    Given shop group "default" with name "Default" exists

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
    When I edit specific price "price1" with following details:
      | reduction type  | amount |
      | reduction value | 12.56  |
    And specific price price1 should have following details:
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |

  Scenario: I edit specific price tax, price and quantity
    Given specific price price1 should have following details:
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |
    When I edit specific price "price1" with following details:
      | includes tax  | false |
      | price         | 50    |
      | from quantity | 3     |
    Then specific price price1 should have following details:
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | false  |
      | price                 | 50     |
      | from quantity         | 3      |
      | from                  |        |
      | to                    |        |
    When I edit specific price "price1" with following details:
      | price         | 45.78 |
      | includes tax  | true  |
      | from quantity | 1     |
    Then specific price price1 should have following details:
      | specific price detail | value  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |

  Scenario: I edit specific price relations
    Given specific price price1 should have following details:
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |
      | shop group            |        |
      | shop                  |        |
      | currency              |        |
      | country               |        |
      | group                 |        |
      | customer              |        |
    When I edit specific price "price1" with following details:
      | shop group | default |
      | shop       | 51      |
      | currency   | 69      |
      | country    | 21      |
      | group      | 33      |
      | customer   | 99      |
    Then specific price price1 should have following details:
      | specific price detail | value   |
      | reduction type        | amount  |
      | reduction value       | 12.56   |
      | includes tax          | true    |
      | price                 | 45.78   |
      | from quantity         | 1       |
      | from                  |         |
      | to                    |         |
      | shop group            | default |
      | shop                  | 51      |
      | currency              | 69      |
      | country               | 21      |
      | group                 | 33      |
      | customer              | 99      |
    When I edit specific price "price1" with following details:
      | shop group |  |
      | shop       |  |
      | currency   |  |
      | country    |  |
      | group      |  |
      | customer   |  |
    Then specific price price1 should have following details:
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |
      | shop group            |        |
      | shop                  |        |
      | currency              |        |
      | country               |        |
      | group                 |        |
      | customer              |        |

  Scenario: I edit specific price dates
    Given specific price price1 should have following details:
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |
    When I edit specific price "price1" with following details:
      | from | 2021-01-01 10:00:00 |
      | to   | 2021-01-01 11:00:00 |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | price                 | 45.78               |
      | from quantity         | 1                   |
      | from                  | 2021-01-01 10:00:00 |
      | to                    | 2021-01-01 11:00:00 |
    When I edit specific price "price1" with following details:
      | from | 0000-00-00 00:00:00 |
      | to   | 0000-00-00 00:00:00 |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | price                 | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
    When I edit specific price "price1" with following details:
      | from | 2021-01-01 10:00:00 |
      | to   | 0000-00-00 00:00:00 |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | price                 | 45.78               |
      | from quantity         | 1                   |
      | from                  | 2021-01-01 10:00:00 |
      | to                    | 0000-00-00 00:00:00 |
    When I edit specific price "price1" with following details:
      | from | 0000-00-00 00:00:00 |
      | to   | 2021-01-01 10:00:00 |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | price                 | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 2021-01-01 10:00:00 |
    When I edit specific price "price1" with following details:
      | from | 2021-01-01 10:00:00 |
      | to   | 2020-01-01 10:00:00 |
    Then I should get error that specific price "date range" is invalid
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | price                 | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 2021-01-01 10:00:00 |
    When I edit specific price "price1" with following details:
      | from |  |
      | to   |  |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | price                 | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 2021-01-01 10:00:00 |
