# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shop --tags shop-crud
@restore-all-tables-before-feature
@clear-cache-before-feature
@shop-crud
Feature: Shop management (BO)
  As a BO user
  I want to add, edit and delete shops

  Background:
    Given shop "shop1" with name "test_shop" exists
    And shop group "default_group" with name "Default" exists
    And category "home" in default language named "Home" exists
    And category "clothes" in default language named "Clothes" exists
    And category "men" in default language named "Men" exists

  Scenario: Add a shop without importing data
    When I add a shop "shop_without_import" with the following properties:
      | name          | Shop without import |
      | color         | #00ff00             |
      | shop group    | default_group       |
      | root category | home                |
      | categories    | home,clothes        |
      | theme         | classic             |
    Then shop "shop_without_import" should have the following properties:
      | name              | Shop without import |
      | color             | #00ff00             |
      | shop group        | default_group       |
      | root category     | home                |
      | categories        | home,clothes        |
      | theme             | classic             |
      | shop group locked | false               |
    And shop "shop_without_import" should have the same "currency" associations as shop "shop1"
    And shop "shop_without_import" should have no "carrier" associations
    And shop "shop_without_import" should be associated to the default customer groups

  Scenario: Add a shop importing data from another shop
    When I add a shop "shop_with_import" with the following properties:
      | name          | Shop with import      |
      | shop group    | default_group         |
      | root category | home                  |
      | categories    | home                  |
      | source shop   | shop1                 |
      | imported data | carrier,country,group |
    Then shop "shop_with_import" should have the following properties:
      | name          | Shop with import |
      | root category | home             |
      | categories    | home             |
    And shop "shop_with_import" should have the same "carrier" associations as shop "shop1"
    And shop "shop_with_import" should have the same "country" associations as shop "shop1"
    And shop "shop_with_import" should have the same "group" associations as shop "shop1"
    And shop "shop_with_import" should have the same "currency" associations as shop "shop1"
    And shop "shop_with_import" should have no "zone" associations

  Scenario: Products imported in a shop with a single category get it as default category
    When I add a shop "shop_with_products" with the following properties:
      | name          | Shop with products |
      | shop group    | default_group      |
      | root category | home               |
      | categories    | home               |
      | source shop   | shop1              |
      | imported data | product            |
    Then shop "shop_with_products" should have the same "product" associations as shop "shop1"
    And the products of shop "shop_with_products" should have "home" as default category

  Scenario: Shop properties must be valid
    When I add a shop "shop_invalid_name" with the following properties:
      | name          | invalid<name> |
      | shop group    | default_group |
      | root category | home          |
      | categories    | home          |
    Then I should get error that shop name is invalid
    When I add a shop "shop_invalid_color" with the following properties:
      | name          | Invalid color |
      | color         | not a color   |
      | shop group    | default_group |
      | root category | home          |
      | categories    | home          |
    Then I should get error that shop color is invalid
    When I add a shop "shop_invalid_theme" with the following properties:
      | name          | Invalid theme |
      | shop group    | default_group |
      | root category | home          |
      | categories    | home          |
      | theme         | unknown-theme |
    Then I should get error that shop theme is invalid
    And shop group "unknown_group" does not exist
    When I add a shop "shop_unknown_group" with the following properties:
      | name          | Unknown group |
      | shop group    | unknown_group |
      | root category | home          |
      | categories    | home          |
    Then I should get error that shop group was not found

  Scenario: Shop name must be unique in its group
    When I add a shop "shop_same_name" with the following properties:
      | name          | test_shop     |
      | shop group    | default_group |
      | root category | home          |
      | categories    | home          |
    Then I should get error that shop name is already used in the group

  Scenario: Root category must be a root category associated to the shop
    When I add a shop "shop_not_root" with the following properties:
      | name          | Not a root category |
      | shop group    | default_group       |
      | root category | clothes             |
      | categories    | home,clothes        |
    Then I should get error that shop root category is invalid
    When I add a shop "shop_root_not_associated" with the following properties:
      | name          | Root not associated |
      | shop group    | default_group       |
      | root category | home                |
      | categories    | clothes             |
    Then I should get error that shop root category must be associated

  Scenario: Edit a shop
    When I add a shop "shop_to_edit" with the following properties:
      | name          | Shop to edit  |
      | shop group    | default_group |
      | root category | home          |
      | categories    | home          |
    And I edit shop "shop_to_edit" with the following properties:
      | name       | Edited shop      |
      | color      | #0000ff          |
      | categories | home,clothes,men |
    Then shop "shop_to_edit" should have the following properties:
      | name          | Edited shop      |
      | color         | #0000ff          |
      | root category | home             |
      | categories    | home,clothes,men |
    When I edit shop "shop_to_edit" with the following properties:
      | categories | clothes |
    Then I should get error that shop root category must be associated
    When I edit shop "shop_to_edit" with the following properties:
      | name | test_shop |
    Then I should get error that shop name is already used in the group

  Scenario: Move a shop to another group
    When I add a shop group "other_group" with the following properties:
      | name | Other group |
    And I add a shop "shop_to_move" with the following properties:
      | name          | Shop to move  |
      | shop group    | default_group |
      | root category | home          |
      | categories    | home          |
    And I edit shop "shop_to_move" with the following properties:
      | shop group | other_group |
    Then shop "shop_to_move" should have the following properties:
      | shop group | other_group |

  Scenario: A shop can move to a group sharing data without customers or orders
    When I add a shop group "empty_sharing_group" with the following properties:
      | name        | Empty sharing group |
      | share_stock | true                |
    And I add a shop "shop_to_sharing_group" with the following properties:
      | name          | Shop to sharing group |
      | shop group    | default_group         |
      | root category | home                  |
      | categories    | home                  |
    And I edit shop "shop_to_sharing_group" with the following properties:
      | shop group | empty_sharing_group |
    Then shop "shop_to_sharing_group" should have the following properties:
      | shop group        | empty_sharing_group |
      | shop group locked | true                |

  Scenario: Shop group rules when a group shares data
    When I add a shop group "sharing_group" with the following properties:
      | name           | Sharing group |
      | share_customer | true          |
    And I add a shop "shop_in_sharing_group" with the following properties:
      | name          | Shop in sharing group |
      | shop group    | sharing_group         |
      | root category | home                  |
      | categories    | home                  |
    Then shop "shop_in_sharing_group" should have the following properties:
      | shop group locked | true |
    When I edit shop "shop_in_sharing_group" with the following properties:
      | shop group | default_group |
    Then I should get error that shop group of the shop cannot be changed
    Given shop "shop_in_sharing_group" has a customer
    And I add a shop "shop_moving_to_sharing_group" with the following properties:
      | name          | Shop moving to sharing group |
      | shop group    | default_group                |
      | root category | home                         |
      | categories    | home                         |
    When I edit shop "shop_moving_to_sharing_group" with the following properties:
      | shop group | sharing_group |
    Then I should get error that shop cannot be moved to this shop group

  Scenario: Delete a shop
    When I add a shop "shop_to_delete" with the following properties:
      | name          | Shop to delete |
      | shop group    | default_group  |
      | root category | home           |
      | categories    | home,clothes   |
    And I delete shop "shop_to_delete"
    Then shop "shop_to_delete" should not exist
    And shop "shop_to_delete" should have no "category" associations
    And shop "shop_to_delete" should have no "currency" associations

  Scenario: Default shop and shops with customers cannot be deleted
    When I delete shop "shop1"
    Then I should get error that default shop cannot be deleted
    When I add a shop "shop_with_customer" with the following properties:
      | name          | Shop with customer |
      | shop group    | default_group      |
      | root category | home               |
      | categories    | home               |
    And shop "shop_with_customer" has a customer
    And I delete shop "shop_with_customer"
    Then I should get error that shop with customers or orders cannot be deleted
    Given shop "unknown_shop" does not exist
    When I delete shop "unknown_shop"
    Then I should get error that shop was not found
