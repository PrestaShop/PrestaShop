# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add
@reset-database-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@add
Feature: Add basic product from Back Office (BO)
  As a BO user
  I need to be able to add new product with basic information from the BO

  # Test single shop behavior. Using scenario tag instead of Background to avoid loading in each scenario
  Scenario: Single shop context is loaded
    Given shop "shop1" with name "test_shop" exists
    And single shop shop1 context is loaded

  Scenario: I add a product with basic information
    in a single shop context
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | is_virtual  | false          |
    Then product "product1" should be disabled
    And product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "product1" should be assigned to default category

  Scenario: I add a product with basic information
    in a single shop context
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | is_virtual  | true           |
    Then product "product1" should be disabled
    And product "product1" should have following options:
      | product option      | value |
      | active              | false |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    And product "product1" type should be virtual
    And product "product1" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "product1" should be assigned to default category

  Scenario:
    I add a product with invalid characters in name
    in a single shop context
    When I add product "product2" with following information:
      | name[en-US] | T-shirt #1 |
      | is_virtual  | false      |
    Then I should get error that product name is invalid

  Scenario: I add a product with symbol in its name
    in a single shop context
    When I add product "product3" with following information:
      | name[en-US] | Shirt - Dom & Jquery |
      | is_virtual  | false                |
    And product "product3" localized "name" should be:
      | locale | value                |
      | en-US  | Shirt - Dom & Jquery |

  # Test multi shop behavior bellow
  Scenario: Multi shop context is loaded
    Given I add a shop group "group1" with name "group1" and color "blue"
    Given I add a shop "shop_2" with name "shop_2" and color "blue" for the group "group1"
    Given I add a shop "shop_3" with name "shop_3" and color "red" for the group "group1"

  Scenario: I add a product with basic information
    in a multi shop context
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | is_virtual  | false          |
    Then product "product1" should be disabled
    And product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "product1" should be assigned to default category
