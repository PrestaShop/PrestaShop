# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-categories
@reset-database-before-feature
@clear-cache-before-feature
@update-categories
Feature: Update product categories from Back Office (BO)
  As a BO user
  I need to be able to update product categories from BO

  Background:
    Given language "fr" with locale "fr-FR" exists
    And category "home" in default language named "Home" exists
    And category "home" is the default one
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    And category "women" in default language named "Women" exists
    And category "accessories" in default language named "Accessories" exists

  Scenario: I assign product to categories
    Given I add product "product1" with following information:
      | name[en-US] | eastern european tracksuit |
      | type        | standard                   |
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | true      |
    Then product product1 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | true       |
    When I assign product product1 to following categories:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    Then product product1 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | false      |
      | men          | Men         | Men         | false      |
      | clothes      | Clothes     | Clothes     | true       |

  Scenario: I assign product to disabled categories
    Given I add product "product2" with following information:
      | name[en-US] | ring of wealth |
      | type        | standard       |
    And I disable category "women"
    And I disable category "accessories"
    When I assign product product2 to following categories:
      | categories       | [home, women, accessories] |
      | default category | women                      |
    Then product product2 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | false      |
      | women        | Women       | Women       | true       |
      | accessories  | Accessories | Accessories | false      |

  Scenario: I assign category which is already assigned to product
    Given product product2 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | false      |
      | women        | Women       | Women       | true       |
      | accessories  | Accessories | Accessories | false      |
    When I assign product product2 to following categories:
      | categories       | [home, women] |
      | default category | women         |
    Then product product2 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | false      |
      | women        | Women       | Women       | true       |

  Scenario: I assign default category which is not in the list of categories
    Given I add product "product3" with following information:
      | name[en-US] | golden bracelet |
      | type        | standard        |
    Then product product3 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | true       |
    When I assign product product3 to following categories:
      | categories       | [women]     |
      | default category | accessories |
    Then product product3 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | women        | Women       | Women       | false      |
      | accessories  | Accessories | Accessories | true       |

  Scenario: I assign new categories providing one non-existing category
    Given product product3 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | women        | Women       | Women       | false      |
      | accessories  | Accessories | Accessories | true       |
    When I assign product product3 to following categories:
      | categories       | [women, idontexist1] |
      | default category | accessories          |
    Then I should get error that assigning product to categories failed
    And product product3 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | women        | Women       | Women       | false      |
      | accessories  | Accessories | Accessories | true       |

  Scenario: I assign new categories providing non-existing default category
    Given product product3 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | women        | Women       | Women       | false      |
      | accessories  | Accessories | Accessories | true       |
    When I assign product product3 to following categories:
      | categories       | [women]     |
      | default category | idontexist2 |
    Then I should get error that assigning product to categories failed
    And product product3 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | women        | Women       | Women       | false      |
      | accessories  | Accessories | Accessories | true       |

  Scenario: I delete all categories from product
    Given product product3 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | women        | Women       | Women       | false      |
      | accessories  | Accessories | Accessories | true       |
    When I delete all categories from product product3
    Then product product3 should be assigned to following categories:
      | id reference | name[en-US] | name[fr-FR] | is default |
      | home         | Home        | Home        | true       |
