# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags update-product-level-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@update-product-level-discount
Feature: Update discount
  PrestaShop allows BO users to update discounts
  As a BO user
  I must be able to update discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 39.812 and 1000 items in stock
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists

  Scenario: Create a complete cart level discount
    When I create a "product_level" discount "complete_percent_product_level_discount" with following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_PRODUCT_2019  |
      | reduction_percent | 10.0                |
      | reduction_product | product1            |
    And discount "complete_percent_product_level_discount" should have the following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | type              | product_level       |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_PRODUCT_2019  |
      | reduction_percent | 10.0                |
      | reduction_product | product1            |
    Then I update discount "complete_percent_product_level_discount" with the following properties:
      | reduction_percent | 15.0                |
      | reduction_product | product2            |
    Then discount "complete_percent_product_level_discount" should have the following properties:
      | name[en-US]       | Promotion           |
      | name[fr-FR]       | Promotion_fr        |
      | type              | product_level       |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_PRODUCT_2019  |
      | reduction_percent | 15.0                |
      | reduction_product | product2            |
