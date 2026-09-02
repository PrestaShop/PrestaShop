# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-with-code
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-with-code
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

  Scenario: Create a discount with code that isn't used already
    When I create a "cart_level" discount "discount_promo_cart_2019" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2019     |
      | reduction_percent | 10.0                |
    And discount "discount_promo_cart_2019" should have the following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | type              | cart_level          |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2019     |
      | reduction_percent | 10.0                |

  Scenario: Create a discount with code that is already used
    When I create a "cart_level" discount "discount_promo_cart_2019_2" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2019     |
      | reduction_percent | 20.0                |
    Then I should get an error that the discount code is already used

  Scenario: Update a discount with the same code
    When I create a "cart_level" discount "discount_promo_cart_2025" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2025     |
      | reduction_percent | 15.0                |
    When I update discount "discount_promo_cart_2025" with the following properties:
      | name[en-US]       | Promotion_updated   |
      | name[fr-FR]       | Promotion_fr_updated|
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2025     |
      | reduction_percent | 15.0                |
    And discount "discount_promo_cart_2025" should have the following properties:
      | name[en-US]       | Promotion_updated   |
      | name[fr-FR]       | Promotion_fr_updated|
      | type              | cart_level          |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2025     |
      | reduction_percent | 15.0                |

  Scenario: Update a discount with a new code that is used already
    When I create a "cart_level" discount "discount_promo_cart_2" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_UNUSED        |
      | reduction_percent | 20.0                |
    And discount "discount_promo_cart_2" should have the following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | type              | cart_level          |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_UNUSED        |
      | reduction_percent | 20.0                |
    When I update discount "discount_promo_cart_2" with the following properties:

      | name[en-US]       | Promotion_updated   |
      | name[fr-FR]       | Promotion_fr_updated|
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2019     |
      | reduction_percent | 15.0                |
    Then I should get an error that the discount code is already used
