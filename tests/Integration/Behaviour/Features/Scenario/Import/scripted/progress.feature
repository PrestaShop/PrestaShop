# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-scripted-progress
@restore-all-tables-before-feature
@import-scripted-progress
Feature: Import job progress
  In order to show a merchant where their import is
  As a BO user
  I should see progress survive between requests, and each phase counted when it is entered

  # One import followed all the way, rather than three imports each restarted to check one thing:
  # the phase is counted on entry, the offset resumes across requests, and a caller may size its
  # own batch mid-flight.
  Scenario: Progress advances request after request, and a caller may resize the batch
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv" with following options:
      | batchLimit | 2 |
    Then import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 0          | 0      |
      | database   | 0          | 0      |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | running    |
      | currentPhaseId  | validation |
      | progressPercent | 13         |
    # entering the phase is what counts it: the total appears now, not when the job was created
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 5          | 2      |
      | database   | 0          | 0      |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | progressPercent | 26 |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 5          | 4      |

    # one unit instead of the job's two: the budget belongs to the caller, the configuration is
    # only the default
    When I continue the import job "job1" with a batch limit of 1
    Then import job "job1" should have the following properties:
      | status          | running  |
      | currentPhaseId  | database |
      | progressPercent | 33       |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 5          | 5      |
      | database   | 5          | 0      |

  Scenario: One call can finish a phase, skip an empty one and end the job
    When I start an import job "job1" for entity type "scripted" from file "scripted/single_row.csv" with following options:
      | batchLimit | 5 |
    And I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status          | finished |
      | currentPhaseId  | database |
      | progressPercent | 100      |
      | skippedRowCount | 0        |
    And import job "job1" should report no message
    # every phase accounted for: two ran to their total, the third counted nothing and was skipped
    And import job "job1" should have the following phases:
      | id           | totalUnits | offset | pausing |
      | validation   | 1          | 1      | true    |
      | database     | 1          | 1      | false   |
      | finalization | 0          | 0      | false   |

  Scenario: An option the core engine ignores still reaches the importer on every batch
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv" with following options:
      | batchLimit       | 2    |
      | scriptedFinalize | true |
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status          | finished     |
      | currentPhaseId  | finalization |
      | progressPercent | 100          |
    # the phase the option unlocks now counts units and runs, where it counted none before
    And import job "job1" should have the following phases:
      | id           | totalUnits | offset |
      | validation   | 5          | 5      |
      | database     | 5          | 5      |
      | finalization | 5          | 5      |
