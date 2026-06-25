# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s country --tags bulk-update-country-zone
@restore-all-tables-before-feature
@bulk-update-country-zone
Feature: Bulk update countries zone
  PrestaShop allows BO users to update zone for multiple countries at once
  As a BO user
  I must be able to update zone for multiple countries at once

  Scenario: Bulk update countries zone
    Given language "language1" with locale "en-US" exists
    And I add new country "zone_country_1" with following properties:
      | name[en-US]                | Zone Country 1                                   |
      | iso_code                   | QD                                               |
      | call_prefix                | 41                                               |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | NNNNN                                            |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | true                                             |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    And I add new country "zone_country_2" with following properties:
      | name[en-US]                | Zone Country 2                                   |
      | iso_code                   | QE                                               |
      | call_prefix                | 42                                               |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | NNNNN                                            |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | true                                             |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    When I bulk update countries "zone_country_1, zone_country_2" to zone 2
    Then country "zone_country_1" should be assigned to zone 2
    And country "zone_country_2" should be assigned to zone 2

  Scenario: Bulk update countries zone should fail when country id is invalid
    Given country "invalid_zone_country" has invalid id
    When I bulk update countries "invalid_zone_country" to zone 2
    Then I should get error that country id is invalid

  Scenario: Bulk update countries zone should fail when country list is empty
    When I bulk update an empty list of countries to zone 2
    Then I should get error that country list is empty

  Scenario: Bulk update countries zone should fail when zone does not exist
    Given language "language1" with locale "en-US" exists
    And I add new country "zone_missing_country" with following properties:
      | name[en-US]                | Zone Missing                                     |
      | iso_code                   | QF                                               |
      | call_prefix                | 51                                               |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | NNNNN                                            |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | true                                             |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    When I bulk update countries "zone_missing_country" to zone 999999
    Then I should get error that zone was not found

  Scenario: Bulk update countries zone should continue on partial failure and aggregate errors
    Given language "language1" with locale "en-US" exists
    And I add new country "zone_partial_country" with following properties:
      | name[en-US]                | Zone Partial                                     |
      | iso_code                   | QG                                               |
      | call_prefix                | 61                                               |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | NNNNN                                            |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | true                                             |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    And country "zone_partial_missing" does not exist
    When I bulk update countries "zone_partial_country, zone_partial_missing" to zone 2
    Then I should get a bulk country exception containing 1 errors
    And country "zone_partial_country" should be assigned to zone 2
