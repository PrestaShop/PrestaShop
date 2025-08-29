# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-cart-level-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-cart-level-discount
Feature: Add discount
  PrestaShop allows BO users to create discounts
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists

  Scenario: Create a complete cart level discount
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
    When I create a "cart_level" discount "complete_amount_cart_level_discount" with following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | code               | PROMO_CART_2019_2   |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    And discount "complete_amount_cart_level_discount" should have the following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | type               | cart_level          |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | code               | PROMO_CART_2019_2   |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    When I create a "cart_level" discount "complete_amount_cart_level_discount_tax_excluded" with following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | code               | PROMO_CART_2019_3   |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | false               |
    And discount "complete_amount_cart_level_discount_tax_excluded" should have the following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | type               | cart_level          |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | code               | PROMO_CART_2019_3   |
      | reduction_amount   | 10.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | false               |

  Scenario: Create an active cart level discount with wrong values
    When I create a "cart_level" discount "invalid_percent_cart_level_discount_1" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2019_4   |
      | reduction_percent | -10.0               |
    Then I should get error that discount field reduction_percent is invalid
    When I create a "cart_level" discount "invalid_percent_cart_level_discount_2" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2019_5   |
      | reduction_percent | 102.0               |
    Then I should get error that discount field reduction_percent is invalid
    When I create a "cart_level" discount "invalid_amount_cart_level_discount" with following properties:
      | name[en-US]        | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | code               | PROMO_CART_2019_5   |
      | reduction_amount   | -102.0              |
      | reduction_currency | usd                 |
      | taxIncluded        | false               |
    Then I should get error that discount field reduction_amount is invalid
