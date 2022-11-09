# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s category
@restore-all-tables-before-feature
@clear-cache-before-feature
Feature: Category Management
  PrestaShop allows BO users to manage categories for products
  As a BO user
  I must be able to create, edit and delete categories in my shop

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "en" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "fr" with locale "fr-FR" exists
    And category "home" in default language named "Home" exists
    And category "root" in default language named "Root" exists
    And category "root" is the root category and it cannot be edited
    And single shop context is loaded
    And category "home" is set as the home category for shop "shop1"
    And category "home-accessories" in default language named "Home Accessories" exists
    And group "visitorGroup" named "Visitor" exists
    And group "guestGroup" named "Guest" exists
    And group "customerGroup" named "Customer" exists

#  Scenario: Add category
#    When I add new category "category1" with following details:
#      | name[en-US]                   | PC parts                       |
#      | name[fr-FR]                   | PC parts fr                    |
#      | active                        | false                          |
#      | parent category               | home-accessories               |
#      | link rewrite[en-US]           | pc-parts                       |
#      | link rewrite[fr-FR]           | pc-parts-fr                    |
#      | group access                  | visitorGroup,guestGroup        |
#      | associated shops              | shop1                          |
#      | description[en-US]            | description english            |
#      | description[fr-FR]            | description french             |
#      | additional description[en-US] | additional description english |
#      | additional description[fr-FR] | additional description french  |
#      | meta description[en-US]       | meta description english       |
#      | meta description[fr-FR]       | meta description french        |
#      | meta title[en-US]             | meta title english             |
#      | meta title[fr-FR]             | meta title french              |
#    Then category "category1" should have following details:
#      | name[en-US]                   | PC parts                       |
#      | name[fr-FR]                   | PC parts fr                    |
#      | active                        | false                          |
#      | parent category               | home-accessories               |
#      | link rewrite[en-US]           | pc-parts                       |
#      | link rewrite[fr-FR]           | pc-parts-fr                    |
#      | group access                  | visitorGroup,guestGroup        |
#      | associated shops              | shop1                          |
#      | description[en-US]            | description english            |
#      | description[fr-FR]            | description french             |
#      | additional description[en-US] | additional description english |
#      | additional description[fr-FR] | additional description french  |
#      | meta description[en-US]       | meta description english       |
#      | meta description[fr-FR]       | meta description french        |
#      | meta title[en-US]             | meta title english             |
#      | meta title[fr-FR]             | meta title french              |

  Scenario: Edit category
    Given I add new category "category2" with following details:
      | name[en-US]         | Mobile phones    |
      | name[fr-FR]         | Mobile phones fr |
      | active              | false            |
      | parent category     | home             |
      | link rewrite[en-US] | mobile-phones-en |
      | link rewrite[fr-FR] | mobile-phones-fr |
    And category "category2" should have following details:
      | name[en-US]                   | Mobile phones                         |
      | name[fr-FR]                   | Mobile phones fr                      |
      | active                        | false                                 |
      | parent category               | home                                  |
      | link rewrite[en-US]           | mobile-phones-en                      |
      | link rewrite[fr-FR]           | mobile-phones-fr                      |
      | group access                  | visitorGroup,guestGroup,customerGroup |
      | associated shops              | shop1                                 |
      | description[en-US]            |                                       |
      | description[fr-FR]            |                                       |
      | additional description[en-US] |                                       |
      | additional description[fr-FR] |                                       |
      | meta description[en-US]       |                                       |
      | meta description[fr-FR]       |                                       |
      | meta title[en-US]             |                                       |
      | meta title[fr-FR]             |                                       |
    When I edit category "category2" with following details:
      | name[en-US]                   | Mobile phones super            |
      | name[fr-FR]                   | Mobile phones super fr         |
      | active                        | true                           |
      | parent category               | home-accessories               |
      | link rewrite[en-US]           | mobile-phones-super-en         |
      | link rewrite[fr-FR]           | mobile-phones-super-fr         |
      | group access                  | guestGroup                     |
      | description[en-US]            | description english            |
      | description[fr-FR]            | description french             |
      | additional description[en-US] | additional description english |
      | additional description[fr-FR] | additional description french  |
      | meta description[en-US]       | meta description english       |
      | meta description[fr-FR]       | meta description french        |
      | meta title[en-US]             | meta title english             |
      | meta title[fr-FR]             | meta title french              |
    Then category "category2" should have following details:
      | name[en-US]                   | Mobile phones super            |
      | name[fr-FR]                   | Mobile phones super fr         |
      | active                        | true                           |
      | parent category               | home-accessories               |
      | link rewrite[en-US]           | mobile-phones-super-en         |
      | link rewrite[fr-FR]           | mobile-phones-super-fr         |
      | group access                  | guestGroup                     |
      | associated shops              | shop1                          |
      | description[en-US]            | description english            |
      | description[fr-FR]            | description french             |
      | additional description[en-US] | additional description english |
      | additional description[fr-FR] | additional description french  |
      | meta description[en-US]       | meta description english       |
      | meta description[fr-FR]       | meta description french        |
      | meta title[en-US]             | meta title english             |
      | meta title[fr-FR]             | meta title french              |
