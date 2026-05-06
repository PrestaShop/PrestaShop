@restore-all-tables-before-feature
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
