# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-specific-price
@restore-products-before-feature
@restore-currencies-after-feature
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
      | fixed price     | 45.78  |
      | from quantity   | 1      |
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | product               | product1            |

  Scenario: I add a specific price with percent reduction to product
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | percentage          |
      | reduction value | 12.56               |
      | includes tax    | false               |
      | fixed price     | 0                   |
      | from quantity   | 1                   |
      | from            | 1969-07-20 20:17:00 |
      | to              | 1969-07-20 20:17:00 |
      | product         | product1            |
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | percentage          |
      | reduction value       | 12.56               |
      | includes tax          | false               |
      | fixed price           | 0                   |
      | from quantity         | 1                   |
      | from                  | 1969-07-20 20:17:00 |
      | to                    | 1969-07-20 20:17:00 |
      | product               | product1            |

  Scenario: I add a specific price with invalid fields I get errors
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | invalid |
      | reduction value | 12.56   |
      | includes tax    | false   |
      | fixed price     | 0       |
      | from quantity   | 1       |
    Then I should get error that specific price reduction_type is invalid
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | percentage |
      | reduction value | -12.56     |
      | includes tax    | false      |
      | fixed price     | 0          |
      | from quantity   | 1          |
    Then I should get error that specific price reduction_percentage is invalid
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | -12.56 |
      | includes tax    | false  |
      | fixed price     | 0      |
      | from quantity   | 1      |
    Then I should get error that specific price reduction_amount is invalid
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | percentage |
      | reduction value | 12.56      |
      | includes tax    | false      |
      | fixed price     | 0          |
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
      | fixed price     | 45.78  |
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
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | shop                  |                     |
      | currency              |                     |
      | country               |                     |
      | group                 |                     |
      | customer              |                     |
      | product               | product1            |

  Scenario: I add a specific price with relations
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | amount       |
      | reduction value | 12.56        |
      | includes tax    | true         |
      | fixed price     | 45.78        |
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
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | shop                  | testShop            |
      | currency              | usd                 |
      | country               | UnitedStates        |
      | group                 | visitor             |
      | customer              | testCustomer        |
      | product               | product1            |

  Scenario: I cannot add specific price if identical one already exists for product
    Given product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | shop                  | testShop            |
      | currency              | usd                 |
      | country               | UnitedStates        |
      | group                 | visitor             |
      | customer              | testCustomer        |
      | product               | product1            |
    When I add a specific price price2 to product product1 with following details:
      | reduction type  | amount       |
      | reduction value | 12.56        |
      | includes tax    | true         |
      | fixed price     | 45.78        |
      | from quantity   | 1            |
      | shop            | testShop     |
      | currency        | usd          |
      | country         | UnitedStates |
      | group           | visitor      |
      | customer        | testCustomer |
    Then I should get error that identical specific price already exists for product
    Given I add product "product2" with following information:
      | name[en-US] | Prestashop backpack |
      | type        | standard            |
    And product "product2" should have 0 specific prices
    When I add a specific price price2 to product product2 with following details:
      | reduction type  | amount       |
      | reduction value | 12.56        |
      | includes tax    | true         |
      | fixed price     | 45.78        |
      | from quantity   | 1            |
      | shop            | testShop     |
      | currency        | usd          |
      | country         | UnitedStates |
      | group           | visitor      |
      | customer        | testCustomer |
    Then product "product2" should have 1 specific prices
    And specific price price2 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | shop                  | testShop            |
      | currency              | usd                 |
      | country               | UnitedStates        |
      | group                 | visitor             |
      | customer              | testCustomer        |
      | product               | product2            |

  Scenario: I cannot add specific price without providing reduction or fixed price
    Given I add product "product3" with following information:
      | name[en-US] | Special Prestashop craft beer |
      | type        | standard                      |
    Then product "product3" should have 0 specific prices
    When I add a specific price price3 to product product3 with following details:
      | reduction type  | amount       |
      | reduction value | 0            |
      | includes tax    | true         |
      | fixed price     | 0            |
      | from quantity   | 1            |
      | shop            | testShop     |
      | currency        | usd          |
      | country         | UnitedStates |
      | group           | visitor      |
      | customer        | testCustomer |
    Then I should get error that specific price reduction or price must be set
    And product "product3" should have 0 specific prices

  Scenario: It is not allowed to set negative fixed price value except the value of initial price option (-1)
    Given I add product "product4" with following information:
      | name[en-US] | Mugger mug |
      | type        | standard   |
    Then product "product4" should have 0 specific prices
    When I add a specific price price4 to product product4 with following details:
      | reduction type  | amount |
      | reduction value | 0      |
      | includes tax    | true   |
      | fixed price     | -1     |
      | from quantity   | 1      |
    Then product "product4" should have 1 specific prices
    And specific price price4 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 0                   |
      | includes tax          | true                |
      | fixed price           | -1                  |
      | from quantity         | 1                   |
      | product               | product4            |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
    When I add a specific price price5 to product product4 with following details:
      | reduction type  | percentage |
      | reduction value | 12.56      |
      | includes tax    | false      |
      | fixed price     | -50        |
      | from quantity   | 1          |
    Then I should get error that specific price "fixed price" is invalid
    And product "product4" should have 1 specific prices

  Scenario: I add a specific price with range dates
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | amount              |
      | reduction value | 12.56               |
      | includes tax    | true                |
      | fixed price     | 45.78               |
      | from quantity   | 1                   |
      | from            | 2021-01-01 10:00:00 |
      | to              | 2021-01-01 11:00:00 |
    Then product "product1" should have 1 specific prices
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 2021-01-01 10:00:00 |
      | to                    | 2021-01-01 11:00:00 |
      | product               | product1            |
