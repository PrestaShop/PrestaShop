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
    Given I add a shop "shop2" with name "test_second_shop" for the group "Default"
    Given I add a shop "shop3" with name "test_third_shop" for the group "Default"

  Scenario: I search for existing shops
    When I search for shops with the term "test" I should get the following results:
      | name             | group_name |
      | test_shop        | Default    |
      | test_second_shop | Default    |
      | test_third_shop  | Default    |
    When I search for shops with the term "second" I should get the following results:
      | name             | group_name |
      | test_second_shop | Default    |
    When I search for shops with the term "third" I should get the following results:
      | name            | group_name |
      | test_third_shop | Default    |
    When I search for shops with the term "THIRD" I should get the following results:
      | name            | group_name |
      | test_third_shop | Default    |
    When I search for shops with the term "doesnt_exist" I should get the following results:
      | name            | group_name |
    When I search for shops with the term " " I should get a SearchShopException
