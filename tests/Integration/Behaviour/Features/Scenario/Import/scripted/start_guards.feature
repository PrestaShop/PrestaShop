# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-scripted-start-guards
@restore-all-tables-before-feature
@import-scripted-start-guards
Feature: Import job start guards
  In order to be told what is wrong before anything is imported
  As a BO user
  I should get one legible error when the file or the configuration cannot be used

  Scenario: A file outside the directories imports may read is refused
    When I start an import job "job1" for entity type "scripted" from the unconfined file "scripted/clean.csv"
    Then I should get an error that the import cannot start because "the file is out of bounds"

  Scenario: A file that does not exist is refused
    When I start an import job "job1" for entity type "scripted" from the unconfined file "scripted/no_such_file.csv"
    Then I should get an error that the import cannot start because "the file was not found"

  Scenario: A file holding only a header is refused
    When I start an import job "job1" for entity type "scripted" from file "scripted/header_only.csv"
    Then I should get an error that the import cannot start because "the file is empty"

  Scenario: An entity type no importer is registered for is refused
    When I start an import job "job1" for entity type "unregistered" from file "scripted/clean.csv"
    Then I should get an error that the import cannot start because "the entity type is unknown"

  Scenario: Truncating is refused for an entity the truncator does not know
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv" with following options:
      | truncate | true |
    Then I should get an error that the import cannot start because "truncating is not supported"

  Scenario: A language that is not installed is refused
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv" in language "zz"
    Then I should get an error that the import cannot start because "the language is not installed"
