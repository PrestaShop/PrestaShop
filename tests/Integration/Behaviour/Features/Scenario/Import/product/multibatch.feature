# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-product-multibatch
@restore-all-tables-before-feature
@reset-all-tables-before-scenario
@reboot-kernel-before-scenario
@clear-cache-before-scenario
@import-product-multibatch
Feature: Import products across several batches
  In order to import a catalogue too large for one request
  As a BO user
  I should get every row, whatever the batch size, and progress that survives between requests

  # Five rows, two units per call: the job crosses several requests and a phase boundary before
  # it finishes, which is the state a real catalogue import spends most of its life in.
  Scenario: A five-row file imported two units at a time produces every product
    When I start an import job "job1" for entity type "product" from file "product_resume.csv" with following options:
      | batchLimit | 2 |
    Then import job "job1" should have the following properties:
      | status          | pending |
      | dataRecordCount | 5       |

    When I continue the import job "job1"
    Then import job "job1" should have the following properties:
      | status         | running    |
      | currentPhaseId | validation |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 5          | 2      |
    # nothing is written before the database phase, however many validation batches have run
    And there should be no product with reference "RES-1"

    When I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status          | finished |
      | progressPercent | 100      |
      | skippedRowCount | 0        |
      | errorCount      | 0        |
    And import job "job1" should have the following phases:
      | id         | totalUnits | offset |
      | validation | 5          | 5      |
      | database   | 5          | 5      |
    And there is a product "res1" with reference "RES-1"
    And there is a product "res5" with reference "RES-5"
    And product "res1" should have following prices information:
      | price | 10.00 |
    And product "res5" should have following prices information:
      | price | 50.00 |
