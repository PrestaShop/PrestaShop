# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-scripted-failure
@restore-all-tables-before-feature
@import-scripted-failure
Feature: Import job failure
  In order to understand why an import stopped
  As a BO user
  I should get a failed job carrying a message I can read

  Scenario: An importer throwing stops the job and explains itself
    When I start an import job "job1" for entity type "scripted" from file "scripted/database_throw.csv"
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status      | failed |
      | workingFile | absent |
    And import job "job1" should report the following messages:
      | severity | phase    | message          |
      | error    | database | Scripted failure |

  Scenario: A failed job cannot be continued
    When I start an import job "job1" for entity type "scripted" from file "scripted/database_throw.csv"
    And I continue the import job "job1" until it stops
    And I continue the import job "job1"
    Then I should get an error that the import job cannot be continued
