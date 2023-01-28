#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s title
@restore-all-tables-before-feature
Feature: Title management
  As an employee
  I must be able to add, edit and delete titles

  Background:
    Given I define an uncreated title "titleNotFound"

  Scenario: Adding new title
    When I add a new title "3rdTitle" with the following properties:
      | name    | Third Title    |
      | type    | Other          |
#    Then title "thirdTitle" name should be "Normandy"
#    Then title "thirdTitle" country should be "United titles"
#    Then title "thirdTitle" zone should be "Europe"
#    And title "thirdTitle" should be enabled
#
#  Scenario: Editing title
#    When I edit title "thirdTitle" with following properties:
#      | name    | Britain       |
#      | enabled | false         |
#      | country | Italy         |
#      | zone    | South America |
#    Then title "thirdTitle" name should be "Britain"
#    Then title "thirdTitle" country should be "Italy"
#    Then title "thirdTitle" zone should be "South America"
#    And title "thirdTitle" should be disabled

  Scenario: Deleting title
    When I delete the title "3rdTitle"
    Then the title "3rdTitle" should be deleted

  Scenario: Deleting not found title
    When I delete the title "titleNotFound"
    Then I should get an error that the title has not been found

  Scenario: Deleting multiple titles in bulk action
    When I add a new title "4thTitle" with the following properties:
      | name    | Fourth  Title  |
      | type    | Other          |
    And I add a new title "5thTitle" with the following properties:
      | name    | Fifth Title    |
      | type    | Other          |
    When I delete titles "4thTitle, 5thTitle" using bulk action
    Then titles "4thTitle, 5thTitle" should be deleted

  Scenario: Deleting multiple titles in bulk action with existing in first
    When I add a new title "6thTitle" with the following properties:
      | name    | Sixth  Title   |
      | type    | Other          |
    When I delete titles "6thTitle, titleNotFound" using bulk action
    Then I should get an error that the title has not been found
    And the title "6thTitle" should be deleted

  Scenario: Deleting multiple titles in bulk action with existing in last
    When I add a new title "7thTitle" with the following properties:
      | name    | Seventh  Title |
      | type    | Other          |
    When I delete titles "titleNotFound, 7thTitle" using bulk action
    Then I should get an error that the title has not been found
    And the title "7thTitle" should not be deleted
