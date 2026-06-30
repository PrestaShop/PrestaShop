@restore-all-tables-before-feature
Feature: Manage import runs
  As an admin
  I want to start, run and cancel import runs
  So that I can import CSV data through a resumable, server-side tracked run

  Scenario: Starting an import run leaves it pending until a batch runs
    When I start an import run "run1" for "categories" from file "dummy.csv"
    Then import run "run1" should have status "pending"

  Scenario: Cancelling a pending import run
    When I start an import run "run2" for "categories" from file "dummy.csv"
    And I cancel import run "run2"
    Then import run "run2" should have status "cancelled"

  Scenario: Cannot start an import run with an empty file name
    When I start an import run with an empty file name
    Then I should get an import error "PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException" with code 3

  Scenario: Cannot start an import run for an unsupported entity type
    When I start an import run for unsupported entity type 99
    Then I should get an import error "PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException" with code 2

  Scenario: Cannot start an import run with a negative batch size
    When I start an import run with a negative batch size
    Then I should get an import error "PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException" with code 8

  Scenario: Asking for an unknown import run fails
    When I ask for the state of a non-existent import run
    Then I should get an import error "PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunNotFoundException"
