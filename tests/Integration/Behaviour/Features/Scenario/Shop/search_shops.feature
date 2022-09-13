# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shop --tags search-shops
@restore-all-tables-before-feature
@clear-cache-before-feature
@search-shops

Feature: Search shops given a search term (BO)
  As a BO user
  I want to get a list of shops for a given search term

  Background:
    Given multiple shop context is loaded
    Given shop "shop1" with name "test_shop" exists

  Scenario: I search for existing shops and shop groups
    Given I add a shop group "shopGroup2" with name "test_second_shop_group" and color "green"
    Given I add a shop group "shopGroup3" with name "empty_shop_group" and color "blue"
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "shopGroup2"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "shopGroup2"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "shopGroup2"
    And I add a shop url to shop "shop1"
    And I add a shop url to shop "shop2"
    And I add a shop url to shop "shop3"
    When I search for the term "test" I should get the following results:
      | name             | group_name             | color | group_color | is_shop_group |
      | test_shop        | Default                |       |             | false         |
      | test_second_shop | test_second_shop_group | red   | green       | false         |
      | test_third_shop  | test_second_shop_group | blue  | green       | false         |
    When I search for the term "second" I should get the following results:
      | name                   | group_name             | color | group_color | is_shop_group |
      | test_second_shop       | test_second_shop_group | red   | green       | false         |
      | test_second_shop_group |                        | green |             | true          |
    When I search for the term "third" I should get the following results:
      | name             | group_name             | color | group_color | is_shop_group |
      | test_third_shop  | test_second_shop_group | blue  | green       | false         |
    When I search for the term "THIRD" I should get the following results:
      | name             | group_name             | color | group_color | is_shop_group |
      | test_third_shop  | test_second_shop_group | blue  | green       | false         |
    When I search for the term "second_shop_group" I should get the following results:
      | name                    | group_name      | color | group_color | is_shop_group |
      | test_second_shop_group  |                 | green |             | true          |
    When I search for the term "empty_shop_group" I should not get any results
    When I search for the term "doesnt_exist" I should not get any results
    When I search for the term "test_shop_without_url" I should not get any results
    When I search for the term " " I should get a SearchShopException
