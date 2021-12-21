#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags specific-prices-listing
@restore-products-before-feature
@clear-cache-before-feature
@specific-prices
@specific-prices-listing

Feature: List specific prices for product in Back Office (BO)
  As an employee
  I need to be able to see all product specific prices from BO

  Background:
    Given language with iso code "en" is the default one

  Scenario: I can see a list of specific prices for product
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have 0 specific prices
    And I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | 12.56  |
      | includes tax    | true   |
      | fixed price     | 45.78  |
      | from quantity   | 1      |
    And I add a specific price price2 to product product1 with following details:
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
    And product "product1" should have 2 specific prices
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
    Then product "product1" should have following list of specific prices:
      | id reference | combination name | reduction type | reduction value | includes tax | fixed price | from quantity | shop | currency | country | group | customer | from | to |
