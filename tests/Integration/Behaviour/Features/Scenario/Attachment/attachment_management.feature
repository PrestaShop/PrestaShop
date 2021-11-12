# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attachment --tags attachment-management
@reset-database-before-feature
@clear-cache-after-feature
@reset-downloads-after-feature
@attachment
@attachment-management
Feature: Manage attachment from Back Office (BO)
  As an employee I want to be able to add, update and delete attachments

  Scenario: I add new attachment
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    When I add new attachment "att1" with following properties:
      | description[en-US] | puffin photo nr1  |
      | description[fr-FR] | photo de macareux |
      | name[en-US]        | puffin            |
      | name[fr-FR]        | macareux          |
      | file_name          | app_icon.png      |
    Then attachment "att1" should have following properties:
      | description[en-US] | puffin photo nr1  |
      | description[fr-FR] | photo de macareux |
      | name[en-US]        | puffin            |
      | name[fr-FR]        | macareux          |
      | file_name          | app_icon.png      |
      | mime               | image/png         |
      | size               | 19187             |

  Scenario: I add new attachment providing name and description only in default language
    When I add new attachment "att2" with following properties:
      | description[en-US] | puffin photo nr1 |
      | name[en-US]        | puffin           |
      | file_name          | app_icon.png     |
    Then attachment "att2" should have following properties:
      | description[en-US] | puffin photo nr1                        |
      | description[fr-FR] |                                         |
      | name[en-US]        | puffin                                  |
      | name[fr-FR]        | puffin                                  |
      | file_name          | app_icon.png                            |
      | mime               | image/png                               |
      | size               | 19187                                   |
