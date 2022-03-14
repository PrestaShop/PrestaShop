# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s language
@restore-languages-after-feature
Feature: Language

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I restore languages tables
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
    And the robots.txt file has a rule where the directory "/en/app/" is allowed
    And the robots.txt file has a rule where the directory "/fr/app/" is allowed
    And the robots.txt file has a rule where the directory "/gb/app/" is allowed

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
    And the robots.txt file has a rule where the directory "/ag/app/" is allowed
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
    And the robots.txt file hasn't a rule where the directory "/ag/app/" is allowed

  Scenario: Edit language
    When I update the language with ISOCode "fr" with the following details:
      | name            | Language          |
      | tagIETF         | it                |
      | shortDateFormat | Y-m-d             |
      | fullDateFormat  | Y-m-d H:i:s       |
      | isRtl           | 0                 |
      | isActive        | 1                 |
      | shop            | shop1             |
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
    And the language with ISOCode "fr" shouldn't exist
    And the robots.txt file hasn't a rule where the directory "/fr/app/" is allowed

  Scenario: Delete a default language
    When language with iso code "fr" is the default one
    And I delete the language with ISOCode "fr"
    Then I should get an error that a default language can't be deleted
    And the language with ISOCode "fr" should exist
    And the robots.txt file has a rule where the directory "/fr/app/" is allowed

  Scenario: Bulk Delete
    When I bulk delete languages with ISOCode "fr,gb"
    And the language with ISOCode "fr" shouldn't exist
    And the language with ISOCode "gb" shouldn't exist
    And the robots.txt file hasn't a rule where the directory "/fr/app/" is allowed
    And the robots.txt file hasn't a rule where the directory "/gb/app/" is allowed

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
    Given the language with ISOCode "fr" should be enabled
    When I disable the language with ISOCode "fr"
    Then the language with ISOCode "fr" should be disabled
    And the robots.txt file hasn't a rule where the directory "/fr/app/" is allowed
    When I enable the language with ISOCode "fr"
    And the language with ISOCode "fr" should be enabled
    And the robots.txt file has a rule where the directory "/fr/app/" is allowed

  Scenario: Toggle the status of the default language
    Given language with iso code "fr" is the default one
    And I disable the language with ISOCode "fr"
    Then I should get an error that a default language can't be disabled
    And the language with ISOCode "fr" should be enabled

  Scenario: Bulk Toggle Status
    Given the language with ISOCode "fr" should be enabled
    And the language with ISOCode "gb" should be enabled
    When I bulk disable languages with ISOCode "gb,fr"
    And the language with ISOCode "fr" should be disabled
    And the language with ISOCode "gb" should be disabled
    And the robots.txt file hasn't a rule where the directory "/fr/app/" is allowed
    And the robots.txt file hasn't a rule where the directory "/gb/app/" is allowed
    When I bulk enable languages with ISOCode "gb,fr"
    And the language with ISOCode "fr" should be enabled
    And the language with ISOCode "gb" should be enabled
    And the robots.txt file has a rule where the directory "/fr/app/" is allowed
    And the robots.txt file has a rule where the directory "/gb/app/" is allowed

  Scenario: Bulk Toggle Status (with a default one)
    Given language with iso code "fr" is the default one
    When I bulk disable languages with ISOCode "fr,gb"
    Then I should get an error that a default language can't be disabled
    And the language with ISOCode "fr" should be enabled
    And the language with ISOCode "gb" should be enabled
    # Second case the gb is provided first so it is disabled before the exception is thrown for default language
    When I bulk disable languages with ISOCode "gb,fr"
    Then I should get an error that a default language can't be disabled
    And the language with ISOCode "fr" should be enabled
    And the language with ISOCode "gb" should be disabled
