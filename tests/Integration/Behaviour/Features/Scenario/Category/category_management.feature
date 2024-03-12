# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s category
@restore-all-tables-before-feature
@clear-cache-before-feature
@reset-img-after-feature
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
    And category "clothes" in default language named "Clothes" exists
    And group "visitorGroup" named "Visitor" exists
    And group "guestGroup" named "Guest" exists
    And group "customerGroup" named "Customer" exists

  Scenario: Add new category
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
      | meta keywords[en-US]          | meta,keyword,english           |
      | meta keywords[fr-FR]          | meta,keyword,french            |
      | redirect type                 | 301-category                   |
      | redirect target               | home-accessories               |
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
      | meta keywords[en-US]          | meta,keyword,english           |
      | meta keywords[fr-FR]          | meta,keyword,french            |
      | redirect type                 | 301-category                   |
      | redirect target               | home-accessories               |

  Scenario: Edit category
    Given I add new category "category2" with following details:
      | name[en-US]         | Mobile phones    |
      | name[fr-FR]         | Mobile phones fr |
      | active              | false            |
      | parent category     | home             |
      | link rewrite[en-US] | mobile-phones-en |
      | link rewrite[fr-FR] | mobile-phones-fr |
      | redirect type       | 301-category     |
      | redirect target     | home             |
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
      | meta keywords[en-US]          |                                       |
      | meta keywords[fr-FR]          |                                       |
      | redirect type                 | 301-category                          |
      | redirect target               | home                                  |
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
      | meta keywords[en-US]          | meta,keyword,english           |
      | meta keywords[fr-FR]          | meta,keyword,french            |
      | redirect type                 | 302                            |
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
      | meta keywords[en-US]          | meta,keyword,english           |
      | meta keywords[fr-FR]          | meta,keyword,french            |
      | redirect type                 | 302                            |

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

  Scenario: Delete a category which is the last category of that product
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
      | id reference | name | is default |
      | home         | Home | true       |
    And I assign product product1 to following categories:
      | categories       | [category6] |
      | default category | category6   |
    Then product "product1" should be assigned to following categories:
      | id reference | name           | is default |
      | category6    | Mobile phones6 | true       |
    # associate_and_disable mode case
    When I delete category "category6" choosing mode "associate_and_disable"
    Then category "category6" does not exist
    # product should be disabled and associated with the deleted category parent
    Then product "product1" should be assigned to following categories:
      | id reference     | name             | is default |
      | home-accessories | Home Accessories | true       |
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
      | id reference | name           | is default |
      | category7    | Mobile phones7 | true       |
    # associate_only mode case
    When I delete category "category7" choosing mode "associate_only"
    # product should be still be enabled and associated with the deleted category parent
    Then product "product1" should be assigned to following categories:
      | id reference     | name             | is default |
      | home-accessories | Home Accessories | true       |
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
      | id reference | name           | is default |
      | category8    | Mobile phones8 | true       |
    # remove_associated mode case
    When I delete category "category8" choosing mode "remove_associated"
    # product should be removed
    Then product "product1" should not exist anymore

  Scenario: Bulk delete categories which are the last remaining associated categories for some product by using associate_only deletion mode
    Given I add new category "category_b6" with following details:
      | name[en-US]         | not important    |
      | name[fr-FR]         | not important    |
      | active              | true             |
      | parent category     | home-accessories |
      | link rewrite[en-US] | not-important    |
      | link rewrite[en-US] | not-important    |
    And I add new category "category_b7" with following details:
      | name[en-US]         | not important    |
      | name[fr-FR]         | not important    |
      | active              | true             |
      | parent category     | home-accessories |
      | link rewrite[en-US] | not-important    |
      | link rewrite[en-US] | not-important    |
    And I add new category "category_b8" with following details:
      | name[en-US]         | not important    |
      | name[fr-FR]         | not important    |
      | active              | true             |
      | parent category     | home-accessories |
      | link rewrite[en-US] | not-important    |
      | link rewrite[en-US] | not-important    |
    And I add product "product_b1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And I add product "product_b2" with following information:
      | name[en-US] | bottle of beer2 |
      | type        | standard        |
    And I enable product "product_b1"
    And I enable product "product_b2"
    And product "product_b1" should be enabled
    And product "product_b2" should be enabled
    And I assign product product_b1 to following categories:
      | categories       | [category_b6,category_b7,category_b8] |
      | default category | category_b7                           |
    And I assign product product_b2 to following categories:
      | categories       | [category_b6,category_b7,category_b8] |
      | default category | category_b6                           |
    Then product "product_b1" should be assigned to following categories:
      | id reference | name          | is default |
      | category_b6  | not important | false      |
      | category_b7  | not important | true       |
      | category_b8  | not important | false      |
    Then product "product_b2" should be assigned to following categories:
      | id reference | name          | is default |
      | category_b6  | not important | true       |
      | category_b7  | not important | false      |
      | category_b8  | not important | false      |
    When I bulk delete categories "category_b6,category_b7,category_b8" choosing mode "associate_only"
    Then category "category_b6" does not exist
    And category "category_b7" does not exist
    And category "category_b8" does not exist
    Then product "product_b1" should be assigned to following categories:
      | id reference     | name             | is default |
      | home-accessories | Home Accessories | true       |
    Then product "product_b2" should be assigned to following categories:
      | id reference     | name             | is default |
      | home-accessories | Home Accessories | true       |
    And product "product_b1" should be enabled
    And product "product_b2" should be enabled

  Scenario: Bulk delete categories which are the last remaining associated categories for some product by using associate_and_disable deletion mode
    Given I add new category "category_b9" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | false         |
      | parent category     | clothes       |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    And I add new category "category_b10" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | true          |
      | parent category     | clothes       |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    And I add product "product_b1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And I add product "product_b2" with following information:
      | name[en-US] | bottle of beer2 |
      | type        | standard        |
    And I enable product "product_b1"
    And I enable product "product_b2"
    And product "product_b1" should be enabled
    And product "product_b2" should be enabled
    And I assign product product_b1 to following categories:
      | categories       | [category_b9,category_b10] |
      | default category | category_b9                |
    And I assign product product_b2 to following categories:
      | categories       | [category_b9,category_b10] |
      | default category | category_b10               |
    And product "product_b1" should be assigned to following categories:
      | id reference | name          | is default |
      | category_b9  | not important | true       |
      | category_b10 | not important | false      |
    And product "product_b2" should be assigned to following categories:
      | id reference | name          | is default |
      | category_b9  | not important | false      |
      | category_b10 | not important | true       |
    When I bulk delete categories "category_b9,category_b10" choosing mode "associate_and_disable"
    Then category "category_b9" does not exist
    And category "category_b10" does not exist
    And product "product_b1" should be assigned to following categories:
      | id reference | name    | is default |
      | clothes      | Clothes | true       |
    And product "product_b2" should be assigned to following categories:
      | id reference | name    | is default |
      | clothes      | Clothes | true       |
    And product "product_b1" should be disabled
    And product "product_b2" should be disabled

  Scenario: Bulk delete categories which are the last remaining associated categories for some product by using associate_and_remove deletion mode
    Given I add new category "category_b11" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | false         |
      | parent category     | clothes       |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    And I add new category "category_b12" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | false         |
      | parent category     | clothes       |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    And I add product "product_b1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And I add product "product_b2" with following information:
      | name[en-US] | bottle of beer2 |
      | type        | standard        |
    And I assign product product_b1 to following categories:
      | categories       | [category_b11,category_b12] |
      | default category | category_b12                |
    And I assign product product_b2 to following categories:
      | categories       | [category_b11,category_b12] |
      | default category | category_b11                |
    And product "product_b1" should be assigned to following categories:
      | id reference | name          | is default |
      | category_b11 | not important | false      |
      | category_b12 | not important | true       |
    And product "product_b2" should be assigned to following categories:
      | id reference | name          | is default |
      | category_b11 | not important | true       |
      | category_b12 | not important | false      |
    # remove_associated mode case
    When I bulk delete categories "category_b11,category_b12" choosing mode "remove_associated"
    Then category "category_b11" does not exist
    And category "category_b12" does not exist
    And product "product_b1" should not exist anymore
    And product "product_b2" should not exist anymore

  Scenario: Delete category which is assigned as default for some product, but is not the last category of that product by using associate_and_disable deletion mode
    # deletion mode shouldn't have impact for this scenario
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
      | id reference | name           | is default |
      | home         | Home           | false      |
      | category9    | Mobile phones9 | true       |
    When I delete category "category9" choosing mode "associate_and_disable"
    Then category "category9" does not exist
    Then product "product2" should be assigned to following categories:
      | id reference     | name             | is default |
      | home             | Home             | false      |
      | home-accessories | Home Accessories | true       |
    And product "product2" should be enabled


  Scenario: Delete category which is assigned as default for some product, but is not the last category of that product by using associate_only deletion mode
    # deletion mode shouldn't have impact for this scenario
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
    When I delete category "category10" choosing mode "associate_only"
    Then category "category10" does not exist
    Then product "product2" should be assigned to following categories:
      | id reference     | name             | is default |
      | home             | Home             | false      |
      | home-accessories | Home Accessories | true       |
    And product "product2" should be enabled

  Scenario: Delete category which is assigned as default for some product, but is not the last category of that product by using remove_associated deletion mode
    # deletion mode shouldn't have impact for this scenario
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
      | id reference     | name             | is default |
      | home             | Home             | false      |
      | home-accessories | Home Accessories | true       |
    And product "product2" should be enabled

  Scenario: Bulk delete categories which are assigned as default for some product, but are not the last categories of that product
    # deletion mode shouldn't have impact for this scenario
    Given I add new category "category_b11" with following details:
      | name[en-US]         | not important    |
      | name[fr-FR]         | not important    |
      | active              | false            |
      | parent category     | home-accessories |
      | link rewrite[en-US] | not-important    |
      | link rewrite[en-US] | not-important    |
    Given I add new category "category_b12" with following details:
      | name[en-US]         | not important    |
      | name[fr-FR]         | not important    |
      | active              | false            |
      | parent category     | home-accessories |
      | link rewrite[en-US] | not-important    |
      | link rewrite[en-US] | not-important    |
    And I add product "product_b1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And I add product "product_b2" with following information:
      | name[en-US] | bottle of beer2 |
      | type        | standard        |
    And I enable product "product_b1"
    And I enable product "product_b2"
    And product "product_b1" should be enabled
    And product "product_b2" should be enabled
    And I assign product product_b1 to following categories:
      | categories       | [home,category_b11,category_b12] |
      | default category | category_b11                     |
    And product "product_b1" should be assigned to following categories:
      | id reference | name          | is default |
      | home         | Home          | false      |
      | category_b11 | not important | true       |
      | category_b12 | not important | false      |
    # product_b2 has "home" category as default, so it shouldn't be affected
    And I assign product product_b2 to following categories:
      | categories       | [home,category_b12] |
      | default category | home                |
    And product "product_b2" should be assigned to following categories:
      | id reference | name          | is default |
      | home         | Home          | true       |
      | category_b12 | not important | false      |
    When I bulk delete categories "category_b11,category_b12" choosing mode "remove_associated"
    Then category "category_b11" does not exist
    And category "category_b12" does not exist
    And product "product_b1" should be assigned to following categories:
      | id reference     | name             | is default |
      | home             | Home             | false      |
      | home-accessories | Home Accessories | true       |
    And product "product_b1" should be assigned to following categories:
      | id reference     | name             | is default |
      | home             | Home             | false      |
      | home-accessories | Home Accessories | true       |

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
    When I move category "category17" up to a position "1"
    Then category "category15" position should be "0"
    Then category "category17" position should be "1"
    Then category "category16" position should be "2"
    Then category "category14" position should be "3"

  Scenario: Edit home category
    When I edit home category "home" with following details:
      | name[en-US]                   | PC parts                       |
      | name[fr-FR]                   | PC parts fr                    |
      | active                        | false                          |
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
      | meta keywords[en-US]          | meta,keyword,english           |
      | meta keywords[fr-FR]          | meta,keyword,french            |
      | redirect type                 | 404                            |
    Then category "home" should have following details:
      | name[en-US]                   | PC parts                       |
      | name[fr-FR]                   | PC parts fr                    |
      | active                        | false                          |
      | parent category               | root                           |
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
      | meta keywords[en-US]          | meta,keyword,english           |
      | meta keywords[fr-FR]          | meta,keyword,french            |
      | redirect type                 | 404                            |

  Scenario: Add new home category
    When I add new home category "home2" with following details:
      | name[en-US]                   | Home sweet home                |
      | name[fr-FR]                   | Home sweet home fr             |
      | active                        | true                           |
      | link rewrite[en-US]           | home-sweet-home                |
      | link rewrite[fr-FR]           | home-sweet-home-fr             |
      | group access                  | customerGroup                  |
      | associated shops              | shop1                          |
      | description[en-US]            | description english            |
      | description[fr-FR]            | description french             |
      | additional description[en-US] | additional description english |
      | additional description[fr-FR] | additional description french  |
      | meta description[en-US]       | meta description english       |
      | meta description[fr-FR]       | meta description french        |
      | meta title[en-US]             | meta title english             |
      | meta title[fr-FR]             | meta title french              |
      | meta keywords[en-US]          | meta,english                   |
      | meta keywords[fr-FR]          | meta,french                    |
    Then category "home2" should have following details:
      | name[en-US]                   | Home sweet home                |
      | name[fr-FR]                   | Home sweet home fr             |
      | active                        | true                           |
      | parent category               | root                           |
      | link rewrite[en-US]           | home-sweet-home                |
      | link rewrite[fr-FR]           | home-sweet-home-fr             |
      | group access                  | customerGroup                  |
      | associated shops              | shop1                          |
      | description[en-US]            | description english            |
      | description[fr-FR]            | description french             |
      | additional description[en-US] | additional description english |
      | additional description[fr-FR] | additional description french  |
      | meta description[en-US]       | meta description english       |
      | meta description[fr-FR]       | meta description french        |
      | meta title[en-US]             | meta title english             |
      | meta title[fr-FR]             | meta title french              |
      | meta keywords[en-US]          | meta,english                   |
      | meta keywords[fr-FR]          | meta,french                    |

  # We cannot test the actual image upload due to its dependency from real HTTP upload,
  # but we can mimic the upload and test EditableCategory construct and images deletion
  Scenario: Assert and delete category cover image
    Given I add new category "category18" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | true          |
      | parent category     | home          |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    And category "category18" should not have a cover image
    When I upload cover image "cover1" named "app_icon.png" to category "category18"
    # @todo: asserting image details is too much for current PR scope.
    # each category image is actually a regenerated thumbnail with a timestamp (is that expected?).
    # It is not so easy to test it, because it looks something like this:
    # ['size' => '19.187kB', 'path' => '/img/tmp/category_29.jpg?time=1668089857']
    Then category "category18" should have a cover image
    When I delete cover image for category "category18"
    Then category "category18" should not have a cover image
    And image "cover1" should not exist

  Scenario: Assert category thumbnail image
    # @todo: there seems to be no command for thumbnail deletion (is it intentional or forgotten?)
    Given I add new category "category19" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | true          |
      | parent category     | home          |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    And category "category19" should not have a thumbnail image
    When I upload thumbnail image "thumb1" named "app_icon.png" to category "category19"
    Then category "category19" should have a thumbnail image

  # enabled seems to be the same as displayed
  Scenario: Update category status
    Given I add new category "category21" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | false         |
      | parent category     | home          |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    And category "category21" is disabled
    When I enable category "category21"
    Then category "category21" is enabled
    When I disable category "category21"
    Then category "category21" is disabled

  Scenario: Update category status using bulk action
    Given I add new category "category22" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | false         |
      | parent category     | home          |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    Given I add new category "category23" with following details:
      | name[en-US]         | not important |
      | name[fr-FR]         | not important |
      | active              | false         |
      | parent category     | home          |
      | link rewrite[en-US] | not-important |
      | link rewrite[en-US] | not-important |
    When I bulk enable categories "category22,category23"
    Then category "category22" is enabled
    And category "category23" is enabled
    When I bulk disable categories "category22,category23"
    Then category "category22" is disabled
    And category "category23" is disabled
    When I enable category "category22"
    And I bulk disable categories "category22,category23"
    Then category "category22" is disabled
    And category "category23" is disabled
    When I enable category "category23"
    And I bulk enable categories "category22,category23"
    Then category "category22" is enabled
    And category "category23" is enabled
