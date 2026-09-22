# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-scripted-options
@restore-all-tables-before-feature
@import-scripted-options
Feature: Import job options
  In order to check a file before importing it, and to keep the file I uploaded
  As a BO user
  I should be able to run an import without writing, and to decide what happens to my file

  Scenario: A dry run stops after validation and never reaches the database phase
    When I start an import job "job1" for entity type "scripted" from file "scripted/validation_warning.csv" with following options:
      | dryRun | true |
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status          | finished |
      | progressPercent | 100      |
      | warningCount    | 1        |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 3          | 3      |
      | database   | 0          | 0      |

  Scenario: A dry run finishes instead of pausing, because nobody will confirm it
    When I start an import job "job1" for entity type "scripted" from file "scripted/validation_error.csv" with following options:
      | dryRun | true |
    And I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | finished |
      | errorCount      | 1        |
      | skippedRowCount | 1        |

  Scenario: The source file is deleted once it has been normalized
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv"
    Then import job "job1" should have the following properties:
      | status      | pending |
      | workingFile | present |
    And the source file of import job "job1" should not exist
    When I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status      | finished |
      | workingFile | absent   |
    And the source file of import job "job1" should not exist

  Scenario: The source file is kept when the caller asks for it, and outlives the job
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv" with following options:
      | keepSourceFile | true |
    Then the source file of import job "job1" should exist
    When I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status      | finished |
      | workingFile | absent   |
    # the working file goes with the job, the merchant's own upload does not
    And the source file of import job "job1" should exist
