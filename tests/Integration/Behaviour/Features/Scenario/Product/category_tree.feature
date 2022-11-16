# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags category-tree
@restore-products-before-feature
@clear-cache-before-feature
@category-tree
Feature: Show category tree in product page (BO)
  As an employee
  I need to be able to see category tree in product page with marked product-category associations
#    @todo: add & assert some categories with different localized names

  Background:
    Given language with iso code "en" is the default one
    And category "home" in default language named "Home" exists
    And category "clothes" in default language named "Clothes" exists
    And category "clothes" parent is category "home"
    And category "men" in default language named "Men" exists
    And category "men" parent is category "clothes"
    And category "women" in default language named "Women" exists
    And category "women" parent is category "clothes"
    And category "accessories" in default language named "Accessories" exists
    And category "accessories" parent is category "home"
    And category "stationery" in default language named "Stationery" exists
    And category "stationery" parent is category "accessories"
    And category "home_accessories" in default language named "Home Accessories" exists
    And category "home_accessories" parent is category "accessories"
    And category "art" in default language named "Art" exists
    And category "art" parent is category "home"

  Scenario: I can see categories tree
    Given I should see following root categories in "en" language:
      | id reference | category name | display name | direct child categories   |
      | home         | Home          | Home         | [clothes,accessories,art] |
    And I should see following categories in "home" category in "en" language:
      | id reference | category name | display name | direct child categories       |
      | clothes      | Clothes       | Clothes      | [men,women]                   |
      | accessories  | Accessories   | Accessories  | [stationery,home_accessories] |
      | art          | Art           | Art          |                               |
    And I should see following categories in "clothes" category in "en" language:
      | id reference | category name | display name | direct child categories |
      | men          | Men           | Men          |                         |
      | women        | Women         | Women        |                         |
    And I should see following categories in "accessories" category in "en" language:
      | id reference     | category name    | display name     | direct child categories |
      | stationery       | Stationery       | Stationery       |                         |
      | home_accessories | Home Accessories | Home Accessories |                         |
    When I add new category "artWomen" with following details:
      | name[en-US]         | Women     |
      | active              | true      |
      | parent category     | art       |
      | link rewrite[en-US] | art-women |
    Then I should see following categories in "art" category in "en" language:
      | id reference | category name | display name | direct child categories |
      | artWomen     | Women         | Art > Women  |                         |
    And I should see following categories in "clothes" category in "en" language:
      | id reference | category name | display name    | direct child categories |
      | men          | Men           | Men             |                         |
      | women        | Women         | Clothes > Women |                         |
