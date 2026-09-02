# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s country --tags bulk-delete-countries
@restore-all-tables-before-feature
@bulk-delete-countries
Feature: Bulk delete countries
  PrestaShop allows BO users to delete multiple countries at once
  As a BO user
  I must be able to delete multiple countries at once

  Scenario: Bulk delete countries
    Given language "language1" with locale "en-US" exists
    And I add new country "bulk_delete_country_1" with following properties:
      | name[en-US]                | Bulk Delete Country 1                            |
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
    And I add new country "bulk_delete_country_2" with following properties:
      | name[en-US]                | Bulk Delete Country 2                            |
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
    When I bulk delete countries "bulk_delete_country_1, bulk_delete_country_2"
    Then country "bulk_delete_country_1" should be deleted
    And country "bulk_delete_country_2" should be deleted

  Scenario: Bulk delete should report invalid country id
    Given country "invalid_country" has invalid id
    When I bulk delete countries "invalid_country"
    Then I should get error that country id is invalid

  Scenario: Bulk delete should no-op when country list is empty
    When I bulk delete an empty list of countries
    Then no exception should have been thrown

  Scenario: Bulk delete should continue on partial failure and aggregate errors
    Given language "language1" with locale "en-US" exists
    And I add new country "bulk_delete_partial_country" with following properties:
      | name[en-US]                | Bulk Delete Partial                              |
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
    And country "bulk_delete_partial_missing" does not exist
    When I bulk delete countries "bulk_delete_partial_country, bulk_delete_partial_missing"
    Then I should get a bulk country exception containing 1 errors
    And country "bulk_delete_partial_country" should be deleted
