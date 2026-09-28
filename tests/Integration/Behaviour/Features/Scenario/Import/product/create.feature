# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-product-create
@restore-all-tables-before-feature
@reset-all-tables-before-scenario
# the importer's resolvers memoize what they look up (QuietResolutionTrait), and those services
# outlive a scenario because Behat keeps one kernel: without a reboot, a manufacturer id cached
# by one scenario is reused by the next, where the table reset has already deleted it
@reboot-kernel-before-scenario
@clear-cache-before-scenario
@import-product-create
Feature: Import products
  In order to load a catalogue from a file
  As a BO user
  I should get real products, built by the same commands the back office uses

  # What is checked here is the SEAM: that a job started and continued through the command bus
  # really drives the product importer, and that every step of the row pipeline ran — not just the
  # first. Each assertion below lands on a different CQRS command, so a step that never fired
  # cannot hide behind the ones that did. What a row does to a product is covered field by field
  # by the engine's own integration tests.
  Scenario: A two-row file creates two products, exercising the whole row pipeline
    When I start an import job "job1" for entity type "product" from file "product_create_basic.csv"
    Then import job "job1" should have the following properties:
      | entityType      | product |
      | status          | pending |
      | dataRecordCount | 2       |
    When I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status          | finished |
      | progressPercent | 100      |
      | skippedRowCount | 0        |
      | errorCount      | 0        |

    Given there is a product "chair" with reference "IMP-CHAIR-001"
    # localized fields — AddProductCommand then UpdateProductCommand
    Then product "chair" localized "name" should be:
      | locale | value          |
      | en-US  | Imported Chair |
    And product "chair" localized "description" should be:
      | locale | value                  |
      | en-US  | Long chair description |
    # plain details — UpdateProductCommand
    And product "chair" should have following details:
      | product detail | value         |
      | reference      | IMP-CHAIR-001 |
      | ean13          | 1234567890128 |
      | isbn           |               |
      | upc            | 123456789012  |
      | mpn            | MPN-CHAIR1    |
    # prices — the price half of UpdateProductCommand
    And product "chair" should have following prices information:
      | price           | 99.90 |
      | wholesale_price | 40.00 |
    # stock — UpdateProductStockAvailableCommand
    And product "chair" should have following stock information:
      | quantity | 25   |
      | location | A-01 |
    # categories — SetAssociatedProductCategoriesCommand. The category did not exist before the
    # import: resolving it by name is what proves the row created it and linked it.
    And category "livingRoom" in default language named "Living Room" exists
    And product "chair" should be assigned to following categories:
      | id reference | name        | is default |
      | livingRoom   | Living Room | true       |
    # specific price — AddSpecificPriceCommand
    And product "chair" should have 1 specific prices

    Given there is a product "lamp" with reference "IMP-LAMP-002"
    Then product "lamp" localized "name" should be:
      | locale | value         |
      | en-US  | Imported Lamp |
    And product "lamp" should have following stock information:
      | quantity | 5 |

  # without matchRef a second import would create a second product per row, and the reference
  # would then match two — which is why this one asks for reference matching explicitly
  Scenario: Re-importing the same file with reference matching updates instead of duplicating
    When I start an import job "job1" for entity type "product" from file "product_create_basic.csv"
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status | finished |
    When I start an import job "job2" for entity type "product" from file "product_create_basic.csv" with following options:
      | matchRef | true |
    And I continue the import job "job2" until it stops
    Then import job "job2" should have the following properties:
      | status     | finished |
      | errorCount | 0        |
    # the reference still resolves to exactly one product: a second row would make this ambiguous
    And there is a product "chair" with reference "IMP-CHAIR-001"
