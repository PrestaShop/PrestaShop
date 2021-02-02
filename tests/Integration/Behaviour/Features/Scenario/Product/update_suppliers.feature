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
      | is_virtual  | false       |
    And product product1 type should be standard
    And product product1 should not have any suppliers assigned
    When I set product product1 default supplier to supplier1 and following suppliers:
      | reference         | supplier reference | product supplier reference     | currency | price tax excluded |
      | product1supplier1 | supplier1          | my first supplier for product1 | USD      | 10                 |
    Then product product1 should have following suppliers:
      | product supplier reference     | currency | price tax excluded |
      | my first supplier for product1 | USD      | 10                 |
    And product product1 should have following supplier values:
      | default supplier | supplier1 |
    When I set product product1 default supplier to supplier2 and following suppliers:
      | reference         | supplier reference | product supplier reference      | currency | price tax excluded |
      | product1supplier1 | supplier1          | my first supplier for product1  | USD      | 10                 |
      | product1supplier2 | supplier2          | my second supplier for product1 | EUR      | 11                 |
      | product1supplier3 | supplier3          | my third supplier for product1  | EUR      | 20                 |
    Then product product1 should have following suppliers:
      | product supplier reference      | currency | price tax excluded |
      | my first supplier for product1  | USD      | 10                 |
      | my second supplier for product1 | EUR      | 11                 |
      | my third supplier for product1  | EUR      | 20                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |

  Scenario: Remove one of product suppliers
    Given product product1 should have following suppliers:
      | product supplier reference      | currency | price tax excluded |
      | my first supplier for product1  | USD      | 10                 |
      | my second supplier for product1 | EUR      | 11                 |
      | my third supplier for product1  | EUR      | 20                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    When I set product product1 default supplier to supplier2 and following suppliers:
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

  Scenario: Remove all associated product suppliers
    Given product product1 type should be standard
    And product product1 should have following suppliers:
      | product supplier reference      | currency | price tax excluded |
      | my first supplier for product1  | USD      | 10                 |
      | my second supplier for product1 | EUR      | 11                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    When I remove all associated product product1 suppliers
    Then product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty

  Scenario: Update product default supplier when it is not associated with product
    Given product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    When I set product product1 default supplier to supplier2 and following suppliers:
      | reference           | supplier reference | product supplier reference     | currency | price tax excluded |
      | product1supplier1-1 | supplier1          | my first supplier for product1 | USD      | 10                 |
    Then I should get error that supplier is not associated with product
    And product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty

  Scenario: Standard product wholesale price should depend on default supplier price
    Given I add product "product3" with following information:
      | name[en-US] | magic staff |
      | is_virtual  | false       |
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
    When I set product product3 default supplier to supplier1 and following suppliers:
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

  # @todo: combination scenarios have to be removed for now, after we decided to handle combination-supplier separately
  # @todo: Don't forget to test these cases when combination-supplier is handled in another PR
#  Scenario: Update combination product suppliers
#    Given I add product "product2" with following information:
#      | name[en-US] | regular T-shirt |
#      | is_virtual  | false           |
#    And product "product2" has following combinations:
#      | reference | quantity | attributes         |
#      | whiteM    | 15       | Size:M;Color:White |
#      | whiteL    | 13       | Size:L;Color:White |
#    And product product2 type should be combination
#    And product product2 should not have any suppliers assigned
#    And product product2 default supplier reference should be empty
#    When I set product product2 default supplier to supplier1 and following suppliers:
#      | reference      | supplier reference | product supplier reference | currency | price tax excluded | combination |
#      | product2whiteM | supplier1          | sup white shirt M 1        | USD      | 5                  | whiteM      |
#      | product2whiteL | supplier1          | sup white shirt L 2        | USD      | 3                  | whiteL      |
#    Then product product2 should have following suppliers:
#      | product supplier reference | currency | price tax excluded | combination |
#      | sup white shirt M 1        | USD      | 5                  | whiteM      |
#      | sup white shirt L 2        | USD      | 3                  | whiteL      |
#    Then product product2 default supplier reference should be empty
#  Scenario: Combination product wholesale price should be reset when default supplier is assigned
#    Given I add product "product4" with following information:
#      | name[en-US] | regular T-shirt |
#      | is_virtual  | false           |
#    And product product4 should have following prices information:
#      | price            | 0     |
#      | ecotax           | 0     |
#      | tax rules group  |       |
#      | on_sale          | false |
#      | wholesale_price  | 0     |
#      | unit_price       | 0     |
#      | unity            |       |
#      | unit_price_ratio | 0     |
#    And I update product product4 prices with following information:
#      | wholesale_price | 15 |
#    And product "product4" has following combinations:
#      | reference | quantity | attributes         |
#      | whiteM    | 15       | Size:M;Color:White |
#      | whiteL    | 13       | Size:L;Color:White |
#    And product product4 type should be combination
#    And product product4 should not have any suppliers assigned
#    And product product4 default supplier reference should be empty
#    And product product4 should have following prices information:
#      | price            | 0     |
#      | ecotax           | 0     |
#      | tax rules group  |       |
#      | on_sale          | false |
#      | wholesale_price  | 15    |
#      | unit_price       | 0     |
#      | unity            |       |
#      | unit_price_ratio | 0     |
#    When I set product product4 default supplier to supplier1 and following suppliers:
#      | reference      | supplier reference | product supplier reference | currency | price tax excluded | combination |
#      | product4whiteM | supplier1          | sup white shirt M 1        | USD      | 5                  | whiteM      |
#      | product4whiteL | supplier1          | sup white shirt L 2        | USD      | 3                  | whiteL      |
#    Then product product4 should have following suppliers:
#      | product supplier reference | currency | price tax excluded | combination |
#      | sup white shirt M 1        | USD      | 5                  | whiteM      |
#      | sup white shirt L 2        | USD      | 3                  | whiteL      |
#    Then product product4 default supplier reference should be empty
#    And product product4 should have following prices information:
#      | price            | 0     |
#      | ecotax           | 0     |
#      | tax rules group  |       |
#      | on_sale          | false |
#      | wholesale_price  | 0     |
#      | unit_price       | 0     |
#      | unity            |       |
#      | unit_price_ratio | 0     |
#    And I update product product4 prices with following information:
#      | wholesale_price | 11 |
#    And product product4 should have following prices information:
#      | price            | 0     |
#      | ecotax           | 0     |
#      | tax rules group  |       |
#      | on_sale          | false |
#      | wholesale_price  | 11    |
#      | unit_price       | 0     |
#      | unity            |       |
#      | unit_price_ratio | 0     |
#    When I remove all associated product product4 suppliers
#    Then product product4 should not have any suppliers assigned
#    And product product4 default supplier reference should be empty
#    And product product4 should have following prices information:
#      | price            | 0     |
#      | ecotax           | 0     |
#      | tax rules group  |       |
#      | on_sale          | false |
#      | wholesale_price  | 0     |
#      | unit_price       | 0     |
#      | unity            |       |
#      | unit_price_ratio | 0     |