#  Scenario: Delete category
#    When I delete category "category1" choosing mode "associate_and_disable"
#    Then category "category1" does not exist
#
#  Scenario: Bulk delete categories
#    When I add new category "category2" with following details:
#      | Name            | PC parts 2       |
#      | Displayed       | true             |
#      | Parent category | Home Accessories |
#      | Friendly URL    | pc-parts2        |
#    And I bulk delete categories "category1,category2" choosing mode "associate_and_disable"
#    Then category "category1" does not exist
#    And category "category2" does not exist
#
##    update category not available for multi shop context
#  Scenario: Update category position
#    When I add new category "category2" with following details:
#      | Name            | PC parts 2       |
#      | Displayed       | true             |
#      | Parent category | Home Accessories |
#      | Friendly URL    | pc-parts2        |
#    And I update category "category2" with generated position and following details:
#      | Parent category | Home Accessories |
#      | Way             | Up               |
#      | Found first     | false            |
#
#  Scenario: Edit home category
#    When I edit home category "Home" with following details:
#      | Name             | dummy root category name    |
#      | Displayed        | false                       |
#      | Description      | dummy root description      |
#      | Meta title       | dummy root meta title       |
#      | Meta description | dummy root meta description |
#      | Friendly URL     | dummy-root                  |
#      | Group access     | Visitor,Guest,Customer      |
#    Then category "Home" should have following details:
#      | Name             | dummy root category name    |
#      | Displayed        | false                       |
#      | Parent category  | Root                        |
#      | Description      | dummy root description      |
#      | Meta title       | dummy root meta title       |
#      | Meta description | dummy root meta description |
#      | Friendly URL     | dummy-root                  |
#      | Group access     | Visitor,Guest,Customer      |
#
#  Scenario: Add root category
#    When I add new root category "root1" with following details:
#      | Name             | dummy root category name    |
#      | Displayed        | false                       |
#      | Description      | dummy root description      |
#      | Meta title       | dummy root meta title       |
#      | Meta description | dummy root meta description |
#      | Friendly URL     | dummy-root                  |
#      | Group access     | Visitor,Guest,Customer      |
#    Then category "root1" should have following details:
#      | Name             | dummy root category name    |
#      | Displayed        | false                       |
#      | Parent category  | Root                        |
#      | Description      | dummy root description      |
#      | Meta title       | dummy root meta title       |
#      | Meta description | dummy root meta description |
#      | Friendly URL     | dummy-root                  |
#      | Group access     | Visitor,Guest,Customer      |
#
#  Scenario: delete category cover image
#    Given I edit category "category1" with following details:
#      | Name                 | dummy category name    |
#      | Displayed            | false                  |
#      | Parent category      | Home Accessories       |
#      | Description          | dummy description      |
#      | Meta title           | dummy meta title       |
#      | Meta description     | dummy meta description |
#      | Friendly URL         | dummy                  |
#      | Group access         | Visitor,Guest,Customer |
#      | Category cover image | logo.jpg               |
#    And category "category1" has cover image
#    When I delete category "category1" cover image
#    Then category "category1" does not have cover image
#
#  Scenario: delete category menu thumbnail image
#    Given I edit category "category1" with following details:
#      | Name             | dummy category name    |
#      | Displayed        | false                  |
#      | Parent category  | Home Accessories       |
#      | Description      | dummy description      |
#      | Meta title       | dummy meta title       |
#      | Meta description | dummy meta description |
#      | Friendly URL     | dummy                  |
#      | Group access     | Visitor,Guest,Customer |
#      | Menu thumbnails  | logo.jpg               |
#    And category "category1" has menu thumbnail image
#    When I delete category "category1" menu thumbnail image
#    Then category "category1" does not have menu thumbnail image
#
##    enabled seems to be the same as displayed
#  Scenario: enable category
#    Given category "category1" is disabled
#    When I enable category "category1"
#    Then category "category1" is enabled
#
#  Scenario: disable category
#    Given I add new category "category2" with following details:
#      | Name            | PC parts 2       |
#      | Displayed       | true             |
#      | Parent category | Home Accessories |
#      | Friendly URL    | pc-parts2        |
#    When I disable category "category2"
#    Then category "category2" is disabled
#
#  Scenario: bulk enable selected categories
#    Given I add new category "category2" with following details:
#      | Name            | PC parts 2       |
#      | Displayed       | false            |
#      | Parent category | Home Accessories |
#      | Friendly URL    | pc-parts2        |
#    When I bulk enable categories "category1,category2"
#    Then category "category1" is enabled
#    And category "category2" is enabled
#
#  Scenario: bulk disable selected categories
#    Given I add new category "category2" with following details:
#      | Name            | PC parts 2       |
#      | Displayed       | true             |
#      | Parent category | Home Accessories |
#      | Friendly URL    | pc-parts2        |
#    When I bulk disable categories "category1,category2"
#    Then category "category1" is disabled
#    And category "category2" is disabled
#
#  Scenario: delete categories which are assigned to products
#    When I add new home category "root1" with following details:
#      | Name             | dummy root category name    |
#      | Displayed        | false                       |
#      | Description      | dummy root description      |
#      | Meta title       | dummy root meta title       |
#      | Meta description | dummy root meta description |
#      | Friendly URL     | dummy-root                  |
#      | Group access     | Visitor,Guest,Customer      |
#    Given category "root1" in default language named "dummy root category name" exists
#    And I add product "product1" with following information:
#      | name[en-US] | bottle of beer |
#      | type        | standard       |
#    Then product "product1" should be disabled
#    And product "product1" type should be standard
#    And product "product1" should be assigned to following categories:
#      | id reference | name[en-US] | is default |
#      | home         | Home        | true       |
#    And I add new category "category3" with following details:
#      | Name            | PC parts 3       |
#      | Displayed       | true             |
#      | Parent category | Home Accessories |
#      | Friendly URL    | pc-parts3        |
#    And I assign product product1 to following categories:
#      | categories       | [home,category3] |
#      | default category | category3        |
#    Then product "product1" should be assigned to following categories:
#      | id reference | name[en-US] | is default |
#      | home         | Home        | false      |
#      | category3    | PC parts 3  | true       |
#    When I delete category "category3" choosing mode "associate_and_disable"
#    Then category "category3" does not exist
#    Then product "product1" should be assigned to following categories:
#      | id reference     | name[en-US]      | is default |
#      | home             | Home             | false      |
#      | home-accessories | Home Accessories | true       |
