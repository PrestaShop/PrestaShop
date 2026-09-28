# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-product-auto-creation
@restore-all-tables-before-feature
@reset-all-tables-before-scenario
@reboot-kernel-before-scenario
@clear-cache-before-scenario
@import-product-auto-creation
Feature: Import products naming entities that do not exist yet
  In order to load a catalogue without preparing its manufacturers and categories first
  As a BO user
  I should get those created along the way, and created once however many rows name them

  # Both rows name the SAME new manufacturer, the same new category path and the same new feature.
  # That the job ends without a single error is what proves each was created once: a second
  # manufacturer of the same name would make the next row ambiguous and the importer would refuse
  # it rather than guess.
  Scenario: The entities a row refers to are created once, whatever the number of rows naming them
    When I start an import job "job1" for entity type "product" from file "product_auto_creation.csv"
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status          | finished |
      | dataRecordCount | 2        |
      | skippedRowCount | 0        |
      | errorCount      | 0        |
      | warningCount    | 0        |

    And there is a product "auto1" with reference "AUTO-1"
    And there is a product "auto2" with reference "AUTO-2"
    # each entity the rows named now exists, resolved by the name the file used
    And manufacturer "brand" named "Brand To Create" exists
    And category "parent" in default language named "Parent To Create" exists
    And category "child" in default language named "Child To Create" exists
    And product "auto1" should be assigned to following categories:
      | id reference | name            | is default |
      | child        | Child To Create | true       |
