# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shop --tags search-shops
@reset-database-before-feature
@search-shops

Feature: Search shops given a search term (BO)
  As a BO user
  I want to get a list of shops for a given search term

  Background:
    Given multiple shop context is loaded
    Given shop "shop1" with name "test_shop" exists
    Given I add a shop "shop2" with name "test_second_shop"
    Given I add a shop "shop3" with name "test_third_shop"

  Scenario: I search for existing shops
    When I search for shops with the term "test"
    Then I should get the following shop results:
      | name             | id   | group_name |
      | test_shop        | 1    | Default    |
      | test_second_shop | 2    | Default    |
      | test_third_shop  | 3    | Default    |
    When I search for shops with the term "second"
    Then I should get the following shop results:
      | name             | id   | group_name |
      | test_second_shop | 2    | Default    |
    When I search for shops with the term "third"
    Then I should get the following shop results:
      | name            | id   | group_name |
      | test_third_shop | 3    | Default    |
    When I search for shops with the term "doesnt_exist"
    Then I should get the following shop results:
      | name            | id   | group_name |
    When I search for shops with the term " "
    Then I should get a ShopException
