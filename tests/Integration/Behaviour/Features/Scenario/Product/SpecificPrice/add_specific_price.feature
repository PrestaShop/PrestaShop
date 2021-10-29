# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-specific-price
@reset-database-before-feature
@add-specific-price
@specific-prices
Feature: Update product options from Back Office (BO)
  As a BO user
  I need to be able to update product options from BO

  Background:
    Given shop "testShop" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And country "UnitedStates" with iso code "US" exists
    And group "visitor" named "Visitor" exists
    And there is customer "testCustomer" with email "pub@prestashop.com"

  Scenario: I add a specific price with amount reduction to product
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | 12.56  |
      | includes tax    | true   |
      | price           | 45.78  |
      | from quantity   | 1      |
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | price                 | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |

  Scenario: I add a specific price with percent reduction to product
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | percentage          |
      | reduction value | 12.56               |
      | includes tax    | false               |
      | price           | -12.78              |
      | from quantity   | 1                   |
      | from            | 1969-07-20 20:17:00 |
      | to              | 1969-07-20 20:17:00 |
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
      | reduction type  | invalid |
      | reduction value | 12.56   |
      | includes tax    | false   |
      | price           | 0       |
      | from quantity   | 1       |
    Then I should get error that specific price reduction_type is invalid
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | percentage |
      | reduction value | -12.56     |
      | includes tax    | false      |
      | price           | 0          |
      | from quantity   | 1          |
    Then I should get error that specific price reduction_percentage is invalid
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | -12.56 |
      | includes tax    | false  |
      | price           | 0      |
      | from quantity   | 1      |
    Then I should get error that specific price reduction_amount is invalid
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | percentage |
      | reduction value | 12.56      |
      | includes tax    | false      |
      | price           | 0          |
      | from quantity   | -1         |
    Then I should get error that specific price from_quantity is invalid

  Scenario: I add a specific price without relations
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | 12.56  |
      | includes tax    | true   |
      | price           | 45.78  |
      | from quantity   | 1      |
      | shop            |        |
      | currency        |        |
      | country         |        |
      | group           |        |
      | customer        |        |
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | price                 | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | shop                  |                     |
      | currency              |                     |
      | country               |                     |
      | group                 |                     |
      | customer              |                     |

  Scenario: I add a specific price with relations
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | amount       |
      | reduction value | 12.56        |
      | includes tax    | true         |
      | price           | 45.78        |
      | from quantity   | 1            |
      | shop            | testShop     |
      | currency        | usd          |
      | country         | UnitedStates |
      | group           | visitor      |
      | customer        | testCustomer |
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | price                 | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | shop                  | testShop            |
      | currency              | usd                 |
      | country               | UnitedStates        |
      | group                 | visitor             |
      | customer              | testCustomer        |
