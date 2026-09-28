# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-product-validation
@restore-all-tables-before-feature
@reset-all-tables-before-scenario
@reboot-kernel-before-scenario
@clear-cache-before-scenario
@import-product-validation
Feature: Import products from a file with invalid rows
  In order not to load a broken catalogue without noticing
  As a BO user
  I should be asked to confirm, and then get only the rows that passed

  Scenario: A file with invalid rows pauses for review, then imports only what is valid
    When I start an import job "job1" for entity type "product" from file "product_invalid_rows.csv"
    And I continue the import job "job1"
    # a real importer's validation phase pauses on its own messages, exactly as the scripted one
    # does, and the report says precisely which row failed on what
    Then import job "job1" should have the following properties:
      | status          | awaiting_confirmation |
      | currentPhaseId  | validation            |
      | dataRecordCount | 8                     |
      | skippedRowCount | 6                     |
      | errorCount      | 5                     |
      | warningCount    | 1                     |
      | noticeCount     | 1                     |
    And import job "job1" should report the following messages:
      | severity | phase      | message                       | rows |
      | error    | validation | Invalid visibility            | 1    |
      | error    | validation | Invalid GTIN                  | 2    |
      | error    | validation | The name is required          | 3    |
      | notice   | validation | The row is empty              | 4    |
      | error    | validation | Category with id 99999        | 5    |
      | warning  | validation | Unrecognized boolean "maybe"  | 6    |
      | error    | validation | Invalid ISBN                  | 7    |
    # nothing is written while the job waits for the merchant to decide
    And there should be no product with reference "INV-OK-1"

    # continuing IS the confirmation: the valid rows go in, the rejected ones stay out
    When I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status          | finished |
      | progressPercent | 100      |
      | skippedRowCount | 6        |
      | errorCount      | 5        |
    And there is a product "valid" with reference "INV-OK-1"
    # a warning is not a refusal: this row was imported, with the fallback value
    And there is a product "fuzzy" with reference "INV-FUZZY-BOOL"
    And there should be no product with reference "INV-BAD-VIS"
    And there should be no product with reference "INV-BAD-GTIN"
    And there should be no product with reference "INV-NO-NAME"
    And there should be no product with reference "INV-BAD-CAT"
    And there should be no product with reference "INV-BAD-ISBN"
