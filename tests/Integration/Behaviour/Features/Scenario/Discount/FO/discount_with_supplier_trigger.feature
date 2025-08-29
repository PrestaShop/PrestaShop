# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-with-supplier-trigger
@restore-all-tables-before-feature
@discount-with-supplier-trigger
Feature: Add discount with supplier trigger on FO
  PrestaShop allows discounts with supplier trigger as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1

  Scenario: First create products and the discount that will be used in following scenarios
    When I add new supplier supplier1 with the following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | shops                   | [shop1]            |
    And I add new supplier supplier2 with the following properties:
      | name                    | my supplier 2      |
      | address                 | Donelaicio st. 2   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr two |
      | meta description[en-US] |                    |
      | shops                   | [shop1]            |
    Given I add product "product1" with following information:
      | name[en-US] | eastern european tracksuit |
      | type        | standard                   |
    When I associate suppliers to product "product1"
      | supplier  | product_supplier  |
      | supplier1 | product1supplier1 |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
    And I update product "product1" stock with following information:
      | delta_quantity | 123 |
    And I enable product "product1"
    And I update product "product1" with following values:
      | price | 5.00 |
    Given I add product "product2" with following information:
      | name[en-US] | shoes    |
      | type        | standard |
    When I associate suppliers to product "product2"
      | supplier  | product_supplier  |
      | supplier2 | product2supplier2 |
    And I update product "product2" stock with following information:
      | delta_quantity | 123 |
    And I enable product "product2"
    And I update product "product2" with following values:
      | price | 10.00 |
    When I create a "cart_level" discount "discount_with_supplier_trigger" with following properties:
      | name[en-US]        | Promotion                   |
      | name[fr-FR]        | Promotion                   |
      | code               | CART_LEVEL_CATEGORY_TRIGGER |
      | active             | true                        |
      | reduction_amount   | 3.0                         |
      | reduction_currency | usd                         |
      | taxIncluded        | true                        |
    Then discount "discount_with_supplier_trigger" should have the following properties:
      | name[en-US]        | Promotion                   |
      | name[fr-FR]        | Promotion                   |
      | type               | cart_level                  |
      | code               | CART_LEVEL_CATEGORY_TRIGGER |
      | active             | true                        |
      | reduction_amount   | 3.0                         |
      | reduction_currency | usd                         |
      | taxIncluded        | true                        |
    When I update discount "discount_with_supplier_trigger" with following conditions matching at least 1 products:
      | condition_type | items     |
      | suppliers      | supplier1 |
    Then discount "discount_with_supplier_trigger" should have the following properties:
      | minimum_product_quantity | 0 |
    And discount "discount_with_supplier_trigger" should have the following product conditions matching at least 1 products:
      | condition_type | items     |
      | suppliers      | supplier1 |

  Scenario: Reduction with supplier trigger on FO
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 1 product "shoes" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$17.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $17.00 |
    When I use a voucher "discount_with_supplier_trigger" on the cart "dummy_cart"
    Then I should get cart rule validation error
    And cart "dummy_cart" total with tax included should be '$17.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $17.00 |
    When I add 1 product "eastern european tracksuit" to the cart "dummy_cart"
    When I use a voucher "discount_with_supplier_trigger" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$19.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $15.00 |
      | shipping       | $7.00  |
      | total_discount | -$3.00 |
      | total          | $19.00 |
