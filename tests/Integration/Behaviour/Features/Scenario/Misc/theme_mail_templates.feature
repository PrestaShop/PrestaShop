# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s misc
@reset-database-before-feature
Feature: Theme mail templates
  In order to use customized email templates in the Back Office (BO)
  As a BO user
  I need to be able to generate theme mail templates with the chosen settings

  Scenario: generate theme mail templates in single shop context
    Given single shop context is loaded
    When I generate emails with the following properties:
      | theme_name | language | overwrite_templates  | core_mails_folder | modules_mail_folder |
      | classic    | en       |                      |                   |                     |
    Then mails folder with sub folder "en" exists

  Scenario: generate theme mail templates in multiple shop context
    Given multiple shop context is loaded
    When I generate emails with the following properties:
      | theme_name | language | overwrite_templates  | core_mails_folder | modules_mail_folder |
      | classic    | en       |                      |                   |                     |
    Then mails folder with sub folder "en" exists
