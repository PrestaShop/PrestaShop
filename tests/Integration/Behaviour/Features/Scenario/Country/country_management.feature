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
    Then country "test" name should be "testName"
    And country "test" should be enabled
