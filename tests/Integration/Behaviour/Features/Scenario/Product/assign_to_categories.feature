# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags assign-categories
@reset-database-before-feature
@assign-categories
Feature: Assign product to categories from Back Office (BO)
  As a BO user
  I need to be able to assign product to categories from BO

  Scenario: I assign categories for product
    Given I add product "product1" with following information:
      | name       | en-US: eastern european tracksuit  |
      | is_virtual | false                              |
    And product "product1" should be assigned to default category
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    When I assign product product1 to following categories:
      | categories       | [men, clothes] |
      | default category | clothes        |
