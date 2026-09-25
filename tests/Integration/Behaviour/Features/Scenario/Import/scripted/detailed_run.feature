# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-scripted-detailed-run
@restore-all-tables-before-feature
@import-scripted-detailed-run
Feature: Import job detailed run
  In order to trust every number the other features assert only in part
  As a BO user
  I should be able to follow one whole import batch by batch, checking everything at each step

  # The reference run: ten rows, a warning, an error and a notice in the middle, four units per
  # call. Every other feature checks a slice of this; here nothing is left unasserted, so a wrong
  # progress or a wrong phase total cannot hide behind a right status.
  Scenario: Every number of a ten-row import, batch after batch
    When I start an import job "job1" for entity type "scripted" from file "scripted/detailed.csv" with following options:
      | batchLimit | 4 |
    Then import job "job1" should have the following properties:
      | entityType      | scripted |
      | status          | pending  |
      | currentPhaseId  |          |
      | dataRecordCount | 10       |
      | skippedRowCount | 0        |
      | progressPercent | 0        |
      | errorCount      | 0        |
      | warningCount    | 0        |
      | noticeCount     | 0        |
      | workingFile     | present  |
    And import job "job1" should report no message
    And import job "job1" should have the following phases:
      | id           | totalUnits | offset | pausing |
      | validation   | 0          | 0      | true    |
      | database     | 0          | 0      | false   |
      | finalization | 0          | 0      | false   |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | running    |
      | currentPhaseId  | validation |
      | skippedRowCount | 0          |
      | progressPercent | 13         |
      | errorCount      | 0          |
      | warningCount    | 0          |
      | noticeCount     | 0          |
    And import job "job1" should report no message
    And import job "job1" should have the following phases:
      | id           | totalUnits | offset |
      | validation   | 10         | 4      |
      | database     | 0          | 0      |
      | finalization | 0          | 0      |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | running    |
      | currentPhaseId  | validation |
      | skippedRowCount | 1          |
      | progressPercent | 26         |
      | errorCount      | 1          |
      | warningCount    | 1          |
      | noticeCount     | 1          |
    And import job "job1" should report the following messages:
      | severity | phase      | message              | rows | rowCount |
      | warning  | validation | price was rounded    | 4    | 1        |
      | error    | validation | reference is missing | 5    | 1        |
      | notice   | validation | category was created | 6    | 1        |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 10         | 8      |
      | database   | 0          | 0      |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | awaiting_confirmation |
      | currentPhaseId  | validation            |
      | skippedRowCount | 1                     |
      | progressPercent | 33                    |
      | errorCount      | 1                     |
      | warningCount    | 1                     |
      | noticeCount     | 1                     |
      | workingFile     | present               |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 10         | 10     |
      | database   | 0          | 0      |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | running  |
      | currentPhaseId  | database |
      | skippedRowCount | 1        |
      | progressPercent | 46       |
    And import job "job1" should have the following phases:
      | id           | totalUnits | offset |
      | validation   | 10         | 10     |
      | database     | 10         | 4      |
      | finalization | 0          | 0      |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | running  |
      | currentPhaseId  | database |
      | progressPercent | 60       |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 10         | 10     |
      | database   | 10         | 8      |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | finished |
      | currentPhaseId  | database |
      | dataRecordCount | 10       |
      | skippedRowCount | 1        |
      | progressPercent | 100      |
      | errorCount      | 1        |
      | warningCount    | 1        |
      | noticeCount     | 1        |
      | workingFile     | absent   |
    And import job "job1" should have the following phases:
      | id           | totalUnits | offset |
      | validation   | 10         | 10     |
      | database     | 10         | 10     |
      | finalization | 0          | 0      |
    And import job "job1" should report the following messages:
      | severity | phase      | message              | rows | rowCount |
      | warning  | validation | price was rounded    | 4    | 1        |
      | error    | validation | reference is missing | 5    | 1        |
      | notice   | validation | category was created | 6    | 1        |
    And the source file of import job "job1" should not exist
