# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s category
@reset-database-before-feature
Feature: Category Management
  PrestaShop allows BO users to manage categories for products
  As a BO user
  I must be able to create, edit and delete categories in my shop

  Background: Adding new Category
    Given I add new category "category1" with following details:
      | Name                 | PC parts         |
      | Displayed            | false            |
      | Parent category      | Home Accessories |
      | Friendly URL         | pc-parts         |

  Scenario: Edit category
    When I edit category "category1" with following details:
      | Name             | dummy category name    |
      | Displayed        | false                  |
      | Parent category  | Home Accessories       |
      | Description      | dummy description      |
      | Meta title       | dummy meta title       |
      | Meta description | dummy meta description |
      | Friendly URL     | dummy                  |
      | Group access     | Visitor,Guest,Customer |
    Then category "category1" should have following details:
      | Name             | dummy category name    |
      | Displayed        | false                  |
      | Parent category  | Home Accessories       |
      | Description      | dummy description      |
      | Meta title       | dummy meta title       |
      | Meta description | dummy meta description |
      | Friendly URL     | dummy                  |
      | Group access     | Visitor,Guest,Customer |

  Scenario: Delete category
    When I delete category "category1" choosing mode "associate_and_disable"
    Then category "category1" does not exist

  Scenario: Bulk delete categories
    When I add new category "category2" with following details:
      | Name                 | PC parts 2       |
      | Displayed            | true             |
      | Parent category      | Home Accessories |
      | Friendly URL         | pc-parts2        |
    And I bulk delete categories "category1,category2" choosing mode "associate_and_disable"
    Then category "category1" does not exist
    And category "category2" does not exist

#    update category not available for multi shop context
  Scenario: Update category position
    When I add new category "category2" with following details:
      | Name                 | PC parts 2       |
      | Displayed            | true             |
      | Parent category      | Home Accessories |
      | Friendly URL         | pc-parts2        |
    And I update category "category2" with generated position and following details:
      | Parent category | Home Accessories    |
      | Way             | Up                  |
      | Found first     | false               |

  Scenario: Edit root category
    When I edit root category "Home" with following details:
      | Name             | dummy root category name    |
      | Displayed        | false                       |
      | Description      | dummy root description      |
      | Meta title       | dummy root meta title       |
      | Meta description | dummy root meta description |
      | Friendly URL     | dummy-root                  |
      | Group access     | Visitor,Guest,Customer      |
    Then category "Home" should have following details:
      | Name             | dummy root category name    |
      | Displayed        | false                       |
      | Parent category  | Root                        |
      | Description      | dummy root description      |
      | Meta title       | dummy root meta title       |
      | Meta description | dummy root meta description |
      | Friendly URL     | dummy-root                  |
      | Group access     | Visitor,Guest,Customer      |

  Scenario: Add root category
    When I add new root category "root1" with following details:
      | Name             | dummy root category name    |
      | Displayed        | false                       |
      | Description      | dummy root description      |
      | Meta title       | dummy root meta title       |
      | Meta description | dummy root meta description |
      | Friendly URL     | dummy-root                  |
      | Group access     | Visitor,Guest,Customer      |
    Then category "root1" should have following details:
      | Name             | dummy root category name    |
      | Displayed        | false                       |
      | Parent category  | Root                        |
      | Description      | dummy root description      |
      | Meta title       | dummy root meta title       |
      | Meta description | dummy root meta description |
      | Friendly URL     | dummy-root                  |
      | Group access     | Visitor,Guest,Customer      |

  Scenario: delete category cover image
    Given I edit category "category1" with following details:
      | Name                 | dummy category name    |
      | Displayed            | false                  |
      | Parent category      | Home Accessories       |
      | Description          | dummy description      |
      | Meta title           | dummy meta title       |
      | Meta description     | dummy meta description |
      | Friendly URL         | dummy                  |
      | Group access         | Visitor,Guest,Customer |
      | Category cover image | logo.jpg               |
    And category "category1" has cover image
    When I delete category "category1" cover image
    Then category "category1" does not have cover image

  Scenario: delete category menu thumbnail image
    Given I edit category "category1" with following details:
      | Name                 | dummy category name    |
      | Displayed            | false                  |
      | Parent category      | Home Accessories       |
      | Description          | dummy description      |
      | Meta title           | dummy meta title       |
      | Meta description     | dummy meta description |
      | Friendly URL         | dummy                  |
      | Group access         | Visitor,Guest,Customer |
      | Menu thumbnails      | logo.jpg               |
    And category "category1" has menu thumbnail image
    When I delete category "category1" menu thumbnail image
    Then category "category1" does not have menu thumbnail image

#    enabled seems to be the same as displayed
  Scenario: enable category
    Given category "category1" is disabled
    When I enable category "category1"
    Then category "category1" is enabled

  Scenario: disable category
    Given I add new category "category2" with following details:
      | Name                 | PC parts 2       |
      | Displayed            | true             |
      | Parent category      | Home Accessories |
      | Friendly URL         | pc-parts2        |
    When I disable category "category2"
    Then category "category2" is disabled

  Scenario: bulk enable selected categories
    Given I add new category "category2" with following details:
      | Name                 | PC parts 2       |
      | Displayed            | false            |
      | Parent category      | Home Accessories |
      | Friendly URL         | pc-parts2        |
    When I bulk enable categories "category1,category2"
    Then category "category1" is enabled
    And category "category2" is enabled

  Scenario: bulk disable selected categories
    Given I add new category "category2" with following details:
      | Name                 | PC parts 2       |
      | Displayed            | true             |
      | Parent category      | Home Accessories |
      | Friendly URL         | pc-parts2        |
    When I bulk disable categories "category1,category2"
    Then category "category1" is disabled
    And category "category2" is disabled

