# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shop --tags search-shops
@reset-database-before-feature
@clear-cache-before-feature
@search-shops

Feature: Search shops given a search term (BO)
  As a BO user
  I want to get a list of shops for a given search term

  Background:
    Given multiple shop context is loaded
    Given shop "shop1" with name "test_shop" exists

  Scenario: I search for existing shops
    Given I add a shop group "shopGroup2" with name "test_second_shop_group" and color "green"
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "test_second_shop_group"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    When I search for shops with the term "test" I should get the following results:
      | name             | group_name             | color | group_color |
      | test_shop        | Default                |       |             |
      | test_second_shop | test_second_shop_group | red   | green       |
      | test_third_shop  | test_second_shop_group | blue  | green       |
    When I search for shops with the term "second" I should get the following results:
      | name             | group_name             | color | group_color |
      | test_second_shop | test_second_shop_group | red   | green       |
    When I search for shops with the term "third" I should get the following results:
      | name             | group_name             | color | group_color |
      | test_third_shop  | test_second_shop_group | blue  | green       |
    When I search for shops with the term "THIRD" I should get the following results:
      | name             | group_name             | color | group_color |
      | test_third_shop  | test_second_shop_group | blue  | green       |
    When I search for shops with the term "doesnt_exist" I should not get any results
    When I search for shops with the term " " I should get a SearchShopException
