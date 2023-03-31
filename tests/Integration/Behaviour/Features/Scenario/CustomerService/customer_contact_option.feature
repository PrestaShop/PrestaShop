# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_service --tags customer-management
@restore-all-tables-before-feature
@customer-management
Feature: Customer contact option

  Background:
    Given language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists

  Scenario: Update default contact options
    When I update contact options with following properties:
      | allowFileUploading    | true               |
      | defaultMessage[en-US] | default message    |
      | defaultMessage[fr-FR] | message par défaut |
    Then contact options should have the following properties:
      | allowFileUploading    | true               |
      | defaultMessage[en-US] | default message    |
      | defaultMessage[fr-FR] | message par défaut |

  Scenario: Update default contact options to different values
    When I update contact options with following properties:
      | allowFileUploading    | false              |
      | defaultMessage[en-US] | default message    |
      | defaultMessage[fr-FR] | message par défaut |
    Then contact options should have the following properties:
      | allowFileUploading    | false              |
      | defaultMessage[en-US] | default message    |
      | defaultMessage[fr-FR] | message par défaut |
