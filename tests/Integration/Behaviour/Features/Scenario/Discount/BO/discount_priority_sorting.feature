# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-priority-sorting
@restore-all-tables-before-feature
@discount-priority-sorting
Feature: Discount Priority Sorting
  PrestaShop allows BO users to create discounts with priority-based sorting
  As a BO user
  I must be able to create discounts that follow priority rules when automatically applied to carts

  Background:
    Given I register a enabled feature flag "discount"
    Given there is a customer named "testCustomer2" whose email is "pub2@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a product in the catalog named "test_product1" with a price of 10.0 and 100 items in stock
    And I enable feature flag "discount"

  Scenario: Priority-based discount replacement - Higher priority replaces lower priority
    When I create a "cart_level" discount "discount_10" with following properties:
      | name[en-US]        | Discount for 10     |
      | active             | true                |
      | valid_from         | 2025-01-01 11:05:00 |
      | valid_to           | 2025-12-01 00:00:00 |
      | priority           | 2                   |
      | reduction_amount   | 1.0                 |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
      | code               |                     |
    And discount "discount_10" should have the following properties:
      | type             | cart_level |
      | priority         | 2          |
      | reduction_amount | 1.0        |
    When I create a "cart_level" discount "discount_20" with following properties:
      | name[en-US]        | Discount for 20     |
      | active             | true                |
      | valid_from         | 2025-01-01 11:05:00 |
      | valid_to           | 2025-12-01 00:00:00 |
      | priority           | 1                   |
      | reduction_amount   | 3.0                 |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
      | code               |                     |
    And discount "discount_20" should have the following properties:
      | type             | cart_level |
      | priority         | 1          |
      | reduction_amount | 3.0        |
    When I update discount "discount_10" with the condition of a minimum amount:
      | minimum_amount                   | 5.0   |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | false |
    When I update discount "discount_20" with the condition of a minimum amount:
      | minimum_amount                   | 15.0  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | false |
    Given I create an empty cart "test_cart" for customer "testCustomer2"
    When I add 1 product "test_product1" to the cart "test_cart"
    Then cart "test_cart" total with tax included should be '$16.00'
    And my cart "test_cart" should have the following details, without hiding auto discounts:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | -$1.00 |
      | total          | $16.00 |
    When I add 1 product "test_product1" to the cart "test_cart"
    Then cart "test_cart" total with tax included should be '$24.00'
    And my cart "test_cart" should have the following details, without hiding auto discounts:
      | total_products | $20.00 |
      | shipping       | $7.00  |
      | total_discount | -$3.00 |
      | total          | $24.00 |

  Scenario: Same priority, different types - type order determines winner
    When I create a "cart_level" discount "cart_level_discount" with following properties:
      | name[en-US]        | Cart Level Discount |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | priority           | 1                   |
      | reduction_amount   | 1.0                 |
      | reduction_currency | usd                 |
      | taxIncluded        | false               |
      | code               |                     |
    And discount "cart_level_discount" should have the following properties:
      | type             | cart_level |
      | priority         | 1          |
      | reduction_amount | 1.0        |
    When I create a "free_shipping" discount "free_shipping_discount" with following properties:
      | name[en-US] | Free Shipping Discount |
      | active      | true                   |
      | valid_from  | 2019-01-01 11:05:00    |
      | valid_to    | 2019-12-01 00:00:00    |
      | priority    | 1                      |
      | code        |                        |
    And discount "free_shipping_discount" should have the following properties:
      | type     | free_shipping |
      | priority | 1             |
    When I update discount "cart_level_discount" with the condition of a minimum amount:
      | minimum_amount                   | 10.0  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | false |
      | minimum_amount_shipping_included | false |
    When I update discount "free_shipping_discount" with the condition of a minimum amount:
      | minimum_amount                   | 10.0  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | false |
      | minimum_amount_shipping_included | false |
    Given I create an empty cart "test_cart" for customer "testCustomer2"
    When I add 1 product "test_product1" to the cart "test_cart"
    Then cart "test_cart" total with tax included should be '$16.00'
    And my cart "test_cart" should have the following details, without hiding auto discounts:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | -$1.00 |
      | total          | $16.00 |

  Scenario: Same priority and type, creation date determines winner
    When I create a "cart_level" discount "older_cart_discount" with following properties:
      | name[en-US]        | Older Cart Discount |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | priority           | 1                   |
      | reduction_amount   | 1.0                 |
      | reduction_currency | usd                 |
      | taxIncluded        | false               |
      | code               |                     |
    And discount "older_cart_discount" should have the following properties:
      | type             | cart_level |
      | priority         | 1          |
      | reduction_amount | 1.0        |
    When I create a "cart_level" discount "newer_cart_discount" with following properties:
      | name[en-US]        | Newer Cart Discount |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | priority           | 1                   |
      | reduction_amount   | 1.2                 |
      | reduction_currency | usd                 |
      | taxIncluded        | false               |
      | code               |                     |
    And discount "newer_cart_discount" should have the following properties:
      | type             | cart_level |
      | priority         | 1          |
      | reduction_amount | 1.2        |
    When I update discount "older_cart_discount" with the condition of a minimum amount:
      | minimum_amount                   | 10.0  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | false |
      | minimum_amount_shipping_included | false |
    When I update discount "newer_cart_discount" with the condition of a minimum amount:
      | minimum_amount                   | 10.0  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | false |
      | minimum_amount_shipping_included | false |
     Given I create an empty cart "test_cart" for customer "testCustomer2"
     When I add 1 product "test_product1" to the cart "test_cart"
     Then cart "test_cart" total with tax included should be '$16.00'
     And my cart "test_cart" should have the following details, without hiding auto discounts:
       | total_products | $10.00 |
       | shipping       | $7.00  |
       | total_discount | -$1.00 |
       | total          | $16.00 |

   Scenario: Lower priority discount cannot replace higher priority discount
    When I create a "cart_level" discount "lower_priority_discount" with following properties:
      | name[en-US]        | Lower Priority Discount |
      | active             | true                    |
      | valid_from         | 2019-01-01 11:05:00     |
      | valid_to           | 2019-12-01 00:00:00     |
      | priority           | 3                       |
      | reduction_amount   | 1.5                     |
      | reduction_currency | usd                     |
      | taxIncluded        | true                    |
      | code               |                         |
    And discount "lower_priority_discount" should have the following properties:
      | type             | cart_level |
      | priority         | 3          |
      | reduction_amount | 1.5        |
    When I create a "cart_level" discount "higher_priority_discount" with following properties:
      | name[en-US]        | Higher Priority Discount |
      | active             | true                     |
      | valid_from         | 2019-01-01 11:05:00      |
      | valid_to           | 2019-12-01 00:00:00      |
      | priority           | 1                        |
      | reduction_amount   | 1.0                      |
      | reduction_currency | usd                      |
      | taxIncluded        | true                     |
      | code               |                          |
    And discount "higher_priority_discount" should have the following properties:
      | type             | cart_level |
      | priority         | 1          |
      | reduction_amount | 1.0        |
    When I update discount "lower_priority_discount" with the condition of a minimum amount:
      | minimum_amount                   | 10.0  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | false |
    When I update discount "higher_priority_discount" with the condition of a minimum amount:
      | minimum_amount                   | 10.0  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | false |
    Given I create an empty cart "test_cart" for customer "testCustomer2"
    When I add 1 product "test_product1" to the cart "test_cart"
    Then cart "test_cart" total with tax included should be '$16.00'
    And my cart "test_cart" should have the following details, without hiding auto discounts:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | -$1.00 |
      | total          | $16.00 |

  Scenario: Equal priority discounts - first one wins
    When I create a "cart_level" discount "first_discount" with following properties:
      | name[en-US]        | First Discount      |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | priority           | 1                   |
      | reduction_amount   | 1.0                 |
      | reduction_currency | usd                 |
      | taxIncluded        | false               |
      | code               |                     |
    And discount "first_discount" should have the following properties:
      | type             | cart_level |
      | priority         | 1          |
      | reduction_amount | 1.0        |
    When I create a "cart_level" discount "second_discount" with following properties:
      | name[en-US]        | Second Discount     |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2019-12-01 00:00:00 |
      | priority           | 1                   |
      | reduction_amount   | 1.2                 |
      | reduction_currency | usd                 |
      | taxIncluded        | false               |
      | code               |                     |
    And discount "second_discount" should have the following properties:
      | type             | cart_level |
      | priority         | 1          |
      | reduction_amount | 1.2        |
    When I update discount "first_discount" with the condition of a minimum amount:
      | minimum_amount                   | 10.0  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | false |
      | minimum_amount_shipping_included | false |
    When I update discount "second_discount" with the condition of a minimum amount:
      | minimum_amount                   | 10.0  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | false |
      | minimum_amount_shipping_included | false |
    Given I create an empty cart "test_cart" for customer "testCustomer2"
    When I add 1 product "test_product1" to the cart "test_cart"
    Then cart "test_cart" total with tax included should be '$16.00'
    And my cart "test_cart" should have the following details, without hiding auto discounts:
      | total_products | $10.00 |
      | shipping       | $7.00  |
      | total_discount | -$1.00 |
      | total          | $16.00 |
