@reset-database-before-feature
Feature: Manufacturer management
  As a employee
  I must be able to correctly add, edit and delete manufacturer

  Scenario: Adding new manufacturer
    When I add new manufacturer "manufacturer-3" with following properties:
      | name             | test-name                         |
      | short_description| desc example for testing purposes |
      | description      |                                   |
      | meta_title       | title test                        |
      | meta_description |                                   |
      | meta_keywords    | these, are, some, keywords, foo   |
      | enabled          | 0                                 |
    Then manufacturer "manufacturer-3" name should be "test-name"
    And manufacturer "manufacturer-3" "short_description" in default language should be "desc example for testing purposes"
    And manufacturer "manufacturer-3" "description" field in default language should be empty
    And manufacturer "manufacturer-3" "meta_title" in default language should be "title test"
    And manufacturer "manufacturer-3" "meta_description" field in default language should be empty
    And manufacturer "manufacturer-3" "meta_keywords" in default language should be "these, are, some, keywords, foo"
    And manufacturer "manufacturer-3" should be disabled

  Scenario: Editing manufacturer
    When I edit manufacturer "manufacturer-3" with following properties:
      | name             | test-name-edited2                                  |
      | short_description| edited description testing                         |
      | meta_description | meta description field filled after edit action    |
      | meta_keywords    |                                                    |
      | enabled          | 1                                                  |
      | shop_association | 1,2,3                                              |
    Then manufacturer "manufacturer-3" name should be "test-name-edited2"
    And manufacturer "manufacturer-3" "short_description" in default language should be "edited description testing"
    And manufacturer "manufacturer-3" "description" field in default language should be empty
    And manufacturer "manufacturer-3" "meta_title" in default language should be "title test"
    And manufacturer "manufacturer-3" "meta_description" in default language should be "meta description field filled after edit action"
    And manufacturer "manufacturer-3" "meta_keywords" field in default language should be empty
    And manufacturer "manufacturer-3" should be enabled

  Scenario: Deleting manufacturer
    Given manufacturer with id "3" exists
    When I delete manufacturer with id "3"
    Then manufacturer with id "3" should not be found
