# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-priority-incompatible-auto
@restore-all-tables-before-feature
@discount-priority-incompatible-auto
@clear-cache-before-feature
@clear-cache-after-feature
Feature: Discount priority - Incompatible auto discounts
  Test that incompatible auto discounts are resolved by priority

  Background:
    Given there is a customer named "testCustomer" whose email is "customer@prestashop.com"
    Given language with iso code "en" is the default one
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And currency "usd" is the default one
    And I enable feature flag "discount"
    And there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock

  Scenario: Incompatible auto discounts - higher priority field wins
    # Priority 5 (lower priority): $25 amount discount
    When I create a "cart_level" discount "auto_incompat_amount_prio5" with following properties:
      | name[en-US]            | Auto Incompatible Amount Priority 5 |
      | active                 | true                                |
      | priority               | 5                                   |
      | valid_from             | 2025-01-01 10:00:00                 |
      | valid_to               | 2026-12-31 23:59:59                 |
      | reduction_amount       | 25.0                                |
      | reduction_currency     | usd                                 |
      | reduction_tax_included | true                                |
      | compatible_types       | product_level                       |
    And I update discount "auto_incompat_amount_prio5" with the following properties:
      | minimum_amount                   | 1.00  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | false |
    # Priority 3 (higher priority): 15% percentage discount
    When I create a "cart_level" discount "auto_incompat_percent_prio3" with following properties:
      | name[en-US]       | Auto Incompatible Percent Priority 3 |
      | active            | true                                 |
      | priority          | 3                                    |
      | valid_from        | 2025-01-01 10:00:00                  |
      | valid_to          | 2026-12-31 23:59:59                  |
      | reduction_percent | 15.0                                 |
      | compatible_types  | product_level                        |
    And I update discount "auto_incompat_percent_prio3" with the following properties:
      | minimum_amount                   | 1.00  |
      | minimum_amount_currency          | usd   |
      | minimum_amount_tax_included      | true  |
      | minimum_amount_shipping_included | false |
    Given I create an empty cart "cart_auto_incompat" for customer "testCustomer"
    When I add 1 product "product1" to the cart "cart_auto_incompat"
    # Only priority 3 (15% percent) should be applied, not priority 5 ($25 amount)
    Then cart "cart_auto_incompat" should have 1 cart rules applied
    And discount "auto_incompat_percent_prio3" is applied to my cart
    # Only 15% discount: $100 - 15% = $85 + $7 shipping = $92
    # (If $25 amount was applied: $100 - $25 = $75 + $7 = $82 - would be wrong)
    And cart "cart_auto_incompat" total with tax included should be '$92.00'
    And my cart "cart_auto_incompat" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00   |
      | total_discount | -$15.00 |
      | total          | $92.00  |


  Scenario: An automatic free gift and an incompatible automatic cart level discount settle on a single discount
    Given there is a product in the catalog named "giftProduct" with a price of 25.0 and 1000 items in stock
    And there is a product "gift_product_ref" with name "giftProduct"
    When I create a "free_gift" discount "auto_free_gift" with following properties:
      | name[en-US]  | Auto Free Gift      |
      | active       | true                |
      | valid_from   | 2025-01-01 10:00:00 |
      | valid_to     | 2026-12-31 23:59:59 |
      | gift_product | gift_product_ref    |
    And I create a "cart_level" discount "auto_cart_percent" with following properties:
      | name[en-US]       | Auto Cart Percent   |
      | active            | true                |
      | valid_from        | 2025-01-01 10:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | reduction_percent | 10.0                |
    And I create an empty cart "cart_gift_incompat" for customer "testCustomer"
    When I add 1 product "product1" to the cart "cart_gift_incompat"
    Then cart "cart_gift_incompat" should have 1 cart rules applied
    And discount "auto_cart_percent" is applied to my cart
    And my cart "cart_gift_incompat" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00   |
      | total_discount | -$10.00 |
      | total          | $97.00  |
