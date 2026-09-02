#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s country --tags country-management
@restore-all-tables-before-feature
@country-management
Feature: country management
  As an employee
  I must be able to add, edit and delete country

  Scenario: Adding new country
    Given language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists
    When I add new country "test" with following properties:
      | name[en-US]                | testName                                         |
      | name[fr-FR]                | testNameFr                                       |
      | iso_code                   | TE                                               |
      | call_prefix                | 123                                              |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | 1 NL                                             |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | true                                             |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    Then the country "test" should have the following properties:
      | name[en-US]                | testName                                         |
      | name[fr-FR]                | testNameFr                                       |
      | iso_code                   | TE                                               |
      | call_prefix                | 123                                              |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | 1 NL                                             |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | true                                             |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |

  Scenario: Adding a country with an already existing ISO code is rejected
    When I add new country "duplicate" with following properties:
      | name[en-US]                | duplicateName                                    |
      | name[fr-FR]                | duplicateNameFr                                  |
      | iso_code                   | TE                                               |
      | call_prefix                | 123                                              |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | 1 NL                                             |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | true                                             |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    Then I should get an "DuplicateCountryIsoCode" error

  Scenario: Editing a country to use an already existing ISO code is rejected
    When I add new country "second" with following properties:
      | name[en-US]                | secondName                                       |
      | name[fr-FR]                | secondNameFr                                     |
      | iso_code                   | TS                                               |
      | call_prefix                | 123                                              |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | 1 NL                                             |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | true                                             |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    And I edit country "second" with following properties:
      | iso_code | TE |
    Then I should get an "DuplicateCountryIsoCode" error

  Scenario: edit country
    Given language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists
    When I edit country "test" with following properties:
      | name[en-US]                | editName                                                           |
      | name[fr-FR]                | editNameFr                                                         |
      | iso_code                   | TA                                                                 |
      | call_prefix                | 1234                                                               |
      | default_currency           | 2                                                                  |
      | zone                       | 2                                                                  |
      | need_zip_code              | false                                                              |
      | zip_code_format            | 1 NLL                                                              |
      | address_format             | firstname lastname\ncompany\naddress1\npostcode city\nCountry:name |
      | is_enabled                 | false                                                              |
      | contains_states            | true                                                               |
      | need_identification_number | true                                                               |
      | display_tax_label          | false                                                              |
      | shop_association           | 1                                                                  |
    Then the country "test" should have the following properties:
      | name[en-US]                | editName                                                           |
      | name[fr-FR]                | editNameFr                                                         |
      | iso_code                   | TA                                                                 |
      | call_prefix                | 1234                                                               |
      | default_currency           | 2                                                                  |
      | zone                       | 2                                                                  |
      | need_zip_code              | false                                                              |
      | zip_code_format            | 1 NLL                                                              |
      | address_format             | firstname lastname\ncompany\naddress1\npostcode city\nCountry:name |
      | is_enabled                 | false                                                              |
      | contains_states            | true                                                               |
      | need_identification_number | true                                                               |
      | display_tax_label          | false                                                              |
      | shop_association           | 1                                                                  |

  Scenario: editing a country with an invalid address format is rejected
    When I edit country "test" with following properties:
      | address_format | only_garbage_here |
    Then I should get an "InvalidAddressFormat" error

  Scenario: editing a country with an address format missing a required field is rejected
    When I edit country "test" with following properties:
      | address_format | lastname\naddress1\ncity\nCountry:name |
    Then I should get an "InvalidAddressFormat" error

  Scenario: editing a country with a negative call prefix is rejected
    When I edit country "test" with following properties:
      | call_prefix | -5 |
    Then I should get error that call prefix is invalid

  Scenario: adding a country with a negative call prefix is rejected
    Given language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists
    When I add new country "invalidPrefix" with following properties:
      | name[en-US]                | invalidPrefixName                                |
      | name[fr-FR]                | invalidPrefixNameFr                              |
      | iso_code                   | TI                                               |
      | call_prefix                | -5                                               |
      | default_currency           | 1                                                |
      | zone                       | 1                                                |
      | need_zip_code              | true                                             |
      | zip_code_format            | 1 NL                                             |
      | address_format             | firstname lastname\naddress1\ncity\nCountry:name |
      | is_enabled                 | true                                             |
      | contains_states            | false                                            |
      | need_identification_number | false                                            |
      | display_tax_label          | true                                             |
      | shop_association           | 1                                                |
    Then I should get error that call prefix is invalid

  Scenario: Delete country
    When I delete country "test"
    Then country "test" should be deleted
