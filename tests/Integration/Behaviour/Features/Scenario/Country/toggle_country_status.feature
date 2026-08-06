# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s country --tags toggle-country-status
@restore-all-tables-before-feature
@toggle-country-status
Feature: Toggle country status
  PrestaShop allows BO users to toggle country status
  As a BO user
  I must be able to toggle country status

  Scenario: Toggle one country status
    Given language "language1" with locale "en-US" exists
    When I add new country "toggle_country" with following properties:
      | name[en-US]                | Toggle Country                                   |
      | iso_code                   | QW                                               |
      | call_prefix                | 11                                               |
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
    And I toggle country status "toggle_country"
    Then country "toggle_country" should be disabled

  Scenario: Toggle one country status should fail when country does not exist
    Given country "missing_country" does not exist
    When I toggle country status "missing_country"
    Then I should get error that country was not found

  Scenario: Toggle country status should fail when country id is invalid
    Given country "invalid_country" has invalid id
    When I toggle country status "invalid_country"
    Then I should get error that country id is invalid

  Scenario: Toggle country status twice returns to original state
    Given language "language1" with locale "en-US" exists
    And I add new country "roundtrip_country" with following properties:
      | name[en-US]                | Roundtrip Country                                |
      | iso_code                   | QV                                               |
      | call_prefix                | 71                                               |
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
    When I toggle country status "roundtrip_country"
    Then country "roundtrip_country" should be disabled
    When I toggle country status "roundtrip_country"
    Then country "roundtrip_country" should be enabled
