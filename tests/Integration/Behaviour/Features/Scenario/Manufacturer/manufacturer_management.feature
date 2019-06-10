@reset-database-before-feature
Feature: Manufacturer management
  As a employee
  I must be able to correctly add, edit and delete manufacturer

  Scenario: Adding new manufacturer
    When I add new manufacturer "manufacturer-1" with following properties:
      | name             |manufacturerTest  |
      | short_description|                  |
      | description      |                  |
      | meta_title       | title test       |
      | meta_description |                  |
      | meta_keywords    |                  |
      | enabled          | false            |
    Then manufacturer "manufacturer-1" name in default language and default shop should be "manufacturerTest"
