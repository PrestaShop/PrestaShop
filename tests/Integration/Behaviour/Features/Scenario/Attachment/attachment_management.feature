# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attachment
@reset-database-before-feature
@clear-downloads-after-feature
Feature: Manage attachment from Back Office (BO)
  As an employee I want to be able to add, update and delete attachments

  Scenario: I add new attachment
    When I add new attachment "att1" with following properties:
      | description | en-US:puffin photo nr1 |
      | name        | en-US:puffin           |
      | file_name   | app_icon.png           |
    Then attachment "att1" should have following properties:
      | description | en-US:puffin photo nr1 |
      | name        | en-US:puffin           |
      | file_name   | app_icon.png           |
      | mime        | image/png              |
      | size        | 19187                  |
