# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attachment --tags add-attachment
@reset-database-before-feature
@add-attachment
Feature: Add attachments from Back Office (BO).
  As an employee I want to be able to add new attachments.

  Scenario: I add new attachment
    Given file "app_icon.png" exists
    When I add new attachment app_icon.png
