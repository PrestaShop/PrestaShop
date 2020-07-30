# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-suppliers
@reset-database-before-feature
@update-suppliers
Feature: Update product suppliers from Back Office (BO)
  As a BO user
  I need to be able to update product suppliers from BO

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "language1" with locale "en-US" exists

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
    And product product1 should have no suppliers assigned
    When I update product product1 suppliers with following values:
      | reference         | supplier reference    | product supplier reference     | currency      | price tax excluded |
      | product1supplier1 | supplier1             | my first supplier for product1 | USD           | 10                 |
    Then product product1 should have following suppliers:
      | reference             | product supplier reference     | currency      | price tax excluded |
      | product1supplier1     | my first supplier for product1 | USD           | 10                 |
    And product product1 should have following values:
      | default supplier | supplier1 |
    When I set product product1 default supplier to supplier2
    Then product product1 should have following suppliers:
      | reference             | product supplier reference     | currency      | price tax excluded |
      | product1supplier1     | my first supplier for product1 | USD           | 10                 |
    And product product1 should have following values:
      | default supplier | supplier2 |

  Scenario: Remove standard product suppliers
    Given product product1 type should be standard
    And product product1 should have following suppliers:
      | reference             | product supplier reference     | currency      | price tax excluded |
      | product1supplier1     | my first supplier for product1 | USD           | 10                 |
    And product product1 should have following values:
      | default supplier | supplier2 |
    When I delete product product1 suppliers
    Then product product1 should have no suppliers assigned
    And product product1 should not have a default supplier

#  Scenario: Update combination product suppliers
#    And product "Test Product Max Stock" has combinations with following details:
#      | reference | quantity | attributes         |
#      | whiteM    | 150      | Size:M;Color:White |
#      | whiteL    | 150      | Size:L;Color:White |
