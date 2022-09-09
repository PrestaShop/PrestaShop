#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s country
@restore-all-tables-before-feature
Feature: country management
  As an employee
  I must be able to add, edit and delete country

  Scenario: Adding new country
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
    When I query country "test" I should get a Country with properties:
      | localisedNames           | testName        |
      | isoCode                  | TE              |
      | callPrefix               | 123             |
      | defaultCurrency          | 1               |
      | zone                     | 1               |
      | needZipCode              | true            |
      | zipCodeFormat            | 1 NL            |
      | enabled                  | true            |
      | containsStates           | false           |
      | needIdNumber             | false           |
      | displayTaxLabel          | true            |
      | shopAssociation          | 1               |

  Scenario: edit country
    When I edit country "test" with following properties:
      | name[en-US]                | editName        |
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
    When I query country "test" I should get a Country with properties:
      | localisedNames           | editName        |
      | isoCode                  | TA              |
      | callPrefix               | 1234            |
      | defaultCurrency          | 2               |
      | zone                     | 2               |
      | needZipCode              | false           |
      | zipCodeFormat            | 1 NLL           |
      | enabled                  | false           |
      | containsStates           | true            |
      | needIdNumber             | true            |
      | displayTaxLabel          | false           |
      | shopAssociation          | 1               |
