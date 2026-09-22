# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-scripted-lifecycle
@restore-all-tables-before-feature
@import-scripted-lifecycle
Feature: Import job lifecycle
  In order to import data without holding a request open
  As a BO user
  I should be able to start a job, continue it batch after batch and cancel it

  Scenario: A clean file runs to completion
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv"
    Then import job "job1" should have the following properties:
      | entityType      | scripted |
      | status          | pending  |
      | dataRecordCount | 5        |
      | workingFile     | present  |
    When I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status          | finished |
      | progressPercent | 100      |
      | skippedRowCount | 0        |
      | errorCount      | 0        |
      | workingFile     | absent   |

  Scenario: A job is cancelled between two batches
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv" with following options:
      | batchLimit | 2 |
    And I continue the import job "job1"
    And I cancel the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | cancelled  |
      | currentPhaseId  | validation |
      | progressPercent | 13         |
      | workingFile     | absent     |
    # the units the job had already spent survive the cancellation, so the merchant can tell
    # what went in before they stopped it.
    # progress is 13% and not 40%: it is measured in PHASES, each weighing the same, so two units
    # of the first of three phases is (0 + 2/5) / 3. A phase's unit count does not exist until the
    # phase is entered, which is why the units of one phase never weigh against another's.
    And import job "job1" should have the following phases:
      | id           | totalUnits | offset |
      | validation   | 5          | 2      |
      | database     | 0          | 0      |
      | finalization | 0          | 0      |

  Scenario: A cancelled job cannot be continued
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv"
    And I cancel the import job "job1"
    And I continue the import job "job1"
    Then I should get an error that the import job cannot be continued

  Scenario: A finished job cannot be cancelled
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv"
    And I continue the import job "job1" until it stops
    And I cancel the import job "job1"
    Then I should get an error that the import job cannot be cancelled
