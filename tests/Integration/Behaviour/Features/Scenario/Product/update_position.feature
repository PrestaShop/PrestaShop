# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-status
@reset-database-before-feature
@clear-cache-before-feature
@update-status
Feature: Update product position from BO (Back Office)
  As an employee I must be able to update product position

  Background: I add category and products
    Given I add new category "category_for_positions" with following details:
      | Name                 | Category for positions  |
      | Displayed            | true                    |
      | Parent category      | Home Accessories        |
      | Friendly URL         | category-for-positions  |
    And category "home" in default language named "Home" exists
    And I add product "product1" with following information:
      | name[en-US] | Values list poster nr. 1 (paper) |
      | type        | standard                         |
    And I assign product "product1" to following categories:
      | categories       | [home, category_for_positions] |
      | default category | home                           |
    And product "product1" should be assigned to following categories:
      | id reference           | name[en-US]            | is default |
      | category_for_positions | Category for positions | false      |
      | home                   | Home                   | default    |
    And I add product "product2" with following information:
      | name[en-US] | Values list poster nr. 2 (paper) |
      | type        | standard                         |
    And I assign product "product2" to following categories:
      | categories       | [home, category_for_positions] |
      | default category | home                           |
    And product "product2" should be assigned to following categories:
      | id reference           | name[en-US]            | is default |
      | category_for_positions | Category for positions | false      |
      | home                   | Home                   | default    |
    And positions should be assigned accordingly:
      | position | product_reference | category_reference     |
      | 1        | product1          | category_for_positions |
      | 2        | product2          | category_for_positions |
  Scenario: I update standard product position
    When I update product position in category "category_for_positions" with following values:
      | row_id | old_position | new_position | product_reference|
      | 1        | 1          | 2            | product1         |
      | 2        | 2          | 1            | product2         |
    Then positions should be assigned accordingly:
      | position | product_reference | category_reference     |
      | 2        | product1          | category_for_positions |
      | 1        | product2          | category_for_positions |
