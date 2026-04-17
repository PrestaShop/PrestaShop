# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s tag --tags tag-management
@restore-all-tables-before-feature
@tag-management
Feature: Tag management
  PrestaShop allows BO users to manage Tags
  As a BO user
  I must be able to create, edit and delete Tags keys

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "english" with locale "en-US" exists
    And language "french" with locale "fr-FR" exists
    And I define an uncreated tag "tagNotFound"

  Scenario: Create Products
    Given I add product "product1" with following information:
      | name[en-US] | bottle of beer           |
      | type        | virtual                  |
    And I add product "product2" with following information:
      | name[en-US] | T-shirt nr1              |
      | type        | standard                 |
    And I add product "product3" with following information:
      | name[en-US] | Shirt - Dom & Jquery     |
      | type        | standard                 |

  Scenario: Create Tag
    Given I add a tag "tag1" with specified properties:
      | name             | Tag 1               |
      | language         | french              |
      | products         | product1,product2   |
    And tag "tag1" name should be "Tag 1"
    And tag "tag1" language should be "french"
    And tag "tag1" products should be "product1,product2"

  Scenario: Creating tag with duplicate name & language should not be allowed
    Given I add a tag "tag2" with specified properties:
      | name             | Tag 1               |
      | language         | french              |
      | products         | product1,product2   |
    Then I should get error that tag is duplicate

  Scenario: Editing Tag
    When I edit tag "tag1" with specified properties:
      | name             | Tag 2               |
    And tag "tag1" name should be "Tag 2"
    And tag "tag1" language should be "french"
    And tag "tag1" products should be "product1,product2"

    When I edit tag "tag1" with specified properties:
      | language         | english             |
    And tag "tag1" name should be "Tag 2"
    And tag "tag1" language should be "english"
    And tag "tag1" products should be "product1,product2"

    When I edit tag "tag1" with specified properties:
      | products         | product2,product3   |
    And tag "tag1" name should be "Tag 2"
    And tag "tag1" language should be "english"
    And tag "tag1" products should be "product2,product3"

  Scenario: Deleting tag
    When I delete the tag "tag1"
    Then the tag "tag1" should be deleted

  Scenario: Deleting not found tag
    When I delete the tag "tagNotFound"
    Then I should get an error that the tag has not been found

  Scenario: Deleting multiple tags in bulk action
    Given I add a tag "tag4" with specified properties:
      | name             | Tag 4               |
      | language         | french              |
      | products         | product1,product2   |
    Given I add a tag "tag5" with specified properties:
      | name             | Tag 5               |
      | language         | french              |
      | products         | product1,product2   |
    When I delete tags "tag4, tag5" using bulk action
    Then tags "tag4, tag5" should be deleted

  Scenario: Deleting multiple tags in bulk action with existing in first
    Given I add a tag "tag6" with specified properties:
      | name             | Tag 6               |
      | language         | french              |
      | products         | product1,product2   |
    When I delete tags "tag6, tagNotFound" using bulk action
    Then I should get an error that the tag has not been found
    And the tag "tag6" should be deleted

  Scenario: Deleting multiple tags in bulk action with existing in last
    Given I add a tag "tag7" with specified properties:
      | name             | Tag 7               |
      | language         | french              |
      | products         | product1,product2   |
    When I delete tags "tagNotFound, tag7" using bulk action
    Then I should get an error that the tag has not been found
    And the tag "tag7" should not be deleted