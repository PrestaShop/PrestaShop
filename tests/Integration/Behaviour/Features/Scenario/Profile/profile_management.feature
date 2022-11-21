# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s profile --tags profile-management
@profile-management
Feature: Manage profiles from BO
  As a BO user
  I need to be able to add edit delete profiles

  Background:
    Given language "language_en" with locale "en-US" exists
    And language "language_fr" with locale "fr-FR" exists

  Scenario: I can add and edit a profile
    When I add a profile "test_profile" with following information:
      | name[en-US] | Test Profile |
      | name[fr-FR] | Profil Test  |
    Then profile "test_profile" should have the following information:
      | name[en-US] | Test Profile                         |
      | name[fr-FR] | Profil Test                          |
      | avatarUrl   | http://localhost/img/pr/default.jpg  |
    When I edit a profile "test_profile" with following information:
      | name[en-US] | Test Profile edited |
      | name[fr-FR] | Profil Test édité   |
    Then profile "test_profile" should have the following information:
      | name[en-US] | Test Profile edited                  |
      | name[fr-FR] | Profil Test édité                    |
      | avatarUrl   | http://localhost/img/pr/default.jpg  |
    When I delete profile "test_profile"
    Then profile "test_profile" cannot be found
