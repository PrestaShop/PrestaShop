@reset-database-before-feature
#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s manufacturer
Feature: Manufacturer management
  As a employee
  I must be able to correctly add, edit and delete manufacturer

  Scenario: Adding new manufacturer
    When I add new manufacturer "manufacturer-3" with following properties:
      | name             | best-shoes                         |
      | short_description| Makes best shoes in Europe         |
      | description      | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.|
      | meta_title       | Perfect quality shoes              |
      | meta_description |                                    |
      | meta_keywords    | Boots, shoes, slippers             |
      | enabled          | 1                                  |
    Then manufacturer "manufacturer-3" name should be "best-shoes"
    And manufacturer "manufacturer-3" "short_description" in default language should be "Makes best shoes in Europe"
    And manufacturer "manufacturer-3" "description" in default language should be "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare."
    And manufacturer "manufacturer-3" "meta_title" in default language should be "Perfect quality shoes"
    And manufacturer "manufacturer-3" "meta_description" field in default language should be empty
    And manufacturer "manufacturer-3" "meta_keywords" in default language should be "Boots, shoes, slippers"
    And manufacturer "manufacturer-3" should be enabled

  Scenario: Adding manufacturer with empty name should not be allowed
    When I add new manufacturer "manufacturer-4" with empty name
    Then I should get error message 'Manufacturer contains invalid field values'

  Scenario: Editing manufacturer
    When I edit manufacturer "manufacturer-3" with following properties:
      | name             | worst-shoes                                |
      | short_description| Worst slippers in EU                       |
      | meta_title       | Worst quality shoes                        |
      | description      |                                            |
      | meta_description | You'd better walk bare foot                |
      | enabled          | 0                                          |
    Then manufacturer "manufacturer-3" name should be "worst-shoes"
    And manufacturer "manufacturer-3" "short_description" in default language should be "Worst slippers in EU"
    And manufacturer "manufacturer-3" "description" field in default language should be empty
    And manufacturer "manufacturer-3" "meta_title" in default language should be "Worst quality shoes"
    And manufacturer "manufacturer-3" "meta_description" in default language should be "You'd better walk bare foot"
    And manufacturer "manufacturer-3" "meta_keywords" in default language should be "Boots, shoes, slippers"
    And manufacturer "manufacturer-3" should be disabled

  Scenario: Toggling manufacturer status
    Given manufacturer "manufacturer-3" should be disabled
    When I toggle manufacturer "manufacturer-3" status
    Then manufacturer "manufacturer-3" should be enabled

  Scenario: Bulk toggling manufacturer status
    Given manufacturers with ids: "1,2,3" exists
    And manufacturers with ids: "1,2,3" should be enabled
    When I disable manufacturers with ids: "1,2,3" in bulk action
    Then manufacturers with ids: "1,2,3" should be disabled

  Scenario: Deleting manufacturer
    Given manufacturer with id "3" exists
    When I delete manufacturer with id "3"
    Then manufacturer with id "3" should not be found

  Scenario: Bulk deleting manufacturers
    Given manufacturers with ids: "1,2" exists
    When I bulk delete manufacturers with ids: "1,2"
    Then manufacturers with ids: "1,2" should not be found
