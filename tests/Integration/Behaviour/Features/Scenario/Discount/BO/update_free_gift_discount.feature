# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags update-free-gift-level-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@update-free-gift-level-discount
Feature: Update discount
  PrestaShop allows BO users to update discounts
  As a BO user
  I must be able to update discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists

  Scenario: Create a complete cart level discount and update it
    Given there is a product in the catalog named "Free Product" with a price of 20.0 and 1000 items in stock
    Given there is a product "hummingbird-tshirt" with name "Hummingbird printed t-shirt"
    Given there is a product "hummingbird-notebook" with name "Hummingbird notebook"
    When I create a "free_gift" discount "complete_free_gift_discount" with following properties:
      | name[en-US]  | Promotion           |
      | name[fr-FR]  | Promotion fr        |
      | active       | true                |
      | valid_from   | 2019-01-01 11:05:00 |
      | valid_to     | 2019-12-01 00:00:00 |
      | code         | FREE_GIFT_2019      |
      | gift_product | hummingbird-tshirt  |
    Then discount "complete_free_gift_discount" should have the following properties:
      | name[en-US]  | Promotion           |
      | name[fr-FR]  | Promotion fr        |
      | type         | free_gift           |
      | active       | true                |
      | valid_from   | 2019-01-01 11:05:00 |
      | valid_to     | 2019-12-01 00:00:00 |
      | code         | FREE_GIFT_2019      |
      | gift_product | hummingbird-tshirt  |
    Then I update discount "complete_free_gift_discount" with the following properties:
      | gift_product | hummingbird-notebook |
    Then discount "complete_free_gift_discount" should have the following properties:
      | name[en-US]  | Promotion            |
      | name[fr-FR]  | Promotion fr         |
      | type         | free_gift            |
      | active       | true                 |
      | valid_from   | 2019-01-01 11:05:00  |
      | valid_to     | 2019-12-01 00:00:00  |
      | code         | FREE_GIFT_2019       |
      | gift_product | hummingbird-notebook |

  Scenario: Create a complete discount and update it with customizable product
    Given there is a product in the catalog named "customizable-mug" with a price of 20.0 and 1000 items in stock
    Given product "customizable-mug" has a customization field named "custo1"
    When I create a "free_gift" discount "complete_free_gift_discount_customizable_product" with following properties:
      | name[en-US]  | Promotion           |
      | name[fr-FR]  | Promotion fr        |
      | active       | true                |
      | valid_from   | 2019-01-01 11:05:00 |
      | valid_to     | 2019-12-01 00:00:00 |
      | code         | FREE_GIFT_2025      |
      | gift_product | hummingbird-tshirt  |
    Then discount "complete_free_gift_discount_customizable_product" should have the following properties:
      | name[en-US]  | Promotion           |
      | name[fr-FR]  | Promotion fr        |
      | type         | free_gift           |
      | active       | true                |
      | valid_from   | 2019-01-01 11:05:00 |
      | valid_to     | 2019-12-01 00:00:00 |
      | code         | FREE_GIFT_2025      |
      | gift_product | hummingbird-tshirt  |
    Then I update discount "complete_free_gift_discount_customizable_product" with the following properties:
      | gift_product | customizable-mug    |
    Then I should get error that discount field gift_product is invalid
