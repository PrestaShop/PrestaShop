# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s language
@restore-languages-after-feature
Feature: Language

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I restore languages tables
    And I add a new language "french" with the following details:
      | name            | Français (French) |
      | isoCode         | fr                |
      | tagIETF         | fr                |
      | shortDateFormat | d/m/Y             |
      | fullDateFormat  | d/m/Y H:i:s       |
      | isRtl           | 0                 |
      | isActive        | 1                 |
      | shop            | shop1             |
    And the language "french" should exist
    And the language "french" should have the following details:
      | name            | Français (French) |
      | isoCode         | fr                |
      | tagIETF         | fr                |
      | locale          | fr-FR             |
      | shortDateFormat | d/m/Y             |
      | fullDateFormat  | d/m/Y H:i:s       |
      | isRtl           | 0                 |
      | isActive        | 1                 |
      | shop            | shop1             |
    And I add a new language "british" with the following details:
      | name            | English GB (English) |
      | isoCode         | gb                   |
      | tagIETF         | en-gb                |
      | shortDateFormat | Y-m-d                |
      | fullDateFormat  | Y-m-d H:i:s          |
      | isRtl           | 0                    |
      | isActive        | 1                    |
      | shop            | shop1                |
    And the language "british" should exist
    And the language "british" should have the following details:
      | name            | English GB (English) |
      | isoCode         | gb                   |
      | tagIETF         | en-gb                |
      | locale          | en-GB                |
      | shortDateFormat | Y-m-d                |
      | fullDateFormat  | Y-m-d H:i:s          |
      | isRtl           | 0                    |
      | isActive        | 1                    |
      | shop            | shop1                |
    And the robots.txt file has a rule where the directory "/en/app/" is allowed
    And the robots.txt file has a rule where the directory "/fr/app/" is allowed
    And the robots.txt file has a rule where the directory "/gb/app/" is allowed

  Scenario: Add new language
    When I add a new language "spanish" with the following details:
      | name            | Español AR (Spanish) |
      | isoCode         | ag                   |
      | tagIETF         | es-ar                |
      | shortDateFormat | Y-m-d                |
      | fullDateFormat  | Y-m-d H:i:s          |
      | isRtl           | 0                    |
      | isActive        | 1                    |
      | shop            | shop1                |
    And the robots.txt file has a rule where the directory "/ag/app/" is allowed
    And the language "spanish" should have the following details:
      | name            | Español AR (Spanish) |
      | isoCode         | ag                   |
      | tagIETF         | es-ar                |
      | locale          | es-AR                |
      | shortDateFormat | Y-m-d                |
      | fullDateFormat  | Y-m-d H:i:s          |
      | isRtl           | 0                    |
      | isActive        | 1                    |
      | shop            | shop1                |
    ## Reset
    When I delete the language "spanish"
    And the robots.txt file hasn't a rule where the directory "/ag/app/" is allowed

  Scenario: Edit language
    When I update the language "french" with the following details:
      | name            | Language    |
      | tagIETF         | it          |
      | shortDateFormat | Y-m-d       |
      | fullDateFormat  | Y-m-d H:i:s |
      | isRtl           | 0           |
      | isActive        | 1           |
      | shop            | shop1       |
    And the language "french" should have the following details:
      | name            | Language    |
      | isoCode         | fr          |
      | tagIETF         | it          |
      | locale          | fr-FR       |
      | shortDateFormat | Y-m-d       |
      | fullDateFormat  | Y-m-d H:i:s |
      | isRtl           | 0           |
      | isActive        | 1           |
      | shop            | shop1       |
    # When iso code is updated locale is automatically updated
    When I update the language "french" with the following details:
      | name    | Language |
      | isoCode | it       |
    And the language "french" should have the following details:
      | name            | Language    |
      | isoCode         | it          |
      | tagIETF         | it          |
      | locale          | it-IT       |
      | shortDateFormat | Y-m-d       |
      | fullDateFormat  | Y-m-d H:i:s |
      | isRtl           | 0           |
      | isActive        | 1           |
      | shop            | shop1       |

  Scenario: Delete language
    When I delete the language "french"
    And the language "french" shouldn't exist
    And the robots.txt file hasn't a rule where the directory "/fr/app/" is allowed

  Scenario: Delete a default language
    When language with iso code "fr" is the default one
    And I delete the language "french"
    Then I should get an error that a default language can't be deleted
    And the language "french" should exist
    And the robots.txt file has a rule where the directory "/fr/app/" is allowed

  Scenario: Bulk Delete
    When I bulk delete languages "french,british"
    And the language "french" shouldn't exist
    And the language "british" shouldn't exist
    And the robots.txt file hasn't a rule where the directory "/fr/app/" is allowed
    And the robots.txt file hasn't a rule where the directory "/gb/app/" is allowed

  Scenario: Bulk Delete (with a default one)
    When language with iso code "fr" is the default one
    And I bulk delete languages "french,british"
    Then I should get an error that a default language can't be deleted
    Then the language "french" should exist
    And the language "british" should exist
    And I bulk delete languages "british,french"
    Then I should get an error that a default language can't be deleted
    Then the language "french" should exist
    And the language "british" shouldn't exist

  Scenario: Toggle Status
    Given the language "french" should be enabled
    When I toggle the language "french" status to disabled
    Then the language "french" should be disabled
    And the robots.txt file hasn't a rule where the directory "/fr/app/" is allowed
    When I toggle the language "french" status to enabled
    And the language "french" should be enabled
    And the robots.txt file has a rule where the directory "/fr/app/" is allowed

  Scenario: Toggle the status of the default language
    Given language with iso code "fr" is the default one
    When I toggle the language "french" status to disabled
    Then I should get an error that a default language can't be disabled
    And the language "french" should be enabled

  Scenario: Bulk Toggle Status
    Given the language "french" should be enabled
    And the language "british" should be enabled
    When I bulk toggle languages "french,british" status to disabled
    And the language "french" should be disabled
    And the language "british" should be disabled
    And the robots.txt file hasn't a rule where the directory "/fr/app/" is allowed
    And the robots.txt file hasn't a rule where the directory "/gb/app/" is allowed
    When I bulk toggle languages "british,french" status to enabled
    And the language "french" should be enabled
    And the language "british" should be enabled
    And the robots.txt file has a rule where the directory "/fr/app/" is allowed
    And the robots.txt file has a rule where the directory "/gb/app/" is allowed

  Scenario: Bulk Toggle Status (with a default one)
    Given language with iso code "fr" is the default one
    When I bulk toggle languages "french,british" status to disabled
    Then I should get an error that a default language can't be disabled
    And the language "french" should be enabled
    And the language "british" should be enabled
    # Second case the gb is provided first so it is disabled before the exception is thrown for default language
    When I bulk toggle languages "british,french" status to disabled
    Then I should get an error that a default language can't be disabled
    And the language "french" should be enabled
    And the language "british" should be disabled

  Scenario: Adding a language with an ISO code already in database is forbidden
    # Adding new language with ISO code already existing is forbidden
    And I add a new language "french2" with the following details:
      | name            | Français (French) |
      | isoCode         | fr                |
      | tagIETF         | fr                |
      | shortDateFormat | d/m/Y             |
      | fullDateFormat  | d/m/Y H:i:s       |
      | isRtl           | 0                 |
      | isActive        | 1                 |
      | shop            | shop1             |
    Then I should get an error that a language with this ISO code already exists
    # Updating a language with ISO code already existing is also forbidden
    When I update the language "french" with the following details:
      | name    | Language |
      | isoCode | gb       |
    Then I should get an error that a language with this ISO code already exists
