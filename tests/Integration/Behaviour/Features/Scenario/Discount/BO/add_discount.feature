# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount
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

  Scenario: Create a simple discount with free shipping
    When I create a free shipping discount "basic_free_shipping_discount"
    Then discount "basic_free_shipping_discount" should have the following properties:
      | free_shipping | true  |
      | active        | false |

  Scenario: Create a complete discount with free shipping
    When I create a free shipping discount "complete_free_shipping_discount" with following properties:
      | name[en-US]       | Promotion              |
      | name[fr-FR]       | Promotion fr           |
      | description       | Promotion for holidays |
      | highlight         | false                  |
      | active            | true                   |
      | allow_partial_use | false                  |
      | priority          | 2                      |
      | valid_from        | 2019-01-01 11:05:00    |
      | valid_to          | 2019-12-01 00:00:00    |
      | total_quantity    | 10                     |
      | quantity_per_user | 1                      |
      | code              | PROMO_2019             |
    Then discount "complete_free_shipping_discount" should have the following properties:
      | name[en-US]       | Promotion              |
      | name[fr-FR]       | Promotion fr           |
      | description       | Promotion for holidays |
      | highlight         | false                  |
      | active            | true                   |
      | allow_partial_use | false                  |
      | priority          | 2                      |
      | valid_from        | 2019-01-01 11:05:00    |
      | valid_to          | 2019-12-01 00:00:00    |
      | total_quantity    | 10                     |
      | quantity_per_user | 1                      |
      | free_shipping     | true                   |
      | code              | PROMO_2019             |

  Scenario: Create a discount with free shipping online but without names should be forbidden
    When I create a free shipping discount "invalid_free_shipping_discount" with following properties:
      | active | true |
    Then I should get error that discount field name is invalid
    # Discount online with name only in default language is valid though
    When I create a free shipping discount "default_language_free_shipping_discount" with following properties:
      | active      | true      |
      | name[en-US] | Promotion |
      | name[fr-FR] |           |
    Then discount "default_language_free_shipping_discount" should have the following properties:
      | active      | true      |
      | name[en-US] | Promotion |
      | name[fr-FR] |           |
