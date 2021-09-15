# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add
@reset-database-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@add
Feature: Add basic product from Back Office (BO)
  As a BO user
  I need to be able to add new product with basic information from the BO

  Background:
    Given language "language1" with locale "en-US" exists
    And category "home" in default language named "Home" exists
    And category "home" is the default one

  Scenario: I add a product with basic information
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "product1" should be disabled
    And product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US] | is default |
      | home         | Home        | true       |

  Scenario: I add a product with basic information
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | virtual        |
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
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US] | is default |
      | home         | Home        | true       |

  Scenario: I add a product with invalid characters in name
    When I add product "product2" with following information:
      | name[en-US] | T-shirt #1 |
      | type        | standard   |
    Then I should get error that product name is invalid

  Scenario: I add a product with symbol in its name
    When I add product "product3" with following information:
      | name[en-US] | Shirt - Dom & Jquery |
      | type        | standard             |
    And product "product3" localized "name" should be:
      | locale | value                |
      | en-US  | Shirt - Dom & Jquery |
