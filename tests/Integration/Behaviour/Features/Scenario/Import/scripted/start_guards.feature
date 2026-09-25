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

  # inside the import directory, yet another job's: normalizing it would also delete it under
  # the job that reads it
  Scenario: Another job's working file is refused as a source
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv"
    And I start an import job "job2" for entity type "scripted" from the working file of import job "job1"
    Then I should get an error that the import cannot start because "the file is out of bounds"
    And import job "job1" should have the following properties:
      | status      | pending |
      | workingFile | present |

  # the roots are compared on real paths, so a link planted inside one does not open what it
  # points at
  Scenario: A link in the import directory to a file outside it is refused
    When I start an import job "job1" for entity type "scripted" from a link in the import directory to the unconfined file "scripted/clean.csv"
    Then I should get an error that the import cannot start because "the file is out of bounds"

  # only the files the back office lists are sources: the directory's own guards would otherwise be
  # imported, then deleted, leaving the uploads next to them downloadable
  Scenario: A dotfile of the import directory is refused
    When I start an import job "job1" for entity type "scripted" from a file planted in the import directory as ".htaccess"
    Then I should get an error that the import cannot start because "the file is out of bounds"

  Scenario: The index file of the import directory is refused
    When I start an import job "job1" for entity type "scripted" from a file planted in the import directory as "index.php"
    Then I should get an error that the import cannot start because "the file is out of bounds"

  Scenario: A file in a subdirectory of the import directory is refused
    When I start an import job "job1" for entity type "scripted" from a file planted in the import directory as "nested/products.csv"
    Then I should get an error that the import cannot start because "the file is out of bounds"

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

  # each scope gets a group of its own: the shop list resolver memoizes per group
  Scenario: A shop scope resolving to no shop is refused
    Given I add a shop group "emptyGroup" with name "Empty group"
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv" for shop group "emptyGroup"
    Then I should get an error that the import cannot start because "the shop scope is empty"

  Scenario: A shop scope wider than one shop is refused
    Given I add a shop group "twoShopGroup" with name "Two shop group"
    And I add a shop "shop2" with name "Second shop" and color "red" for the group "twoShopGroup"
    And I add a shop "shop3" with name "Third shop" and color "blue" for the group "twoShopGroup"
    When I start an import job "job1" for entity type "scripted" from file "scripted/clean.csv" for shop group "twoShopGroup"
    Then I should get an error that the import cannot start because "the shop scope is too wide"
