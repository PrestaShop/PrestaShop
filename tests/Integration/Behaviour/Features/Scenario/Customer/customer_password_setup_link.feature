# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer --tags customer-password-setup-link
@restore-all-tables-before-feature
@customer-password-setup-link
Feature: Send the password setup link to a customer
  As a BO user
  I must be able to send the password setup link to a customer

  Scenario: Send the password setup link to a customer without a reset password token
    When I create customer "customer1" with following details:
      | firstName | first                    |
      | lastName  | customer                 |
      | email     | customer1@prestashop.com |
      | password  | PrestaShopForever1_!     |
    Then customer "customer1" should not have a reset password token
    When I send the password setup link to customer "customer1"
    Then customer "customer1" should have a valid reset password token
    And customer "customer1" should receive the password setup email with their reset password token

  Scenario: Send the password setup link again while the reset password token is still valid
    When I create customer "customer2" with following details:
      | firstName | second                   |
      | lastName  | customer                 |
      | email     | customer2@prestashop.com |
      | password  | PrestaShopForever1_!     |
    And customer "customer2" has the valid reset password token "0123456789abcdef0123456789abcdef01234567"
    When I send the password setup link to customer "customer2"
    Then customer "customer2" should have the valid reset password token "0123456789abcdef0123456789abcdef01234567"
    And customer "customer2" should receive the password setup email with their reset password token

  Scenario: Fail to send the password setup link to a customer that does not exist
    Given customer "nonexistent" does not exist
    When I send the password setup link to customer "nonexistent"
    Then I should get error that customer was not found

  Scenario: Fail to send the password setup link to a guest
    Given I create customer "guest" with following details:
      | firstName | guest                |
      | lastName  | customer             |
      | email     | guest@prestashop.com |
      | isGuest   | true                 |
    When I send the password setup link to customer "guest"
    Then I should get an error that the password setup link cannot be sent to a guest customer
    And customer "guest" should not have a reset password token

  Scenario: Fail to send the password setup link to a deleted customer
    Given I create customer "deleted" with following details:
      | firstName | deleted                |
      | lastName  | customer               |
      | email     | deleted@prestashop.com |
      | password  | PrestaShopForever1_!   |
    And customer "deleted" has been deleted
    When I send the password setup link to customer "deleted"
    Then I should get an error that the password setup link cannot be sent to a deleted customer
    And customer "deleted" should not have a reset password token

  Scenario: Fail to send the password setup link to an inactive customer
    Given I create customer "inactive" with following details:
      | firstName | inactive                |
      | lastName  | customer                |
      | email     | inactive@prestashop.com |
      | password  | PrestaShopForever1_!    |
    And I disable customer "inactive"
    When I send the password setup link to customer "inactive"
    Then I should get an error that the password setup link cannot be sent to an inactive customer
    And customer "inactive" should not have a reset password token
