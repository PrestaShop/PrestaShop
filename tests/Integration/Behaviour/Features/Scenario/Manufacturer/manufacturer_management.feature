@reset-database-before-feature
Feature: Manufacturer management
  As a employee
  I must be able to correctly add, edit and delete manufacturer

  Scenario: Adding new manufacturer
    When I add new manufacturer "manufacturer-1" with following properties:
      | name             | test-name                         |
      | short_description| desc example for testing purposes |
      | description      |                                   |
      | meta_title       | title test                        |
      | meta_description |                                   |
      | meta_keywords    | these, are, some, keywords, foo   |
      | enabled          | 0                             |
    Then manufacturer "manufacturer-1" name should be "test-name"
    And manufacturer "manufacturer-1" "short_description" in default language should be "desc example for testing purposes"
    And manufacturer "manufacturer-1" "description" field in default language should be empty
    And manufacturer "manufacturer-1" "meta_title" in default language should be "title test"
    And manufacturer "manufacturer-1" "meta_description" field in default language should be empty
    And manufacturer "manufacturer-1" "meta_keywords" in default language should be "these, are, some, keywords, foo"
    And manufacturer "manufacturer-1" should be disabled
