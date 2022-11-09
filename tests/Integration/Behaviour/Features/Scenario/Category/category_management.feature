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

  Scenario: Add category
    When I add new category "category1" with following details:
      | name[en-US]                   | PC parts                       |
      | name[fr-FR]                   | PC parts fr                    |
      | active                        | false                          |
      | parent category               | home-accessories               |
      | link rewrite[en-US]           | pc-parts                       |
      | link rewrite[fr-FR]           | pc-parts-fr                    |
      | group access                  | visitorGroup,guestGroup        |
      | associated shops              | shop1                          |
      | description[en-US]            | description english            |
      | description[fr-FR]            | description french             |
      | additional description[en-US] | additional description english |
      | additional description[fr-FR] | additional description french  |
      | meta description[en-US]       | meta description english       |
      | meta description[fr-FR]       | meta description french        |
      | meta title[en-US]             | meta title english             |
      | meta title[fr-FR]             | meta title french              |
    Then category "category1" should have following details:
      | name[en-US]                   | PC parts                       |
      | name[fr-FR]                   | PC parts fr                    |
      | active                        | false                          |
      | parent category               | home-accessories               |
      | link rewrite[en-US]           | pc-parts                       |
      | link rewrite[fr-FR]           | pc-parts-fr                    |
      | group access                  | visitorGroup,guestGroup        |
      | associated shops              | shop1                          |
      | description[en-US]            | description english            |
      | description[fr-FR]            | description french             |
      | additional description[en-US] | additional description english |
      | additional description[fr-FR] | additional description french  |
      | meta description[en-US]       | meta description english       |
      | meta description[fr-FR]       | meta description french        |
      | meta title[en-US]             | meta title english             |
      | meta title[fr-FR]             | meta title french              |

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

  Scenario: Delete category
    Given I add new category "category3" with following details:
      | name[en-US]         | Mobile phones3   |
      | name[fr-FR]         | Mobile phones fr |
      | active              | false            |
      | parent category     | home             |
      | link rewrite[en-US] | mobile-phones-en |
      | link rewrite[fr-FR] | mobile-phones-fr |
    And I add new category "category4" with following details:
      | name[en-US]         | Mobile phones4   |
      | name[fr-FR]         | Mobile phones fr |
      | active              | false            |
      | parent category     | home             |
      | link rewrite[en-US] | mobile-phones-en |
      | link rewrite[fr-FR] | mobile-phones-fr |
    And I add new category "category5" with following details:
      | name[en-US]         | Mobile phones5   |
      | name[fr-FR]         | Mobile phones fr |
      | active              | false            |
      | parent category     | home             |
      | link rewrite[en-US] | mobile-phones-en |
      | link rewrite[fr-FR] | mobile-phones-fr |
    When I delete category "category3" choosing mode "associate_and_disable"
    Then category "category3" does not exist
    When I delete category "category4" choosing mode "associate_only"
    Then category "category4" does not exist
    When I delete category "category5" choosing mode "remove_associated"
    Then category "category5" does not exist

  Scenario: Delete category which is assigned to product and it is the only one left by using different deletion modes
    Given I add new category "category6" with following details:
      | name[en-US]         | Mobile phones6    |
      | name[fr-FR]         | Mobile phones6 fr |
      | active              | false             |
      | parent category     | home-accessories  |
      | link rewrite[en-US] | mobile-phones-en  |
      | link rewrite[fr-FR] | mobile-phones-fr  |
    And I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And I enable product "product1"
    And product "product1" should be enabled
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | true       |
    And I assign product product1 to following categories:
      | categories       | [category6] |
      | default category | category6   |
    Then product "product1" should be assigned to following categories:
      | id reference | name[en-US]    | name[fr-FR]       | is default |
      | category6    | Mobile phones6 | Mobile phones6 fr | true       |
    # associate_and_disable mode case
    When I delete category "category6" choosing mode "associate_and_disable"
    Then category "category6" does not exist
    # product should be disabled and associated with the deleted category parent
    Then product "product1" should be assigned to following categories:
      | id reference     | name[en-US]      | name[fr-FR]      | is default |
      | home-accessories | Home Accessories | Home Accessories | true       |
    And product "product1" should be disabled
    Given I add new category "category7" with following details:
      | name[en-US]         | Mobile phones7    |
      | name[fr-FR]         | Mobile phones7 fr |
      | active              | false             |
      | parent category     | home-accessories  |
      | link rewrite[en-US] | mobile-phones-en  |
      | link rewrite[fr-FR] | mobile-phones-fr  |
    And I enable product "product1"
    And I assign product product1 to following categories:
      | categories       | [category7] |
      | default category | category7   |
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US]    | name[fr-FR]       | is default |
      | category7    | Mobile phones7 | Mobile phones7 fr | true       |
    # associate_only mode case
    When I delete category "category7" choosing mode "associate_only"
    # product should be still be enabled and associated with the deleted category parent
    Then product "product1" should be assigned to following categories:
      | id reference     | name[en-US]      | name[fr-FR]      | is default |
      | home-accessories | Home Accessories | Home Accessories | true       |
    And product "product1" should be enabled
    Given I add new category "category8" with following details:
      | name[en-US]         | Mobile phones8    |
      | name[fr-FR]         | Mobile phones8 fr |
      | active              | false             |
      | parent category     | home-accessories  |
      | link rewrite[en-US] | mobile-phones-en  |
      | link rewrite[fr-FR] | mobile-phones-fr  |
    And I assign product product1 to following categories:
      | categories       | [category8] |
      | default category | category8   |
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US]    | name[fr-FR]       | is default |
      | category8    | Mobile phones8 | Mobile phones8 fr | true       |
    # remove_associated mode case
    When I delete category "category8" choosing mode "remove_associated"
    # product should be removed
    Then product "product1" should not exist anymore

  Scenario: Delete category which is assigned as default for some product, but is not the last category of that product
    Given I add new category "category9" with following details:
      | name[en-US]         | Mobile phones9    |
      | name[fr-FR]         | Mobile phones9 fr |
      | active              | false             |
      | parent category     | home-accessories  |
      | link rewrite[en-US] | mobile-phones-en  |
      | link rewrite[fr-FR] | mobile-phones-fr  |
    And I add product "product2" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And I enable product "product2"
    And I assign product product2 to following categories:
      | categories       | [home,category9] |
      | default category | category9        |
    Then product "product2" should be assigned to following categories:
      | id reference | name[en-US]    | name[fr-FR]       | is default |
      | home         | Home           | Home              | false      |
      | category9    | Mobile phones9 | Mobile phones9 fr | true       |
    When I delete category "category9" choosing mode "associate_and_disable"
    Then category "category9" does not exist
    # should assign the parent of the deleted category as the default one. Product status shouldn't be impacted.
    Then product "product2" should be assigned to following categories:
      | id reference     | name[en-US]      | name[fr-FR]      | is default |
      | home             | Home             | Home             | false      |
      | home-accessories | Home Accessories | Home Accessories | true       |
    And product "product2" should be enabled
    # repeat the same with different modes to ensure that the modes doesn't have impact
    Given I add new category "category10" with following details:
      | name[en-US]         | Mobile phones10    |
      | name[fr-FR]         | Mobile phones10 fr |
      | active              | false              |
      | parent category     | home-accessories   |
      | link rewrite[en-US] | mobile-phones-en   |
      | link rewrite[fr-FR] | mobile-phones-fr   |
    And I assign product product2 to following categories:
      | categories       | [home,category10] |
      | default category | category10        |
    # associate_only mode case
    When I delete category "category10" choosing mode "associate_only"
    Then category "category10" does not exist
    Then product "product2" should be assigned to following categories:
      | id reference     | name[en-US]      | name[fr-FR]      | is default |
      | home             | Home             | Home             | false      |
      | home-accessories | Home Accessories | Home Accessories | true       |
    And product "product2" should be enabled
    Given I add new category "category11" with following details:
      | name[en-US]         | Mobile phones11    |
      | name[fr-FR]         | Mobile phones11 fr |
      | active              | false              |
      | parent category     | home-accessories   |
      | link rewrite[en-US] | mobile-phones-en   |
      | link rewrite[fr-FR] | mobile-phones-fr   |
    And I assign product product2 to following categories:
      | categories       | [home,category11] |
      | default category | category11        |
    # remove_associated mode case
    When I delete category "category11" choosing mode "remove_associated"
    Then category "category11" does not exist
    Then product "product2" should be assigned to following categories:
      | id reference     | name[en-US]      | name[fr-FR]      | is default |
      | home             | Home             | Home             | false      |
      | home-accessories | Home Accessories | Home Accessories | true       |
    And product "product2" should be enabled

  Scenario: Bulk delete categories
    Given I add new category "category12" with following details:
      | name[en-US]         | Mobile phones12    |
      | name[fr-FR]         | Mobile phones12 fr |
      | active              | false              |
      | parent category     | home-accessories   |
      | link rewrite[en-US] | mobile-phones-en   |
      | link rewrite[fr-FR] | mobile-phones-fr   |
    Given I add new category "category13" with following details:
      | name[en-US]         | Mobile phones12    |
      | name[fr-FR]         | Mobile phones12 fr |
      | active              | false              |
      | parent category     | home-accessories   |
      | link rewrite[en-US] | mobile-phones-en   |
      | link rewrite[fr-FR] | mobile-phones-fr   |
    And I bulk delete categories "category12,category13" choosing mode "associate_and_disable"
    Then category "category12" does not exist
    And category "category13" does not exist

  Scenario: Update category position
    Given I add new category "category-500" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | true          |
      | parent category     | home          |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    Given I add new category "category14" with following details:
      | name[en-US]         | PC parts 14    |
      | name[fr-FR]         | PC parts 14 fr |
      | active              | true           |
      | parent category     | category-500   |
      | link rewrite[en-US] | pc-parts14     |
      | link rewrite[en-US] | pc-parts14-fr  |
    And I add new category "category15" with following details:
      | name[en-US]         | PC parts 15    |
      | name[fr-FR]         | PC parts 15 fr |
      | active              | true           |
      | parent category     | category-500   |
      | link rewrite[en-US] | pc-parts15     |
      | link rewrite[en-US] | pc-parts15-fr  |
    And I add new category "category16" with following details:
      | name[en-US]         | PC parts 16    |
      | name[fr-FR]         | PC parts 16 fr |
      | active              | true           |
      | parent category     | category-500   |
      | link rewrite[en-US] | pc-parts16     |
      | link rewrite[en-US] | pc-parts16-fr  |
    And I add new category "category17" with following details:
      | name[en-US]         | PC parts 17    |
      | name[fr-FR]         | PC parts 17 fr |
      | active              | true           |
      | parent category     | category-500   |
      | link rewrite[en-US] | pc-parts16     |
      | link rewrite[en-US] | pc-parts17-fr  |
    # "category14" is the first category in "category-500" parent, so I assume its position is 0
    And category "category14" position should be "0"
    And category "category15" position should be "1"
    And category "category16" position should be "2"
    And category "category17" position should be "3"
    When I move category "category14" down to a position "2"
    Then category "category15" position should be "0"
    Then category "category16" position should be "1"
    Then category "category14" position should be "2"
    Then category "category17" position should be "3"
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
