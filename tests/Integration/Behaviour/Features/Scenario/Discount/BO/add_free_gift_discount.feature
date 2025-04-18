# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-free-gift-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-free-gift-discount
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

  Scenario: Create a simple discount with free gift
    When I create a free gift discount "basic_free_gift_discount"
    Then discount "basic_free_gift_discount" should have the following properties:
      | active           | false |

  Scenario: Create a complete discount with free gift
    Given there is a product in the catalog named "Free Product" with a price of 20.0 and 1000 items in stock
    Given there is a product "hummingbird-tshirt" with name "Hummingbird printed t-shirt"
    When I create a free gift discount "complete_free_gift_discount" with following properties:
      | name[en-US]      | Promotion              |
      | name[fr-FR]      | Promotion fr           |
      | active           | true                   |
      | valid_from       | 2019-01-01 11:05:00    |
      | valid_to         | 2019-12-01 00:00:00    |
      | code             | FREE_GIFT_2019         |
      | gift_product     | hummingbird-tshirt     |
    Then discount "complete_free_gift_discount" should have the following properties:
      | name[en-US]      | Promotion              |
      | name[fr-FR]      | Promotion fr           |
      | active           | true                   |
      | valid_from       | 2019-01-01 11:05:00    |
      | valid_to         | 2019-12-01 00:00:00    |
      | code             | FREE_GIFT_2019         |
      | gift_product     | hummingbird-tshirt     |

  Scenario: Create a discount with free gift online but without names should be forbidden
    When I create a free gift discount "invalid_free_gift_discount" with following properties:
      | active | true |
    Then I should get error that discount field name is invalid
    # Discount online with name only in default language is valid though
    When I create a free gift discount "default_language_free_gift_discount" with following properties:
      | active           | true      |
      | name[en-US]      | Promotion |
      | name[fr-FR]      |           |
    Then discount "default_language_free_gift_discount" should have the following properties:
      | active           | true      |
      | name[en-US]      | Promotion |
      | name[fr-FR]      |           |
