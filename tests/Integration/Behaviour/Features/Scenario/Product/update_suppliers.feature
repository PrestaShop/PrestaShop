# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-suppliers
@reset-database-before-feature
@clear-cache-before-feature
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
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,1              |
      | shops                   | [shop1]            |
    And I add new supplier supplier2 with following properties:
      | name                    | my supplier 2      |
      | address                 | Donelaicio st. 2   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr two |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,2              |
      | shops                   | [shop1]            |
    And I add new supplier supplier3 with following properties:
      | name                    | my supplier 3    |
      | address                 | Donelaicio st. 3 |
      | city                    | Kaunas           |
      | country                 | Lithuania        |
      | enabled                 | true             |
      | description[en-US]      | just a 3         |
      | meta title[en-US]       | my third supp    |
      | meta description[en-US] |                  |
      | meta keywords[en-US]    | sup,3            |
      | shops                   | [shop1]          |
    Given I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product product1 type should be standard
    And product product1 should not have any suppliers assigned
    When I set product product1 suppliers:
      | reference         | supplier reference | product supplier reference     | currency | price tax excluded |
      | product1supplier1 | supplier1          | my first supplier for product1 | USD      | 10                 |
    Then product product1 should have following suppliers:
      | product supplier reference     | currency | price tax excluded |
      | my first supplier for product1 | USD      | 10                 |
    # Default supplier is the first one
    And product product1 should have following supplier values:
      | default supplier           | supplier1                      |
      | default supplier reference | my first supplier for product1 |
    When I set product product1 suppliers:
      | reference         | supplier reference | product supplier reference         | currency | price tax excluded |
      | product1supplier1 | supplier1          | my new first supplier for product1 | USD      | 10                 |
      | product1supplier2 | supplier2          | my second supplier for product1    | EUR      | 11                 |
      | product1supplier3 | supplier3          | my third supplier for product1     | EUR      | 20                 |
    Then product product1 should have following suppliers:
      | product supplier reference         | currency | price tax excluded |
      | my new first supplier for product1 | USD      | 10                 |
      | my second supplier for product1    | EUR      | 11                 |
      | my third supplier for product1     | EUR      | 20                 |
    # Default supplier was already set it should be the same but reference is updated
    And product product1 should have following supplier values:
      | default supplier           | supplier1                          |
      | default supplier reference | my new first supplier for product1 |
    # Explicitly set default supplier for product
    When I set product product1 default supplier to supplier2
    And product product1 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |

  Scenario: Remove one of product suppliers
    Given product product1 should have following suppliers:
      | product supplier reference         | currency | price tax excluded |
      | my new first supplier for product1 | USD      | 10                 |
      | my second supplier for product1    | EUR      | 11                 |
      | my third supplier for product1     | EUR      | 20                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    When I set product product1 suppliers:
      | reference         | supplier reference | product supplier reference      | currency | price tax excluded |
      | product1supplier1 | supplier1          | my first supplier for product1  | USD      | 10                 |
      | product1supplier2 | supplier2          | my second supplier for product1 | EUR      | 11                 |
    Then product product1 should have following suppliers:
      | product supplier reference      | currency | price tax excluded |
      | my first supplier for product1  | USD      | 10                 |
      | my second supplier for product1 | EUR      | 11                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    # If default supplier is removed another one is automatically associated
    When I set product product1 suppliers:
      | reference            | supplier reference | product supplier reference      | currency | price tax excluded |
      | product1supplier3bis | supplier3          | my third supplier for product1  | EUR      | 20                 |
      | product1supplier1    | supplier1          | my first supplier for product1  | USD      | 10                 |
    Then product product1 should have following suppliers:
      | product supplier reference     | currency | price tax excluded |
      | my first supplier for product1 | USD      | 10                 |
      | my third supplier for product1 | EUR      | 20                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier3                      |
      | default supplier reference | my third supplier for product1 |

  Scenario: Remove all associated product suppliers
    Given product product1 type should be standard
    And product product1 should have following suppliers:
      | product supplier reference     | currency | price tax excluded |
      | my first supplier for product1 | USD      | 10                 |
      | my third supplier for product1 | EUR      | 20                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier3                      |
      | default supplier reference | my third supplier for product1 |
    When I remove all associated product product1 suppliers
    Then product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty

  Scenario: Update product default supplier when it is not associated with product
    Given product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    When I set product product1 default supplier to supplier1
    Then I should get error that supplier is not associated with product
    And product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty

  Scenario: Standard product wholesale price should depend on default supplier price
    Given I add product "product3" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product product3 type should be standard
    And product product3 should not have any suppliers assigned
    And product product3 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 0     |
      | unit_price       | 0     |
      | unity            |       |
      | unit_price_ratio | 0     |
    When I set product product3 suppliers:
      | reference         | supplier reference | product supplier reference     | currency | price tax excluded |
      | product3supplier1 | supplier1          | my first supplier for product3 | USD      | 10                 |
    Then product product3 should have following suppliers:
      | product supplier reference     | currency | price tax excluded |
      | my first supplier for product3 | USD      | 10                 |
    And product product3 should have following supplier values:
      | default supplier | supplier1 |
    And product product3 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 10    |
      | unit_price       | 0     |
      | unity            |       |
      | unit_price_ratio | 0     |
    When I remove all associated product "product3" suppliers
    Then product product3 should not have any suppliers assigned
    And product product3 should not have a default supplier
    And product product3 default supplier reference should be empty
    And product product3 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 0     |
      | unit_price       | 0     |
      | unity            |       |
      | unit_price_ratio | 0     |
