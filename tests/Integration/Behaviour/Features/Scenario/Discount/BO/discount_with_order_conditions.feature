# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-bo-order-conditions
@restore-all-tables-before-feature
@restore-languages-after-feature
@discount-bo-order-conditions
Feature: Back Office discount order conditions
  PrestaShop allows BO users to create discounts with order-based conditions
  As a BO user
  I must be able to create discounts with order-based minimum amount conditions

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And language with iso code "en" is the default one

  Scenario: Create discount with order-based minimum amount condition
    When I create a "order_level" discount "bo_order_discount_minimum_amount" with following properties:
      | name[en-US] | BO Order Discount with Minimum Amount |
      | reduction_amount | 10.00                            |
      | reduction_currency | usd                            |
      | taxIncluded | true                                  |
    Then discount "bo_order_discount_minimum_amount" should have the following properties:
      | name[en-US] | BO Order Discount with Minimum Amount |
      | type        | order_level                           |
    When I update discount "bo_order_discount_minimum_amount" with the condition of a minimum amount:
      | minimum_amount                   | 75.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | true  |
    Then discount "bo_order_discount_minimum_amount" should have the following properties:
      | name[en-US]                      | BO Order Discount with Minimum Amount |
      | type                              | order_level                           |
      | minimum_amount                   | 75.00                               |
      | minimum_amount_currency          | usd                                 |
      | minimum_amount_tax_included      | true                                |
      | minimum_amount_shipping_included | true                                |
    And discount "bo_order_discount_minimum_amount" should have no product conditions

  Scenario: Create discount with order condition tax excluded
    When I create a "order_level" discount "bo_order_discount_tax_excluded" with following properties:
      | name[en-US] | BO Order Discount Tax Excluded |
      | reduction_amount | 5.00                        |
      | reduction_currency | usd                       |
      | taxIncluded | true                             |
    Then discount "bo_order_discount_tax_excluded" should have the following properties:
      | name[en-US] | BO Order Discount Tax Excluded |
      | type        | order_level                     |
    When I update discount "bo_order_discount_tax_excluded" with the condition of a minimum amount:
      | minimum_amount                   | 25.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | false |
      | minimum_amount_shipping_included | true  |
    Then discount "bo_order_discount_tax_excluded" should have the following properties:
      | name[en-US]                      | BO Order Discount Tax Excluded |
      | type                              | order_level                   |
      | minimum_amount                   | 25.00                         |
      | minimum_amount_currency          | usd                           |
      | minimum_amount_tax_included      | false                         |
      | minimum_amount_shipping_included | true                          |

  Scenario: Create discount with order condition without shipping
    When I create a "order_level" discount "bo_order_discount_no_shipping" with following properties:
      | name[en-US] | BO Order Discount No Shipping |
      | reduction_amount | 8.00                       |
      | reduction_currency | usd                      |
      | taxIncluded | true                            |
    Then discount "bo_order_discount_no_shipping" should have the following properties:
      | name[en-US] | BO Order Discount No Shipping |
      | type        | order_level                    |
    When I update discount "bo_order_discount_no_shipping" with the condition of a minimum amount:
      | minimum_amount                   | 60.00 |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | false |
    Then discount "bo_order_discount_no_shipping" should have the following properties:
      | name[en-US]                      | BO Order Discount No Shipping |
      | type                              | order_level                  |
      | minimum_amount                   | 60.00                        |
      | minimum_amount_currency          | usd                          |
      | minimum_amount_tax_included      | true                         |
      | minimum_amount_shipping_included | false                        |

  Scenario: Update existing discount to add order conditions
    When I create a "order_level" discount "bo_existing_discount" with following properties:
      | name[en-US] | Existing Discount |
      | reduction_amount | 15.00          |
      | reduction_currency | usd          |
      | taxIncluded | true                |
    Then discount "bo_existing_discount" should have the following properties:
      | name[en-US] | Existing Discount |
      | type        | order_level       |
    When I update discount "bo_existing_discount" with the condition of a minimum amount:
      | minimum_amount                   | 100.00 |
      | minimum_amount_currency          | usd    |
      | minimum_amount_tax_included      | true   |
      | minimum_amount_shipping_included | true   |
    Then discount "bo_existing_discount" should have the following properties:
      | name[en-US]                      | Existing Discount |
      | type                              | order_level       |
      | minimum_amount                   | 100.00            |
      | minimum_amount_currency          | usd               |
      | minimum_amount_tax_included      | true              |
      | minimum_amount_shipping_included | true              |
    # Update the discount with different order conditions
    When I update discount "bo_existing_discount" with the condition of a minimum amount:
      | minimum_amount                   | 150.00 |
      | minimum_amount_currency          | usd    |
      | minimum_amount_tax_included      | false  |
      | minimum_amount_shipping_included | false  |
    Then discount "bo_existing_discount" should have the following properties:
      | name[en-US]                      | Existing Discount |
      | type                              | order_level       |
      | minimum_amount                   | 150.00            |
      | minimum_amount_currency          | usd               |
      | minimum_amount_tax_included      | false             |
      | minimum_amount_shipping_included | false             |
