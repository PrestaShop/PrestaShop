# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-free-shipping-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-free-shipping-discount
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

  Scenario: Create a complete discount with free shipping
    When I create a "free_shipping" discount "complete_free_shipping_discount" with following properties:
      | name[en-US]       | Promotion              |
      | name[fr-FR]       | Promotion fr           |
      | active            | true                   |
      | valid_from        | 2019-01-01 11:05:00    |
      | valid_to          | 2019-12-01 00:00:00    |
      | code              | PROMO_2019             |
    Then discount "complete_free_shipping_discount" should have the following properties:
      | name[en-US]       | Promotion              |
      | name[fr-FR]       | Promotion fr           |
      | type              | free_shipping          |
      | active            | true                   |
      | valid_from        | 2019-01-01 11:05:00    |
      | valid_to          | 2019-12-01 00:00:00    |
      | code              | PROMO_2019             |

