# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attachment
@reset-database-before-feature
Feature: Manage attachment from Back Office (BO)
  As an employee I want to be able to add, update and delete attachments

  Scenario: I add new attachment
    When I add new attachment "att1" with following properties:
      | description      | puffin photo nr1 |
      | name             | puffin           |
      | file_name        | puffin.jpg       |
      | mime             | image/jpeg       |
