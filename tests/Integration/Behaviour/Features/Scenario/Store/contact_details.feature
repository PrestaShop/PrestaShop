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

  Scenario: Saving contact details with a country containing states and no state fails validation
    Given I save contact details with the following values:
      | name    | My French Shop   |
      | email   | shop@example.com |
      | country | France           |
    When I save contact details with the following values:
      | name    | My US Shop       |
      | email   | shop@example.com |
      | country | United States    |
    Then saving contact details should fail with the error "An address located in a country containing states must have a state selected."
    And the contact details country should be "France"

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

  Scenario: Unchecking a field in a shop context removes its shop value
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And multiple shop context is loaded
    And I save contact details with the following values:
      | name  | My Shop          |
      | email | shop@example.com |
      | phone | 0100000000       |
    And single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name             | My Shop          |
      | email            | shop@example.com |
      | phone            | 0222222222       |
      | multistore_phone | true             |
    When I save contact details with the following values:
      | name  | My Shop          |
      | email | shop@example.com |
      | phone | 0222222222       |
    Then the shop "shop2" should only override the contact details ""
    And the contact details should have the following values:
      | phone | 0100000000 |

  Scenario: Overriding the country and the state in a shop context stores their names for that shop only
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And multiple shop context is loaded
    And I save contact details with the following values:
      | name    | My French Shop   |
      | email   | shop@example.com |
      | country | France           |
    When single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name                  | My French Shop   |
      | email                 | shop@example.com |
      | country               | United States    |
      | state                 | Alabama          |
      | multistore_id_country | true             |
      | multistore_id_state   | true             |
    Then the shop "shop2" should only override the contact details "PS_SHOP_COUNTRY,PS_SHOP_COUNTRY_ID,PS_SHOP_STATE,PS_SHOP_STATE_ID"
    And the contact details country should be "United States"
    And the contact details state should be "Alabama"
    When multiple shop context is loaded
    Then the contact details country should be "France"
    And the contact details state should have no value

  Scenario: Overriding only the state in a shop context keeps the inherited country
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And multiple shop context is loaded
    And I save contact details with the following values:
      | name    | My US Shop       |
      | email   | shop@example.com |
      | country | United States    |
      | state   | Alabama          |
    When single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name                | My US Shop       |
      | email               | shop@example.com |
      | country             | United States    |
      | state               | Alaska           |
      | multistore_id_state | true             |
    Then the shop "shop2" should only override the contact details "PS_SHOP_STATE,PS_SHOP_STATE_ID"
    And the contact details country should be "United States"
    And the contact details state should be "Alaska"

  Scenario: Overriding only the country in a shop context empties the inherited state of another country
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And multiple shop context is loaded
    And I save contact details with the following values:
      | name    | My US Shop       |
      | email   | shop@example.com |
      | country | United States    |
      | state   | Alabama          |
    When single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name                  | My US Shop       |
      | email                 | shop@example.com |
      | country               | France           |
      | state                 | Alabama          |
      | multistore_id_country | true             |
    Then the shop "shop2" should only override the contact details "PS_SHOP_COUNTRY,PS_SHOP_COUNTRY_ID,PS_SHOP_STATE,PS_SHOP_STATE_ID"
    And the contact details country should be "France"
    And the contact details state should have no value
    When multiple shop context is loaded
    Then the contact details country should be "United States"
    And the contact details state should be "Alabama"
    # Unchecking the Country also removes the empty State override created with it (its row is hidden)
    When single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name                | My US Shop       |
      | email               | shop@example.com |
      | country             | France           |
      | multistore_id_state | true             |
    Then the shop "shop2" should only override the contact details ""
    And the contact details country should be "United States"
    And the contact details state should be "Alabama"

  Scenario: Removing the country override in a shop context removes a state override of another country
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And multiple shop context is loaded
    And I save contact details with the following values:
      | name    | My US Shop       |
      | email   | shop@example.com |
      | country | United States    |
      | state   | Alabama          |
    And single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name                  | My US Shop       |
      | email                 | shop@example.com |
      | country               | Canada           |
      | state                 | Quebec           |
      | multistore_id_country | true             |
      | multistore_id_state   | true             |
    # The disabled Country still submits the shop's own Canada
    When I save contact details with the following values:
      | name                | My US Shop       |
      | email               | shop@example.com |
      | country             | Canada           |
      | state               | Quebec           |
      | multistore_id_state | true             |
    Then the shop "shop2" should only override the contact details ""
    And the contact details country should be "United States"
    And the contact details state should be "Alabama"

  Scenario: Removing the country override in a shop context keeps a state override of the inherited country
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And multiple shop context is loaded
    And I save contact details with the following values:
      | name    | My US Shop       |
      | email   | shop@example.com |
      | country | United States    |
      | state   | Alabama          |
    And single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name                  | My US Shop       |
      | email                 | shop@example.com |
      | country               | United States    |
      | state                 | Alaska           |
      | multistore_id_country | true             |
      | multistore_id_state   | true             |
    When I save contact details with the following values:
      | name                | My US Shop       |
      | email               | shop@example.com |
      | country             | United States    |
      | state               | Alaska           |
      | multistore_id_state | true             |
    Then the shop "shop2" should only override the contact details "PS_SHOP_STATE,PS_SHOP_STATE_ID"
    And the contact details country should be "United States"
    And the contact details state should be "Alaska"

  Scenario: Overriding only a country containing states in a shop context requires a state
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And multiple shop context is loaded
    And I save contact details with the following values:
      | name    | My French Shop   |
      | email   | shop@example.com |
      | country | France           |
    When single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name                  | My French Shop   |
      | email                 | shop@example.com |
      | country               | United States    |
      | multistore_id_country | true             |
    Then saving contact details should fail with the error "An address located in a country containing states must have a state selected."
    And the shop "shop2" should only override the contact details ""

  Scenario: A shop inheriting a country containing states without a state can save its other contact details
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And multiple shop context is loaded
    And the contact details of all shops have the country "United States" and no state
    When single shop "shop2" context is loaded
    And I save contact details with the following values:
      | name             | My US Shop       |
      | email            | shop@example.com |
      | country          | United States    |
      | phone            | 0112345678       |
      | multistore_phone | true             |
    Then the shop "shop2" should only override the contact details "PS_SHOP_PHONE"
