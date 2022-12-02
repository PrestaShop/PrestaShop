# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_service --tags customer-management
@restore-all-tables-before-feature
@customer-management
Feature: Customer contact option

  Scenario: Update default contact options
    When I update contact options with following properties:
      | allowFileUploading | true            |
      | defaultMessage     | default message |
    Then contact options should have the following properties:
      | allowFileUploading | true            |
      | defaultMessage     | default message |

  Scenario: Update default contact options to different values
    When I update contact options with following properties:
      | allowFileUploading | false                   |
      | defaultMessage     | default message updated |
    Then contact options should have the following properties:
      | allowFileUploading | false                   |
      | defaultMessage     | default message updated |
