# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s category
@reset-database-before-feature
Feature: Category Management
  PrestaShop allows BO users to manage categories for products
  As a BO user
  I must be able to create, edit and delete categories in my shop

  Scenario: Adding new Category
    Given I specify "name" "PC parts" for new category "category1"
    And I specify displayed to be "enabled" for new category "category1"
    And I specify "parent category" "Home Accessories" for new category "category1"
    And I specify "description" "Best PC parts" for new category "category1"
    And I specify "meta title" "PC parts meta title" for new category "category1"
    And I specify "meta description" "PC parts meta description" for new category "category1"
    And I specify "friendly url" "pc-parts" for new category "category1"
    And I specify group access for "Customer,Guest,Visitor" for new category "category1"
    When I add new category "category1" with specified properties
    Then category "category1" should have following details:
      | Name             | PC parts                  |
      | Displayed        | true                      |
      | Parent category  | Home Accessories          |
      | Description      | Best PC parts             |
      | Meta title       | PC parts meta title       |
      | Meta description | PC parts meta description |
      | Friendly URL     | pc-parts                  |
      | Group access     | Customer,Guest,Visitor    |
#
#    Then category "category1" "name" should be "PC parts"
#    And category "category1" should be "displayed"
#    And category "category1" parent category should be "Home Accessories"
#    And category "category1" "description" should be "Best PC parts"
#    And category "category1" "meta title" should be "PC parts meta title"
#    And category "category1" "meta description" should be "PC parts meta description"
#    And category "category1" "friendly url" should be "pc-parts"
#    And category "category1" group access should be for "Customer,Guest,Visitor"

  Scenario: Edit category
    When I edit category "category1" with following details:
      | Name | Category name |
