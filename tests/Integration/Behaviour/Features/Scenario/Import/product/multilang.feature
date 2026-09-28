# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-product-multilang
@restore-all-tables-before-feature
@reset-all-tables-before-scenario
@reboot-kernel-before-scenario
@clear-cache-before-scenario
@import-product-multilang
Feature: Import products in several languages
  In order to translate a catalogue one file at a time
  As a BO user
  I should be able to import the same products again in another language

  # A file carries ONE language, named when the job starts. Creating a product fills every
  # language with what the file holds, because a product with an empty name in some language is
  # unusable; importing again in another language then overwrites only that language.
  Scenario: A second import in another language translates the products it matches
    Given language "french" with locale "fr-FR" exists
    When I start an import job "job1" for entity type "product" from file "product/multilang_en.csv"
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status     | finished |
      | errorCount | 0        |

    Given there is a product "chair" with reference "ML-BEHAT-1"
    # creation duplicated the English file into every installed language
    Then product "chair" localized "name" should be:
      | locale | value             |
      | en-US  | Imported Chair EN |
      | fr-FR  | Imported Chair EN |

    When I start an import job "job2" for entity type "product" from file "product/multilang_fr.csv" in language "fr" with following options:
      | matchRef | true |
    And I continue the import job "job2" until it stops
    Then import job "job2" should have the following properties:
      | status     | finished |
      | errorCount | 0        |

    # only the language the file was imported in changed
    And product "chair" localized "name" should be:
      | locale | value              |
      | en-US  | Imported Chair EN  |
      | fr-FR  | Chaise importee FR |
    And product "chair" localized "description" should be:
      | locale | value                 |
      | en-US  | English description   |
      | fr-FR  | Description francaise |
