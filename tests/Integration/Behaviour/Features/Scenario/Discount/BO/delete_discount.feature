# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags delete-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@delete-discount
Feature: Delete discount
  PrestaShop allows BO users to delete discounts
  As a BO user
  I must be able to delete discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists

  Scenario: Create a complete cart level discount and delete it
    When I create a "cart_level" discount "complete_percent_cart_level_discount" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2019     |
      | reduction_percent | 10.0                |
    And discount "complete_percent_cart_level_discount" should have the following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | type              | cart_level          |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2019     |
      | reduction_percent | 10.0                |
    Then I delete discount "complete_percent_cart_level_discount":
    Then discount "complete_percent_cart_level_discount" should not exist anymore:
