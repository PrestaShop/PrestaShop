# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-with-feature-trigger
@restore-all-tables-before-feature
@discount-with-feature-trigger
Feature: Add discount with feature trigger on FO
  PrestaShop allows discounts with feature trigger as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1

  Scenario: First create products and the discount that will be used in following scenarios
    When I create product feature "element" with specified properties:
      | name[en-US]      | Nature Element |
      | associated shops | shop1          |
    Then product feature "element" should have following details:
      | name[en-US] | Nature Element |
      | name[fr-FR] | Nature Element |
    When I create feature value "fire" for feature "element" with following properties:
      | value[en-US] | Fire |
      | value[fr-FR] | Feu  |
    Given I add product "fireMagicBook" with following information:
      | name[en-US] | Fire Magic Book |
      | type        | standard        |
    Then product "fireMagicBook" should have no feature values
    When I set to product "fireMagicBook" the following feature values:
      | feature | feature_value |
      | element | fire          |
    Then product "fireMagicBook" should have following feature values:
      | feature | feature_value |
      | element | fire          |
    And I update product "fireMagicBook" stock with following information:
      | delta_quantity | 123 |
    And I enable product "fireMagicBook"
    And I update product "fireMagicBook" with following values:
      | price | 5.00 |
    Given I add product "darkMagicBook" with following information:
      | name[en-US] | Dark Magic Book |
      | type        | standard        |
    And I update product "darkMagicBook" stock with following information:
      | delta_quantity | 123 |
    And I enable product "darkMagicBook"
    And I update product "darkMagicBook" with following values:
      | price | 10.00 |
    Then product "darkMagicBook" should have no feature values
    When I create a "cart_level" discount "discount_with_feature_trigger" with following properties:
      | name[en-US]                | Promotion                  |
      | name[fr-FR]                | Promotion                  |
      | code                       | CART_LEVEL_FEATURE_TRIGGER |
      | active                     | true                       |
      | reduction_amount           | 3.0                        |
      | reduction_currency         | usd                        |
      | reduction_tax_included     | true                       |
      | productConditionQuantity   | 1                          |
      | productCondition[features] | fire                       |
    Then discount "discount_with_feature_trigger" should have the following properties:
      | name[en-US]                | Promotion                  |
      | name[fr-FR]                | Promotion                  |
      | type                       | cart_level                 |
      | code                       | CART_LEVEL_FEATURE_TRIGGER |
      | active                     | true                       |
      | reduction_amount           | 3.0                        |
      | reduction_currency         | usd                        |
      | reduction_tax_included     | true                       |
      | minimum_product_quantity   | 0                          |
      | productConditionQuantity   | 1                          |
      | productCondition[features] | fire                       |

  Scenario: Reduction with feature trigger on FO
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 1 product "Dark Magic Book" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$17.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $17.00 |
    When I use a voucher "discount_with_feature_trigger" on the cart "dummy_cart"
    Then I should get cart rule validation error
    And cart "dummy_cart" total with tax included should be '$17.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $17.00 |
    When I add 1 product "Fire Magic Book" to the cart "dummy_cart"
    When I use a voucher "discount_with_feature_trigger" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$19.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $15.00 |
      | shipping       | $7.00  |
      | total_discount | -$3.00 |
      | total          | $19.00 |
