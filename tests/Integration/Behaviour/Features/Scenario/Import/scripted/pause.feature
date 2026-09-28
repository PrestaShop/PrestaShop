# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-scripted-pause
@restore-all-tables-before-feature
@import-scripted-pause
Feature: Import job pause
  In order to review what an import found before it writes anything
  As a BO user
  I should be asked to confirm when a pausing phase reports something

  Scenario: A warning during validation stops the job for review
    When I start an import job "job1" for entity type "scripted" from file "scripted/validation_warning.csv"
    And I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status         | awaiting_confirmation |
      | currentPhaseId | validation            |
      | warningCount   | 1                     |
    And import job "job1" should report the following messages:
      | severity | phase      | message           | rows | rowCount |
      | warning  | validation | price was rounded | 1    | 1        |

  Scenario: Continuing a paused job is how the pause is accepted
    When I start an import job "job1" for entity type "scripted" from file "scripted/validation_warning.csv"
    And I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status | awaiting_confirmation |
    When I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status       | finished |
      | warningCount | 1        |

  Scenario: A notice is information and does not stop anything
    When I start an import job "job1" for entity type "scripted" from file "scripted/validation_notice.csv"
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status      | finished |
      | noticeCount | 1        |
    And import job "job1" should report the following messages:
      | severity | phase      | message              |
      | notice   | validation | category was created |

  Scenario: A row validation rejected is not imported afterwards
    When I start an import job "job1" for entity type "scripted" from file "scripted/validation_error.csv"
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status          | finished |
      | skippedRowCount | 1        |
      | errorCount      | 1        |
    And import job "job1" should report the following messages:
      | severity | phase      | message              | rows |
      | error    | validation | reference is missing | 1    |
