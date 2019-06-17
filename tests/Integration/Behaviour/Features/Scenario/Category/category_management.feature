# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s category
@reset-database-before-feature
Feature: Category Management
  PrestaShop allows BO users to manage categories for products
  As a BO user
  I must be able to create, edit and delete categories in my shop

  Scenario: Adding new Category
    When I add new category "category1" with following properties:
      | name         | PC parts   |
      | is_displayed    | 0.88  |
      | parent_category_id       | 2     |
      | description | Best PC parts |
      | meta_title | PC parts meta title |
      | meta_description | PC parts meta description |
      | friendly_url | pc-parts |
      | group_ids | [1, 2] |
    Then category "category1" name should be "PC parts"
