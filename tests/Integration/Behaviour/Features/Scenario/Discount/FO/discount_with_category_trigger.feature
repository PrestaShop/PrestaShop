# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-with-category-trigger
@restore-all-tables-before-feature
@discount-with-category-trigger
Feature: Add discount with category trigger on FO
  PrestaShop allows discounts with restricted products as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And category "home" in default language named "Home" exists
    And category "clothes" in default language named "Clothes" exists
    And category "clothes" parent is category "home"
    And category "accessories" in default language named "Accessories" exists
    And category "accessories" parent is category "home"

  Scenario: First create products and the discount that will be used in following scenarios
    Given I add product "product1" with following information:
      | name[en-US] | eastern european tracksuit |
      | type        | standard                   |
    And product "product1" should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    Then product product1 should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    When I assign product product1 to following categories:
      | categories       | [home, clothes] |
      | default category | clothes         |
    Then product product1 should be assigned to following categories:
      | id reference | name    | is default |
      | home         | Home    | false      |
      | clothes      | Clothes | true       |
    And I update product "product1" stock with following information:
      | delta_quantity | 123 |
    And I enable product "product1"
    And I update product "product1" with following values:
      | price | 5.00 |
    Given I add product "product2" with following information:
      | name[en-US] | shoes    |
      | type        | standard |
    And product "product2" should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    Then product product2 should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    When I assign product product2 to following categories:
      | categories       | [home, accessories] |
      | default category | accessories         |
    Then product product2 should be assigned to following categories:
      | id reference | name        | is default |
      | home         | Home        | false      |
      | accessories  | Accessories | true       |
    And I update product "product2" stock with following information:
      | delta_quantity | 123 |
    And I enable product "product2"
    And I update product "product2" with following values:
      | price | 10.00 |
    When I create a "cart_level" discount "discount_with_category_trigger" with following properties:
      | name[en-US]        | Promotion                   |
      | name[fr-FR]        | Promotion                   |
      | code               | CART_LEVEL_CATEGORY_TRIGGER |
      | active             | true                        |
      | reduction_amount   | 3.0                         |
      | reduction_currency | usd                         |
      | taxIncluded        | true                        |
    Then discount "discount_with_category_trigger" should have the following properties:
      | name[en-US]        | Promotion                   |
      | name[fr-FR]        | Promotion                   |
      | type               | cart_level                  |
      | code               | CART_LEVEL_CATEGORY_TRIGGER |
      | active             | true                        |
      | reduction_amount   | 3.0                         |
      | reduction_currency | usd                         |
      | taxIncluded        | true                        |
    When I update discount "discount_with_category_trigger" with following conditions matching at least 1 products:
      | condition_type | items   |
      | categories     | clothes |
    Then discount "discount_with_category_trigger" should have the following properties:
      | minimum_product_quantity | 0 |
    And discount "discount_with_category_trigger" should have the following product conditions matching at least 1 products:
      | condition_type | items   |
      | categories     | clothes |

  Scenario: Reduction with category trigger on FO
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 1 product "shoes" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$17.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $17.00 |
    When I use a voucher "discount_with_category_trigger" on the cart "dummy_cart"
    Then I should get cart rule validation error
    And cart "dummy_cart" total with tax included should be '$17.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $17.00 |
    When I add 1 product "eastern european tracksuit" to the cart "dummy_cart"
    When I use a voucher "discount_with_category_trigger" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$19.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $15.00 |
      | shipping       | $7.00  |
      | total_discount | -$3.00 |
      | total          | $19.00 |

  Scenario: Discount on category that is not default:
    When I create a "cart_level" discount "discount_with_category_trigger_2" with following properties:
      | name[en-US]        | Promotion                     |
      | name[fr-FR]        | Promotion                     |
      | code               | CART_LEVEL_CATEGORY_TRIGGER_2 |
      | active             | true                          |
      | reduction_amount   | 3.0                           |
      | reduction_currency | usd                           |
      | taxIncluded        | true                          |
    Then discount "discount_with_category_trigger_2" should have the following properties:
      | name[en-US]        | Promotion                     |
      | name[fr-FR]        | Promotion                     |
      | type               | cart_level                    |
      | code               | CART_LEVEL_CATEGORY_TRIGGER_2 |
      | active             | true                          |
      | reduction_amount   | 3.0                           |
      | reduction_currency | usd                           |
      | taxIncluded        | true                          |
    When I update discount "discount_with_category_trigger_2" with following conditions matching at least 1 products:
      | condition_type | items |
      | categories     | home  |
    Then discount "discount_with_category_trigger_2" should have the following properties:
      | minimum_product_quantity | 0 |
    And discount "discount_with_category_trigger_2" should have the following product conditions matching at least 1 products:
      | condition_type | items |
      | categories     | home  |

  Scenario: Reduction with category trigger on FO
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 1 product "shoes" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$17.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $17.00 |
    When I use a voucher "discount_with_category_trigger_2" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$14.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | -$3.00 |
      | total          | $14.00 |
