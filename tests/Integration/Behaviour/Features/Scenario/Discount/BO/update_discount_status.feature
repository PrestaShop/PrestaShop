# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags update-discount-status
@restore-all-tables-before-feature
@restore-languages-after-feature
@update-discount-status
Feature: Update discount status
  PrestaShop allows BO users to enable or disable a discount from the edit page
  As a BO user
  I must be able to change the status of a discount when editing it

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given currency "usd" is the default one
    And language with iso code "en" is the default one

  Scenario: Disable an enabled discount from the edit page
    Given I create a "cart_level" discount "my_discount" with following properties:
      | name[en-US]       | Promotion           |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:00 |
      | reduction_percent | 10.0                |
    Then discount "my_discount" is enabled
    When I update discount "my_discount" with the following properties:
      | active | false |
    Then discount "my_discount" is disabled

  Scenario: Enable a disabled discount from the edit page
    Given I create a "cart_level" discount "my_discount" with following properties:
      | name[en-US]       | Promotion           |
      | active            | false               |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2025-12-31 23:59:00 |
      | reduction_percent | 10.0                |
    Then discount "my_discount" is disabled
    When I update discount "my_discount" with the following properties:
      | active | true |
    Then discount "my_discount" is enabled
