# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-position
@restore-products-before-feature
@clear-cache-before-feature
@update-position
Feature: Update product position from BO (Back Office)
  As an employee I must be able to update product position

  Background: I add category and products
    Given shop "shop1" with name "test_shop" exists
    And single shop context is loaded
    And language "en" with locale "en-US" exists
    And language with iso code "en" is the default one
    And category "home" in default language named "Home" exists
    And category "home" is set as the home category for shop "shop1"
    And category "home-accessories" in default language named "Home Accessories" exists
    Given I add new category "category_for_positions" with following details:
      | name[en-US]         | Category for positions |
      | active              | true                   |
      | parent category     | home-accessories       |
      | link rewrite[en-US] | category-for-positions |
    And I add new category "other_category_for_positions" with following details:
      | name[en-US]         | Other category for positions |
      | active              | true                         |
      | parent category     | home-accessories             |
      | link rewrite[en-US] | category-for-positions       |
    And category "home" in default language named "Home" exists
    And I add product "product1" with following information:
      | name[en-US] | Values list poster nr. 1 (paper) |
      | type        | standard                         |
    And I assign product "product1" to following categories:
      | categories       | [home, category_for_positions, other_category_for_positions] |
      | default category | home                                                         |
    And product "product1" should be assigned to following categories:
      | id reference                 | name                         | is default |
      | category_for_positions       | Category for positions       | false      |
      | other_category_for_positions | Other category for positions | false      |
      | home                         | Home                         | default    |
    And I add product "product2" with following information:
      | name[en-US] | Values list poster nr. 2 (paper) |
      | type        | standard                         |
    And I assign product "product2" to following categories:
      | categories       | [home, category_for_positions, other_category_for_positions] |
      | default category | home                                                         |
    And product "product2" should be assigned to following categories:
      | id reference                 | name                         | is default |
      | category_for_positions       | Category for positions       | false      |
      | other_category_for_positions | Other category for positions | false      |
      | home                         | Home                         | default    |
    And products in category category_for_positions should have the following positions:
      | position | product_reference |
      | 1        | product1          |
      | 2        | product2          |
    And products in category other_category_for_positions should have the following positions:
      | position | product_reference |
      | 1        | product1          |
      | 2        | product2          |

  Scenario: I update standard product position
    When I update product position in category "category_for_positions" with following values:
      | row_id | old_position | new_position | product_reference |
      | 1      | 1            | 2            | product1          |
      | 2      | 2            | 1            | product2          |
    Then products in category category_for_positions should have the following positions:
      | position | product_reference |
      | 2        | product1          |
      | 1        | product2          |
    # Positions should be only updated for the specified category
    But products in category other_category_for_positions should have the following positions:
      | position | product_reference |
      | 1        | product1          |
      | 2        | product2          |
    When I update product position in category "other_category_for_positions" with following values:
      | row_id | old_position | new_position | product_reference |
      | 1      | 1            | 2            | product1          |
      | 2      | 2            | 1            | product2          |
    # Now they are modified
    Then products in category other_category_for_positions should have the following positions:
      | position | product_reference |
      | 2        | product1          |
      | 1        | product2          |
