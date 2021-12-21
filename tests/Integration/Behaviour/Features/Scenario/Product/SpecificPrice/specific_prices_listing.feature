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
    And shop "testShop" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And country "UnitedStates" with iso code "US" exists
    And group "visitor" named "Visitor" exists
    And there is customer "testCustomer" with email "pub@prestashop.com"

  Scenario: I can see a list of specific prices for product
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have 0 specific prices
    And I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | 111.50 |
      | includes tax    | true   |
      | fixed price     | 400    |
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
      | reduction value       | 111.50              |
      | includes tax          | true                |
      | fixed price           | 400                 |
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
      | product               | product1            |
    Then product "product1" should have following list of specific prices in "en" language:
      | id reference | combination | reduction type | reduction value | includes tax | fixed price | from quantity | shop      | currency | country       | group   | customer | from                | to                  |
      | price1       |             | amount         | 111.50          | true         | 400         | 1             |           |          |               |         |          | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | price2       |             | amount         | 12.56           | true         | 45.78       | 1             | test_shop | USD      | United States | Visitor | John DOE | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
