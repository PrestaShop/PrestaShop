# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-scripted-concurrency
@restore-all-tables-before-feature
@import-scripted-concurrency
Feature: Import job concurrency
  In order to keep one job consistent when two requests reach it at once
  As a BO user
  I should see the cancellation win, keep the rows it was too late to stop, and see a second batch refused

  # Twelve rows, four per call, and the cancel lands on row 7 — inside the second database batch,
  # so it is observed with four rows still unprocessed rather than at a convenient boundary.
  Scenario: A cancellation landing mid-batch keeps the rows that were already written
    When I start an import job "job1" for entity type "scripted" from file "scripted/database_cancel.csv" with following options:
      | batchLimit | 4 |
    And I continue the import job "job1"
    And I continue the import job "job1"
    And I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status         | running  |
      | currentPhaseId | database |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 12         | 12     |
      | database   | 12         | 0      |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status         | running  |
      | currentPhaseId | database |
    And import job "job1" should have the following phases:
      | id       | totalUnits | offset |
      | database | 12         | 4      |

    # this batch reaches the cancelling row: the slice finishes, then the sequencer probes the
    # database, finds a status it did not write, and stops there
    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | cancelled |
      | currentPhaseId  | database  |
      | progressPercent | 55        |
      | workingFile     | absent    |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 12         | 12     |
      | database   | 12         | 8      |

    # and it stays cancelled: nothing may pick it up again
    When I continue the import job "job1"
    Then I should get an error that the import job cannot be continued

  Scenario: A second batch of the same job is refused, and the job carries on afterwards
    When I start an import job "job1" for entity type "scripted" from file "scripted/database_reenter.csv" with following options:
      | batchLimit | 4 |
    And I continue the import job "job1"
    And I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status         | running  |
      | currentPhaseId | database |
    And import job "job1" should report no message

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status         | running  |
      | currentPhaseId | database |
      | noticeCount    | 1        |
    And import job "job1" should report the following messages:
      | severity | phase    | message                  | rows |
      | notice   | database | concurrent batch refused | 1    |
    And import job "job1" should have the following phases:
      | id       | totalUnits | offset |
      | database | 8          | 4      |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | finished |
      | progressPercent | 100      |
      | skippedRowCount | 0        |
    And import job "job1" should have the following phases:
      | id           | totalUnits | offset |
      | validation   | 8          | 8      |
      | database     | 8          | 8      |
      | finalization | 0          | 0      |
