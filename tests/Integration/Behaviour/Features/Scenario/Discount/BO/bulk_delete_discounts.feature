# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags bulk-delete-discounts
@restore-all-tables-before-feature
@restore-languages-after-feature
@bulk-delete-discounts
Feature: Bulk delete discounts
  PrestaShop allows BO users to delete multiple discounts at once
  As a BO user
  I must be able to delete multiple discounts at once

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists

  Scenario: Delete multiple discount status at once
    Given I create a "cart_level" discount "cart_level1" with following properties:
      | name[en-US]       | Promotion1          |
      | active            | false               |
      | valid_from        | 2025-01-01 11:05:00 |
      | valid_to          | 2025-12-01 00:00:00 |
      | reduction_percent | 10.0                |
    Given I create a "cart_level" discount "cart_level2" with following properties:
      | name[en-US]       | Promotion2          |
      | active            | false               |
      | valid_from        | 2025-01-01 11:05:00 |
      | valid_to          | 2025-12-01 00:00:00 |
      | reduction_percent | 10.0                |
    Given I create a "cart_level" discount "cart_level3" with following properties:
      | name[en-US]       | Promotion3          |
      | active            | false               |
      | valid_from        | 2025-01-01 11:05:00 |
      | valid_to          | 2025-12-01 00:00:00 |
      | reduction_percent | 10.0                |
    Then discount "cart_level1" should exist
    And discount "cart_level2" should exist
    And discount "cart_level3" should exist
    When I bulk delete discounts "cart_level1,cart_level2"
    Then discount "cart_level1" should not exist
    And discount "cart_level2" should not exist
    And discount "cart_level3" should exist
