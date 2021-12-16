# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s misc --tags theme_mail_templates
@restore-all-tables-before-feature
@theme_mail_templates
Feature: Theme mail templates
  In order to use customized email templates in the Back Office (BO)
  As a BO user
  I need to be able to generate theme mail templates with the chosen settings

  Scenario: generate theme mail templates in single shop context
    Given single shop context is loaded
    When I generate emails with the following details:
      | Email theme | classic           |
      | Language    | English (English) |
    Then mails folder with sub folder "en" exists

  Scenario: generate theme mail templates in multiple shop context
    Given multiple shop context is loaded
    When I generate emails with the following details:
      | Email theme | classic           |
      | Language    | English (English) |
    Then mails folder with sub folder "en" exists
