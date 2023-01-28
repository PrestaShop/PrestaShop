#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s manufacturer
@restore-all-tables-before-feature
Feature: Manufacturer management
  As an employee
  I must be able to add, edit and delete manufacturers

  Scenario: Adding new manufacturer
    When I add new manufacturer "shoeman" with following properties:
      | name             | best-shoes                         |
      | short_description| Makes best shoes in Europe         |
      | description      | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.|
      | meta_title       | Perfect quality shoes              |
      | meta_description |                                    |
      | meta_keywords    | Boots, shoes, slippers             |
      | enabled          | true                               |
    Then manufacturer "shoeman" name should be "best-shoes"
    And manufacturer "shoeman" "short_description" in default language should be "Makes best shoes in Europe"
    And manufacturer "shoeman" "description" in default language should be "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare."
    And manufacturer "shoeman" "meta_title" in default language should be "Perfect quality shoes"
    And manufacturer "shoeman" "meta_description" field in default language should be empty
    And manufacturer "shoeman" "meta_keywords" in default language should be "Boots, shoes, slippers"
    And manufacturer "shoeman" should be enabled

  Scenario: Editing manufacturer
    When I edit manufacturer "shoeman" with following properties:
      | name             | worst-shoes                                |
      | short_description| Worst slippers in EU                       |
      | meta_title       | Worst quality shoes                        |
      | description      |                                            |
      | meta_description | You'd better walk bare foot                |
      | enabled          | false                                      |
    Then manufacturer "shoeman" name should be "worst-shoes"
    And manufacturer "shoeman" "short_description" in default language should be "Worst slippers in EU"
    And manufacturer "shoeman" "description" field in default language should be empty
    And manufacturer "shoeman" "meta_title" in default language should be "Worst quality shoes"
    And manufacturer "shoeman" "meta_description" in default language should be "You'd better walk bare foot"
    And manufacturer "shoeman" "meta_keywords" in default language should be "Boots, shoes, slippers"
    And manufacturer "shoeman" should be disabled

  Scenario: Delete manufacturer logo image
    Given I edit manufacturer "shoeman" with following properties:
      | name             | worst-shoes                  |
      | short_description| Worst slippers in EU         |
      | meta_title       | Worst quality shoes          |
      | description      |                              |
      | meta_description | You'd better walk bare foot  |
      | enabled          | false                        |
      | logo image      | logo.jpg                     |
    And the manufacturer "shoeman" has a logo image
    When I delete the manufacturer "shoeman" logo image
    Then the manufacturer "shoeman" does not have a logo image

  Scenario: Enable and disable manufacturer status
    Given manufacturer "shoeman" is disabled
    When I enable manufacturer "shoeman"
    Then manufacturer "shoeman" should be enabled
    When I disable manufacturer "shoeman"
    Then manufacturer "shoeman" should be disabled

  Scenario: Enabling and disabling multiple manufacturers in bulk action
    When I add new manufacturer "baller" with following properties:
      | name             | Baller                             |
      | short_description| Makes big balls                    |
      | description      | consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.|
      | meta_title       | Have some balls                    |
      | meta_description |                                    |
      | meta_keywords    | Balls, basketball, football        |
      | enabled          | false                              |
    And I add new manufacturer "rocket" with following properties:
      | name             | Rocket                             |
      | short_description| Cigarettes manufacturer            |
      | description      | Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.|
      | meta_title       | You smoke - you die!               |
      | meta_description | The sun is shining and the weather is sweet|
      | meta_keywords    | Cigarettes, smoke                  |
      | enabled          | true                               |
    When I enable multiple manufacturers: "baller, rocket" using bulk action
    Then manufacturers: "baller, rocket" should be enabled
    When I disable multiple manufacturers: "baller, rocket" using bulk action
    Then manufacturers: "baller, rocket" should be disabled

  Scenario: Deleting manufacturer right after changing its name
    When I edit manufacturer "shoeman" with following properties:
      | name             | Shoeman              |
    Then manufacturer "shoeman" name should be "Shoeman"
    When I delete manufacturer "shoeman"
    Then manufacturer "shoeman" should be deleted

  Scenario: Deleting multiple manufacturers in bulk action
    When I delete manufacturers: "baller, rocket" using bulk action
    Then manufacturers: "baller, rocket" should be deleted

  Scenario: Get manufacturer for viewing
    When I add new manufacturer "shoeman" with following properties:
      | name             | testBrand                         |
      | short_description| Makes best shoes in Europe         |
      | description      | Lorem ipsum dolor sit amets ornare |
      | meta_title       | Perfect quality shoes              |
      | meta_description |                                    |
      | meta_keywords    | Boots, shoes, slippers             |
      | enabled          | true                               |
    And I add new brand address "testBrandAddress" with following details:
      | Brand            | testBrand                          |
      | Last name        | testLastName                       |
      | First name       | testFirstName                      |
      | Address          | test street 123                    |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |
    And I add new brand address "testBrandAddress2" with following details:
      | Brand            | testBrand                          |
      | Last name        | testLastName                       |
      | First name       | testFirstName                      |
      | Address          | test street 12345                  |
      | City             | Vilnius                            |
      | Country          | Lithuania                          |
    Then manufacturer "shoeman" should have 2 addresses and 0 products

