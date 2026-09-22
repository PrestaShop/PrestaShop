# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-scripted-purge
@restore-all-tables-before-feature
@import-scripted-purge
Feature: Import job purge
  In order to keep the import table and its working directory from growing forever
  As a BO user
  I should be able to collect finished jobs and the files nothing owns any more

  Scenario: A finished job older than the retention window is collected
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv"
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status | finished |
    Given the import job "job1" was last updated 8 days ago
    When I purge import jobs I should get the following result:
      | purgedJobCount          | 1 |
      | removedWorkingFileCount | 0 |
    Then the import job "job1" should no longer exist

  Scenario: A job that is still running is never collected, however old the row is
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv" with following options:
      | batchLimit | 2 |
    And I continue the import job "job1"
    Given the import job "job1" was last updated 8 days ago
    When I purge import jobs I should get the following result:
      | purgedJobCount          | 0 |
      | removedWorkingFileCount | 0 |
    Then import job "job1" should have the following properties:
      | status | running |

  Scenario: Nothing is collected inside the retention window
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv"
    And I continue the import job "job1" until it stops
    And I purge import jobs I should get the following result:
      | purgedJobCount          | 0 |
      | removedWorkingFileCount | 0 |

  Scenario: A working file no job owns is swept
    Given an orphan import working file was left 8 days ago
    When I purge import jobs I should get the following result:
      | purgedJobCount          | 0 |
      | removedWorkingFileCount | 1 |

  Scenario: A recent orphan is left alone, because a job may be starting right now
    Given an orphan import working file was left 1 days ago
    When I purge import jobs I should get the following result:
      | purgedJobCount          | 0 |
      | removedWorkingFileCount | 0 |

  Scenario: An expiration date in the future is refused
    When I purge import jobs expired before "+1 hour"
    Then I should get an error that the import job expiration date is invalid
