# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-specific-price
@restore-products-before-feature
@restore-currencies-after-feature
@delete-specific-price
@specific-prices
Feature: Delete a product specific price from Back Office (BO)
  As a BO user
  I need to be able to delete a product specific price from BO

  Scenario: I delete a specific price
    Given shop "testShop" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And country "UnitedStates" with iso code "US" exists
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | 10     |
      | includes tax    | true   |
      | fixed price     | 30     |
      | from quantity   | 1      |
    When I add a specific price price2 to product product1 with following details:
      | reduction type  | percentage          |
      | reduction value | 12.56               |
      | includes tax    | false               |
      | fixed price     | 0                   |
      | from quantity   | 1                   |
      | from            | 2022-07-20 20:17:00 |
      | to              | 2023-07-20 20:17:00 |
      | product         | product1            |
    Then product "product1" should have 2 specific prices
    When I delete specific price "price1"
    Then product "product1" should have 1 specific prices
    When I delete specific price "price2"
    Then product "product1" should have 0 specific prices
