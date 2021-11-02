# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s language
@reset-database-before-feature
Feature: Language

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I add a new language with the following details:
      | name            | Français (French) |
      | isoCode         | fr                |
      | tagIETF         | fr                |
      | shortDateFormat | d/m/Y             |
      | fullDateFormat  | d/m/Y H:i:s       |
      | isRtl           | 0                 |
      | isActive        | 1                 |
      | shop            | shop1             |
    And the language with ISOCode "fr" should exist
    And I add a new language with the following details:
      | name            | English GB (English) |
      | isoCode         | gb                   |
      | tagIETF         | en-gb                |
      | shortDateFormat | Y-m-d                |
      | fullDateFormat  | Y-m-d H:i:s          |
      | isRtl           | 0                    |
      | isActive        | 1                    |
      | shop            | shop1                |
    And the language with ISOCode "gb" should exist

  Scenario: Add new language
    When I add a new language with the following details:
      | name            | Español AR (Spanish) |
      | isoCode         | ag                   |
      | tagIETF         | es-ar                |
      | shortDateFormat | Y-m-d                |
      | fullDateFormat  | Y-m-d H:i:s          |
      | isRtl           | 0                    |
      | isActive        | 1                    |
      | shop            | shop1                |
    Then I should get no error
    And the language with ISOCode "ag" should have the following details:
      | name            | Español AR (Spanish) |
      | isoCode         | ag                   |
      | tagIETF         | es-ar                |
      | shortDateFormat | Y-m-d                |
      | fullDateFormat  | Y-m-d H:i:s          |
      | isRtl           | 0                    |
      | isActive        | 1                    |
      | shop            | shop1                |
    ## Reset
    When I delete the language with ISOCode "ag"
    Then I should get no error

  Scenario: Edit language
    When I update the language with ISOCode "fr" with the following details:
      | name            | Language          |
      | tagIETF         | it                |
      | shortDateFormat | Y-m-d             |
      | fullDateFormat  | Y-m-d H:i:s       |
      | isRtl           | 0                 |
      | isActive        | 1                 |
      | shop            | shop1             |
    Then I should get no error
    And the language with ISOCode "fr" should have the following details:
      | name            | Language          |
      | isoCode         | fr                |
      | tagIETF         | it                |
      | shortDateFormat | Y-m-d             |
      | fullDateFormat  | Y-m-d H:i:s       |
      | isRtl           | 0                 |
      | isActive        | 1                 |
      | shop            | shop1             |

  Scenario: Delete language
    When I delete the language with ISOCode "fr"
    Then I should get no error
    And the language with ISOCode "fr" shouldn't exist

  Scenario: Delete a default language
    When language with iso code "fr" is the default one
    When I delete the language with ISOCode "fr"
    Then I should get an error that a default language can't be deleted
    And the language with ISOCode "fr" should exist
    ## Reset default language back to english
    When language with iso code "en" is the default one

  Scenario: Bulk Delete
    When I bulk delete languages with ISOCode "fr,gb"
    Then I should get no error
    And the language with ISOCode "fr" shouldn't exist
    And the language with ISOCode "gb" shouldn't exist

  Scenario: Bulk Delete (with a default one)
    When language with iso code "fr" is the default one
    And I bulk delete languages with ISOCode "fr,gb"
    Then I should get an error that a default language can't be deleted
    Then the language with ISOCode "fr" should exist
    And the language with ISOCode "gb" should exist
    And I bulk delete languages with ISOCode "gb,fr"
    Then I should get an error that a default language can't be deleted
    Then the language with ISOCode "fr" should exist
    And the language with ISOCode "gb" shouldn't exist

  Scenario: Toggle Status
    When I add a new language with the following details:
      | name            | Español AR (Spanish) |
      | isoCode         | ag                   |
      | tagIETF         | es-ar                |
      | shortDateFormat | Y-m-d                |
      | fullDateFormat  | Y-m-d H:i:s          |
      | isRtl           | 0                    |
      | isActive        | 1                    |
      | shop            | shop1                |
    Then I should get no error
    And the language with ISOCode "ag" should be enabled
    When I disable the language with ISOCode "ag"
    Then the language with ISOCode "ag" should be disabled
    When I enable the language with ISOCode "ag"
    And the language with ISOCode "ag" should be enabled

  Scenario: Toggle the status of the default language
    When language with iso code "ag" is the default one
    And I disable the language with ISOCode "ag"
    Then I should get an error that a default language can't be disabled
    And the language with ISOCode "ag" should be enabled
    ## Reset default language back to english
    When language with iso code "en" is the default one

  Scenario: Bulk Toggle Status
    Given the language with ISOCode "fr" should be enabled
    And the language with ISOCode "ag" should be enabled
    When I bulk disable languages with ISOCode "ag,fr"
    Then I should get no error
    And the language with ISOCode "fr" should be disabled
    And the language with ISOCode "ag" should be disabled
    When I bulk enable languages with ISOCode "ag,fr"
    Then I should get no error
    And the language with ISOCode "fr" should be enabled
    And the language with ISOCode "ag" should be enabled

  Scenario: Bulk Toggle Status (with a default one)
    When language with iso code "ag" is the default one
    When I bulk disable languages with ISOCode "ag,fr"
    Then I should get an error that a default language can't be disabled
    And the language with ISOCode "fr" should be enabled
    And the language with ISOCode "ag" should be enabled
    When I bulk disable languages with ISOCode "fr,ag"
    Then I should get an error that a default language can't be disabled
    And the language with ISOCode "fr" should be disabled
    And the language with ISOCode "ag" should be enabled
    ## Reset default language back to english
    When language with iso code "en" is the default one
