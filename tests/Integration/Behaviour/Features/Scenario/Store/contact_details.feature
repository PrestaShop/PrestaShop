@restore-all-tables-before-feature
@restore-shops-after-feature
@contact_details
Feature: contact details configuration
  In order to display shop contact information to customers
  As a BO user
  I should be able to save and retrieve contact details

  Scenario: Save contact details
    When I save contact details with the following values:
      | name  | My Shop          |
      | email | shop@example.com |
      | city  | Paris            |
      | phone | 0112345678       |
      | fax   | 0112345679       |
    Then the contact details should have the following values:
      | name  | My Shop          |
      | email | shop@example.com |
      | city  | Paris            |
      | phone | 0112345678       |
      | fax   | 0112345679       |

  Scenario: Save contact details with a country and state
    When I save contact details with the following values:
      | name    | My US Shop       |
      | email   | shop@example.com |
      | country | United States    |
      | state   | Alabama          |
    Then the contact details country should be "United States"
    And the contact details state should be "Alabama"

  Scenario: Saving contact details for a country without states clears the state
    When I save contact details with the following values:
      | name    | My French Shop   |
      | email   | shop@example.com |
      | country | France           |
    Then the contact details country should be "France"
    And the contact details state should have no value

  Scenario: Saving contact details without a name fails validation
    When I save contact details with the following values:
      | name  |                  |
      | email | shop@example.com |
    Then saving contact details should fail with a validation error

  Scenario: Saving contact details from a shop context overrides only the checked fields
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And multiple shop context is loaded
    And I save contact details with the following values:
      | name  | My Shop          |
      | email | shop@example.com |
      | phone | 0100000000       |
    When single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name             | My Shop          |
      | email            | shop@example.com |
      | phone            | 0222222222       |
      | multistore_phone | true             |
    Then the shop "shop2" should only override the contact details "PS_SHOP_PHONE"
    And the contact details should have the following values:
      | name  | My Shop    |
      | phone | 0222222222 |
    When multiple shop context is loaded
    Then the contact details should have the following values:
      | phone | 0100000000 |
