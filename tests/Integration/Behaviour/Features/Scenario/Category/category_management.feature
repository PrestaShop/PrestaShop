# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s category
@reset-database-before-feature
Feature: Category Management
  PrestaShop allows BO users to manage categories for products
  As a BO user
  I must be able to create, edit and delete categories in my shop

  Background: Adding new Category
    Given I add new category "category1" with following details:
      | Name                 | PC parts                  |
      | Displayed            | true                      |
      | Parent category      | Home Accessories          |
      | Friendly URL         | pc-parts                  |

  Scenario: Getting category
    Then category "category1" should have following details:
      | Name                 | PC parts                  |
      | Displayed            | true                      |
      | Parent category      | Home Accessories          |
      | Friendly URL         | pc-parts                  |

  Scenario: Edit category
    When I edit category "category1" with following details:
      | Name             | dummy category name       |
      | Displayed        | false                     |
      | Parent category  | Home Accessories          |
      | Description      | dummy description         |
      | Meta title       | dummy meta title          |
      | Meta description | dummy meta description    |
      | Friendly URL     | dummy                     |
      | Group access     | Visitor,Guest,Customer    |
    Then category "category1" should have following details:
      | Name             | dummy category name       |
      | Displayed        | false                     |
      | Parent category  | Home Accessories          |
      | Description      | dummy description         |
      | Meta title       | dummy meta title          |
      | Meta description | dummy meta description    |
      | Friendly URL     | dummy                     |
      | Group access     | Visitor,Guest,Customer    |

  Scenario: Delete category
    When I delete category "category1" choosing "associate_and_disable"
    Then category "category1" does not exist


