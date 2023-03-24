#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s country
@restore-all-tables-before-feature
Feature: country management
  As an employee
  I must be able to add, edit and delete country

  Scenario: Adding new country
    Given language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists
    When I add new country "test" with following properties:
      | name[en-US]                | testName        |
      | iso_code                   | TE              |
      | call_prefix                | 123             |
      | default_currency           | 1               |
      | zone                       | 1               |
      | need_zip_code              | true            |
      | zip_code_format            | 1 NL            |
      | address_format             | not implemented |
      | is_enabled                 | true            |
      | contains_states            | false           |
      | need_identification_number | false           |
      | display_tax_label          | true            |
      | shop_association           | 1               |
    Then the country "test" should have the following properties:
      | localizedNames[en-US] | testName |
      | localizedNames[fr-FR] | testName |
      | isoCode               | TE       |
      | callPrefix            | 123      |
      | defaultCurrency       | 1        |
      | zone                  | 1        |
      | needZipCode           | true     |
      | zipCodeFormat         | 1 NL     |
      | enabled               | true     |
      | containsStates        | false    |
      | needIdNumber          | false    |
      | displayTaxLabel       | true     |
      | shopAssociation       | 1        |

  Scenario: edit country
    Given language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists
    When I edit country "test" with following properties:
      | name[en-US]                | editName1       |
      | name[fr-FR]                | editName2       |
      | iso_code                   | TA              |
      | call_prefix                | 1234            |
      | default_currency           | 2               |
      | zone                       | 2               |
      | need_zip_code              | false           |
      | zip_code_format            | 1 NLL           |
      | address_format             | not implemented |
      | is_enabled                 | false           |
      | contains_states            | true            |
      | need_identification_number | true            |
      | display_tax_label          | false           |
      | shop_association           | 1               |
    Then the country "test" should have the following properties:
      | localizedNames[en-US] | editName1 |
      | localizedNames[fr-FR] | editName2 |
      | isoCode               | TA        |
      | callPrefix            | 1234      |
      | defaultCurrency       | 2         |
      | zone                  | 2         |
      | needZipCode           | false     |
      | zipCodeFormat         | 1 NLL     |
      | enabled               | false     |
      | containsStates        | true      |
      | needIdNumber          | true      |
      | displayTaxLabel       | false     |
      | shopAssociation       | 1         |

  Scenario: Delete country
    When I delete country "test"
    Then country "test" should be deleted
