# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s country --tags delete-default-country-multishop
@restore-all-tables-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@delete-default-country-multishop
Feature: Delete the default country of a shop in multishop
  A deleted country is removed from every shop
  As a BO user
  I must not be able to delete the default country of any shop, whatever the shop context

  Scenario: deleting the default country of another shop is refused
    Given I enable multishop feature
    And language "language1" with locale "en-US" exists
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_shop2" and color "blue" for the group "default_shop_group"
    And I add new country "shop2_default_country" with following properties:
      | name[en-US]                | Shop2 Default Country                            |
      | iso_code                   | QM                                               |
      | call_prefix                | 25                                               |
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
    And no exception should have been thrown
    And the default country of shop "shop2" is "shop2_default_country"
    And single shop "shop1" context is loaded
    When I delete country "shop2_default_country"
    Then I should get an error that the default country cannot be deleted

  Scenario: bulk delete refuses the default country of another shop and deletes the others
    Given I add new country "multishop_bulk_country" with following properties:
      | name[en-US]                | Multishop Bulk Country                           |
      | iso_code                   | QN                                               |
      | call_prefix                | 26                                               |
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
    And no exception should have been thrown
    When I bulk delete countries "shop2_default_country, multishop_bulk_country"
    Then I should get a bulk country exception refusing the default country
    And country "multishop_bulk_country" should be deleted
