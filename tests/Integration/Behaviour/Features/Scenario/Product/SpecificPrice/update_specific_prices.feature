# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-specific-prices
@reset-database-before-feature
@update-specific-prices
@specific-prices
Feature: Update product options from Back Office (BO)
  As a BO user
  I need to be able to update product options from BO

  Scenario: I add a specific price with amount reduction to product
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |

  Scenario: I add a specific price with percent reduction to product
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type        | percentage          |
      | reduction value       | 12.56               |
      | includes tax          | false               |
      | price                 | -12.78              |
      | from quantity         | 1                   |
      | from                  | 1969-07-20 20:17:00 |
      | to                    | 1969-07-20 20:17:00 |
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | percentage          |
      | reduction value       | 12.56               |
      | includes tax          | false               |
      | price                 | -12.78              |
      | from quantity         | 1                   |
      | from                  | 1969-07-20 20:17:00 |
      | to                    | 1969-07-20 20:17:00 |

  Scenario: I add a specific price with invalid fields I get errors
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type        | invalid |
      | reduction value       | 12.56   |
      | includes tax          | false   |
      | price                 | 0       |
      | from quantity         | 1       |
    Then I should get error that specific price reduction_type is invalid
    When I add a specific price price1 to product product1 with following details:
      | reduction type        | percentage |
      | reduction value       | -12.56     |
      | includes tax          | false      |
      | price                 | 0          |
      | from quantity         | 1          |
    Then I should get error that specific price reduction_percentage is invalid
    When I add a specific price price1 to product product1 with following details:
      | reduction type        | amount     |
      | reduction value       | -12.56     |
      | includes tax          | false      |
      | price                 | 0          |
      | from quantity         | 1          |
    Then I should get error that specific price reduction_amount is invalid
    When I add a specific price price1 to product product1 with following details:
      | reduction type        | percentage |
      | reduction value       | 12.56      |
      | includes tax          | false      |
      | price                 | 0          |
      | from quantity         | -1         |
    Then I should get error that specific price from_quantity is invalid

  Scenario: I add a specific price without relations
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
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
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
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

  Scenario: I add a specific price with relations
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |
      | shop group            | 42     |
      | shop                  | 51     |
      | currency              | 69     |
      | country               | 21     |
      | group                 | 33     |
      | customer              | 99     |
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value  |
      | reduction type        | amount |
      | reduction value       | 12.56  |
      | includes tax          | true   |
      | price                 | 45.78  |
      | from quantity         | 1      |
      | from                  |        |
      | to                    |        |
      | shop group            | 42     |
      | shop                  | 51     |
      | currency              | 69     |
      | country               | 21     |
      | group                 | 33     |
      | customer              | 99     |
