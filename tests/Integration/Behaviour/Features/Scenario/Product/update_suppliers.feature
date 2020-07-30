# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-suppliers
@reset-database-before-feature
@update-suppliers
Feature: Update product suppliers from Back Office (BO)
  As a BO user
  I need to be able to update product suppliers from BO

  Background:
    Given shop "shop1" with name "test_shop" exists
    And single shop shop1 context is loaded
    And language "language1" with locale "en-US" exists
    And there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63

  Scenario: Update standard product suppliers
    And I add new supplier supplier1 with following properties:
      | name                 | my supplier 1                   |
      | address              | Donelaicio st. 1                |
      | city                 | Kaunas                          |
      | country              | Lithuania                       |
      | enabled              | true                            |
      | description          | en-US:just a supplier           |
      | meta title           | en-US:my supplier nr one        |
      | meta description     | en-US:                          |
      | meta keywords        | en-US:sup,1                     |
      | shops                | [shop1]                         |
    And I add new supplier supplier2 with following properties:
      | name                 | my supplier 2                   |
      | address              | Donelaicio st. 2                |
      | city                 | Kaunas                          |
      | country              | Lithuania                       |
      | enabled              | true                            |
      | description          | en-US:just a supplier           |
      | meta title           | en-US:my supplier nr two        |
      | meta description     | en-US:                          |
      | meta keywords        | en-US:sup,2                     |
      | shops                | [shop1]                         |
    Given I add product "product1" with following information:
      | name       | en-US:magic staff                         |
      | is_virtual | false                                     |
    And product product1 type should be standard
    And product product1 should not have any suppliers assigned
    And product product1 default supplier reference should be empty
    When I update product product1 suppliers with following values:
      | reference         | supplier reference    | product supplier reference     | currency      | price tax excluded |
      | product1supplier1 | supplier1             | my first supplier for product1 | USD           | 10                 |
    Then product product1 should have following suppliers:
      | product supplier reference     | currency      | price tax excluded |
      | my first supplier for product1 | USD           | 10                 |
    And product product1 should have following values:
      | default supplier           | supplier1                      |
      | default supplier reference | my first supplier for product1 |
    When I set product product1 default supplier to supplier2
    Then I should get error that I cannot update default supplier
    Then product product1 should have following suppliers:
      | product supplier reference     | currency      | price tax excluded |
      | my first supplier for product1 | USD           | 10                 |
    And product product1 should have following values:
      | default supplier           | supplier1                      |
      | default supplier reference | my first supplier for product1 |
    When I update product product1 suppliers with following values:
      | reference         | supplier reference    | product supplier reference      | currency      | price tax excluded |
      | product1supplier1 | supplier1             | my first supplier for product1  | USD           | 10                 |
      | product1supplier2 | supplier2             | my second supplier for product1 | EUR           | 11                 |
    And I set product product1 default supplier to supplier2
    Then product product1 should have following suppliers:
      | product supplier reference      | currency      | price tax excluded |
      | my first supplier for product1  | USD           | 10                 |
      | my second supplier for product1 | EUR           | 11                 |
    And product product1 should have following values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |

  Scenario: Remove standard product suppliers
    Given product product1 type should be standard
    And product product1 should have following suppliers:
      | product supplier reference      | currency      | price tax excluded |
      | my first supplier for product1  | USD           | 10                 |
      | my second supplier for product1 | EUR           | 11                 |
    And product product1 should have following values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    When I delete product product1 suppliers
    Then product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier

  Scenario: Update combination product suppliers
    Given I add product "product2" with following information:
      | name       | en-US:regular T-shirt                     |
      | is_virtual | false                                     |
    And product "product2" has following combinations:
      | reference | quantity | attributes         |
      | whiteM    | 15       | Size:M;Color:White |
      | whiteL    | 15       | Size:L;Color:White |
    And product product2 type should be combination
    And product product2 should not have any suppliers assigned
    And product product2 default supplier reference should be empty
    And product "product2" has following combinations:
      | reference | quantity | attributes         |
      | whiteM    | 15      | Size:M;Color:White  |
      | whiteL    | 15      | Size:L;Color:White  |
    When I update product product2 suppliers with following values:
      | reference      | supplier reference    | product supplier reference        | currency      | price tax excluded | combination |
      | product2whiteM | supplier1             | sup white shirt M 1               | USD           | 5                  | whiteM      |
      | product2whiteL | supplier1             | sup white shirt L 2               | USD           | 5                  | whiteL      |
    Then product product2 should have following suppliers:
      | product supplier reference        | currency      | price tax excluded | combination |
      | sup white shirt M 1               | USD           | 5                  | whiteM      |
      | sup white shirt L 2               | USD           | 5                  | whiteL      |
    Then product product2 default supplier reference should be empty
