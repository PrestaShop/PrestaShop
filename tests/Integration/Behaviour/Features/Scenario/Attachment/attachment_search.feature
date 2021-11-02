# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attachment --tags search-attachment
@reset-database-before-feature
@clear-cache-after-feature
@reset-downloads-after-feature
@attachment
@search-attachment
Feature: Manage attachment from Back Office (BO)
  As an employee I want to be able to search attachments

  Background:
    Given language "languageEn" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "languageFr" with locale "fr-FR" exists
    When I add new attachment "att1" with following properties:
      | description[en-US] | puffin photo nr1  |
      | description[fr-FR] | photo de macareux |
      | name[en-US]        | puffin            |
      | name[fr-FR]        | macareux          |
      | file_name          | app_icon.png      |
    And I add new attachment "att2" with following properties:
      | description[en-US] | chainsaw user guide |
      | description[fr-FR] | notice tronçonneuse |
      | name[en-US]        | user guide          |
      | name[fr-FR]        | notice              |
      | file_name          | dummy_zip.zip       |
    Then attachment "att1" should have following properties:
      | description[en-US] | puffin photo nr1  |
      | description[fr-FR] | photo de macareux |
      | name[en-US]        | puffin            |
      | name[fr-FR]        | macareux          |
      | file_name          | app_icon.png      |
      | mime               | image/png           |
      | size               | 19187               |
    And attachment "att2" should have following properties:
      | description[en-US] | chainsaw user guide |
      | description[fr-FR] | notice tronçonneuse |
      | name[en-US]        | user guide          |
      | name[fr-FR]        | notice              |
      | file_name          | dummy_zip.zip       |
      | mime               | application/zip     |
      | size               | 22405               |

    Scenario: I can search attachments file based on a language
      When I search for attachment matching "puffin" I get following results:
        | attachment_id | file_name    | name[en-US] | name[fr-FR] | description[en-US] | description[fr-FR] | mime      | size  |
        | att1          | app_icon.png | puffin      | macareux    | puffin photo nr1   | photo de macareux  | image/png | 19187 |
      # Case insensitive
      And I search for attachment matching "PuFfin" I get following results:
        | attachment_id | file_name    | name[en-US] | name[fr-FR] | description[en-US] | description[fr-FR] | mime      | size  |
        | att1          | app_icon.png | puffin      | macareux    | puffin photo nr1   | photo de macareux  | image/png | 19187 |
      # Search in description as well
      And I search for attachment matching "photo" I get following results:
        | attachment_id | file_name    | name[en-US] | name[fr-FR] | description[en-US] | description[fr-FR] | mime      | size  |
        | att1          | app_icon.png | puffin      | macareux    | puffin photo nr1   | photo de macareux  | image/png | 19187 |
      # Search in file_name as well
      And I search for attachment matching "dummy" I get following results:
        | attachment_id | file_name     | name[en-US] | name[fr-FR] | description[en-US]  | description[fr-FR]  | mime            | size  |
        | att2          | dummy_zip.zip | user guide  | notice      | chainsaw user guide | notice tronçonneuse | application/zip | 22405 |
      And I search for attachment matching "notice" I get following results:
        | attachment_id | file_name     | name[en-US] | name[fr-FR] | description[en-US]  | description[fr-FR]  | mime            | size  |
        | att2          | dummy_zip.zip | user guide  | notice      | chainsaw user guide | notice tronçonneuse | application/zip | 22405 |
      And I search for attachment matching "tRoN" I get following results:
        | attachment_id | file_name     | name[en-US] | name[fr-FR] | description[en-US]  | description[fr-FR]  | mime            | size  |
        | att2          | dummy_zip.zip | user guide  | notice      | chainsaw user guide | notice tronçonneuse | application/zip | 22405 |
      When I search for attachment matching "toto" I get no results
      And I search for attachment matching "ot" I get following results:
        | attachment_id | file_name    | name[en-US] | name[fr-FR] | description[en-US] | description[fr-FR] | mime      | size  |
        | att1          | app_icon.png | puffin      | macareux    | puffin photo nr1   | photo de macareux  | image/png | 19187 |
        | att2          | dummy_zip.zip | user guide  | notice      | chainsaw user guide | notice tronçonneuse | application/zip | 22405 |
      And I delete attachment att2
      Then I search for attachment matching "ot" I get following results:
        | attachment_id | file_name    | name[en-US] | name[fr-FR] | description[en-US] | description[fr-FR] | mime      | size  |
        | att1          | app_icon.png | puffin      | macareux    | puffin photo nr1   | photo de macareux  | image/png | 19187 |
