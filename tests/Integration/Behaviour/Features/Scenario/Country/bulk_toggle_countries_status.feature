# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s country --tags bulk-toggle-countries-status
@restore-all-tables-before-feature
@bulk-toggle-countries-status
Feature: Bulk toggle countries status
  PrestaShop allows BO users to update multiple countries status at once
  As a BO user
  I must be able to update multiple countries status at once

  Scenario: Bulk enable and disable countries
    Given language "language1" with locale "en-US" exists
    And I add new country "bulk_country_1" with following properties:
      | name[en-US]                | Bulk Country 1                                   |
      | iso_code                   | QX                                               |
      | call_prefix                | 21                                               |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | NNNNN                                            |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | false                                            |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    And I add new country "bulk_country_2" with following properties:
      | name[en-US]                | Bulk Country 2                                   |
      | iso_code                   | QB                                               |
      | call_prefix                | 22                                               |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | NNNNN                                            |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | false                                            |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    When I bulk enable countries "bulk_country_1, bulk_country_2"
    Then country "bulk_country_1" should be enabled
    And country "bulk_country_2" should be enabled
    When I bulk disable countries "bulk_country_1, bulk_country_2"
    Then country "bulk_country_1" should be disabled
    And country "bulk_country_2" should be disabled

  Scenario: Bulk toggle should report invalid country id
    Given country "invalid_country" has invalid id
    When I bulk enable countries "invalid_country"
    Then I should get error that country id is invalid

  Scenario: Bulk toggle should no-op when country list is empty
    When I bulk enable an empty list of countries
    Then no exception should have been thrown

  Scenario: Bulk toggle should continue on partial failure and aggregate errors
    Given language "language1" with locale "en-US" exists
    And I add new country "bulk_partial_country" with following properties:
      | name[en-US]                | Bulk Partial                                     |
      | iso_code                   | QC                                               |
      | call_prefix                | 31                                               |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | NNNNN                                            |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | false                                            |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    And country "bulk_partial_missing" does not exist
    When I bulk enable countries "bulk_partial_country, bulk_partial_missing"
    Then I should get a bulk country exception containing 1 errors
    And country "bulk_partial_country" should be enabled
