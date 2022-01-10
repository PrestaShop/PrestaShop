# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags edit-specific-price
@restore-products-before-feature
@restore-currencies-after-feature
@clear-cache-before-feature
@edit-specific-price
@specific-prices
Feature: Edit existing Specific Price from Back Office (BO).
  As an employee I want to be able to edit existing specific price for a product

  Background:
    Given shop "testShop" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And country "UnitedStates" with iso code "US" exists
    And group "visitor" named "Visitor" exists
    And there is customer "testCustomer" with email "pub@prestashop.com"

  Scenario: I edit specific price reduction
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
    And product "product1" should have 1 specific prices
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
    When I edit specific price "price1" with following details:
      | reduction type  | percentage |
      | reduction value | 10         |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | percentage          |
      | reduction value       | 10                  |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | reduction type  | amount |
      | reduction value | 12.56  |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | product               | product1            |

  Scenario: I edit specific price tax, price and quantity
    Given specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | includes tax  | false |
      | fixed price   | 50    |
      | from quantity | 3     |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | false               |
      | fixed price           | 50                  |
      | from quantity         | 3                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | fixed price   | 45.78 |
      | includes tax  | true  |
      | from quantity | 1     |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | includes tax          | true                |
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | product               | product1            |

  Scenario: I edit specific price relations
    Given specific price price1 should have following details:
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
    When I edit specific price "price1" with following details:
      | shop     | testShop     |
      | currency | usd          |
      | country  | UnitedStates |
      | group    | visitor      |
      | customer | testCustomer |
    Then specific price price1 should have following details:
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
    When I edit specific price "price1" with following details:
      | shop     |  |
      | currency |  |
      | country  |  |
      | group    |  |
      | customer |  |
    Then specific price price1 should have following details:
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

  Scenario: I edit specific price with non-existing relations
    Given currency "jen" does not exist
    And shop "nonExistingShop" does not exist
    And country "Simsalabim" does not exist
    And group "pirates" does not exist
    And customer "Rick - the pickle" does not exist
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
    When I edit specific price "price1" with following details:
      | shop | nonExistingShop |
    Then I should get error that shop was not found
    When I edit specific price "price1" with following details:
      | currency | jen |
    Then I should get error that currency was not found
    When I edit specific price "price1" with following details:
      | country | Simsalabim |
    Then I should get error that country was not found
    When I edit specific price "price1" with following details:
      | group | pirates |
    Then I should get error that group was not found
    When I edit specific price "price1" with following details:
      | customer | Rick - the pickle |
    Then I should get error that customer was not found
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

  Scenario: I edit specific price dates
    Given specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | from | 2021-01-01 10:00:00 |
      | to   | 2021-01-01 11:00:00 |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 2021-01-01 10:00:00 |
      | to                    | 2021-01-01 11:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | from | 0000-00-00 00:00:00 |
      | to   | 0000-00-00 00:00:00 |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | from | 2021-01-01 10:00:00 |
      | to   | 0000-00-00 00:00:00 |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 2021-01-01 10:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | from | 0000-00-00 00:00:00 |
      | to   | 2021-01-01 10:00:00 |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 2021-01-01 10:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | from | 2021-01-01 10:00:00 |
      | to   | 2020-01-01 10:00:00 |
    Then I should get error that specific price "date range" is invalid
    And specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 2021-01-01 10:00:00 |
      | product               | product1            |

  Scenario: Date ranges are not reset when not provided
    Given specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 2021-01-01 10:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | includes tax | false |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | false               |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 2021-01-01 10:00:00 |
      | product               | product1            |
    When I edit specific price "price1" with following details:
      | includes tax | true |
    Then specific price price1 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | 45.78               |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 2021-01-01 10:00:00 |
      | product               | product1            |

  Scenario: I cannot edit specific price if it results in having 2 identical specific prices for product
    Given I add product "product2" with following information:
      | name[en-US] | Prestashop backpack |
      | type        | standard            |
    And product "product2" should have 0 specific prices
    And I add a specific price price2 to product product2 with following details:
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
    And I add a specific price price3 to product product2 with following details:
      | reduction type  | amount       |
      | reduction value | 12.56        |
      | includes tax    | true         |
      | fixed price     | 45.78        |
      | from quantity   | 1            |
      | shop            | testShop     |
      | currency        | usd          |
      | country         | UnitedStates |
      | group           | visitor      |
      | customer        |              |
    And product "product2" should have 2 specific prices
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
    And specific price price3 should have following details:
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
      | customer              |                     |
      | product               | product2            |
    When I edit specific price "price3" with following details:
      | customer | testCustomer |
    Then I should get error that identical specific price already exists for product
    And specific price price3 should have following details:
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
      | customer              |                     |
      | product               | product2            |

  Scenario: I cannot edit specific price when it results in having no reduction or fixed price
    Given specific price price3 should have following details:
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
      | customer              |                     |
      | product               | product2            |
    When I edit specific price "price3" with following details:
      | reduction type  | percentage   |
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
    And specific price price3 should have following details:
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
      | customer              |                     |
      | product               | product2            |

  Scenario: It is not allowed to set negative fixed price value except the value of initial price option (-1)
    Given specific price price3 should have following details:
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
      | customer              |                     |
      | product               | product2            |
    When I edit specific price "price3" with following details:
      | fixed price | -1 |
    Then specific price price3 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | -1                  |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | shop                  | testShop            |
      | currency              | usd                 |
      | country               | UnitedStates        |
      | group                 | visitor             |
      | customer              |                     |
      | product               | product2            |
    When I edit specific price "price3" with following details:
      | fixed price | -50 |
    Then I should get error that specific price "fixed price" is invalid
    And specific price price3 should have following details:
      | specific price detail | value               |
      | reduction type        | amount              |
      | reduction value       | 12.56               |
      | includes tax          | true                |
      | fixed price           | -1                  |
      | from quantity         | 1                   |
      | from                  | 0000-00-00 00:00:00 |
      | to                    | 0000-00-00 00:00:00 |
      | shop                  | testShop            |
      | currency              | usd                 |
      | country               | UnitedStates        |
      | group                 | visitor             |
      | customer              |                     |
      | product               | product2            |
