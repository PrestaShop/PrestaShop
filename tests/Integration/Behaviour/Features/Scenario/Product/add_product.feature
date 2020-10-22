# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add
@reset-database-before-feature
@clear-cache-after-feature
@add
Feature: Add basic product from Back Office (BO)
  As a BO user
  I need to be able to add new product with basic information from the BO

  Scenario: I add a product with basic information
    When I add product "product1" with following information:
      | name       | en-US:bottle of beer |
      | is_virtual | false                |
    Then product "product1" should have following values:
      | active           | false          |
    And product "product1" type should be standard
    And product "product1" localized "name" should be "en-US:bottle of beer"
    And product "product1" should be assigned to default category

  Scenario: I add a product with basic information
    When I add product "product1" with following information:
      | name       | en-US:bottle of beer |
      | is_virtual | true                 |
    Then product "product1" should have following values:
      | active           | false          |
    Then product "product1" should have following options information:
      | condition        | new            |
    And product "product1" type should be virtual
    And product "product1" localized "name" should be "en-US:bottle of beer"
    And product "product1" should be assigned to default category

  Scenario: I add a product with invalid characters in name
    When I add product "product2" with following information:
      | name       | en-US: T-shirt #1 |
      | is_virtual | false             |
    Then I should get error that product name is invalid

