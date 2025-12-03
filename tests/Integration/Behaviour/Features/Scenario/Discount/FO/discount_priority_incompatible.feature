# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-priority-incompatible
@restore-all-tables-before-feature
@discount-priority-incompatible
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Discount priority - Incompatible discounts
    Test that incompatible discounts are resolved by priority

    Background:
        Given there is a customer named "testCustomer" whose email is "customer@prestashop.com"
        Given language with iso code "en" is the default one
        Given shop "shop1" with name "test_shop" exists
        And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
        And currency "usd" is the default one
        And I enable feature flag "discount"
        And there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock

    Scenario: Incompatible cart discounts - higher priority field wins - both codes
        Given I create an empty cart "cart1" for customer "testCustomer"
        And I add 1 product "product1" to the cart "cart1"
        # Priority 5 (lower priority): $15 amount discount
        When I create a "cart_level" discount "incompat_amount_prio5" with following properties:
            | name[en-US]        | Incompatible Amount Priority 5 |
            | active             | true                           |
            | priority           | 5                              |
            | valid_from         | 2025-01-01 10:00:00            |
            | valid_to           | 2026-12-31 23:59:59            |
            | code               | INCOMPAT_AMT5                  |
            | reduction_amount   | 15.0                           |
            | reduction_currency | usd                            |
            | taxIncluded        | true                           |
        And I set compatible types for discount "incompat_amount_prio5" to:
            | product_level |
        # Priority 3 (higher priority): 20% percentage discount
        When I create a "cart_level" discount "incompat_percent_prio3" with following properties:
            | name[en-US]       | Incompatible Percent Priority 3 |
            | active            | true                            |
            | priority          | 3                               |
            | valid_from        | 2025-01-01 10:00:00             |
            | valid_to          | 2026-12-31 23:59:59             |
            | code              | INCOMPAT_PCT3                   |
            | reduction_percent | 20.0                            |
        And I set compatible types for discount "incompat_percent_prio3" to:
            | product_level |
        When I use a voucher "incompat_amount_prio5" on the cart "cart1"
        And I use a voucher "incompat_percent_prio3" on the cart "cart1"
        # Priority 3 (20%) should win and replace priority 5 ($15)
        Then cart "cart1" should have 1 cart rules applied
        And discount "incompat_percent_prio3" is applied to my cart
        And discount code "incompat_amount_prio5" is not applied to my cart
        # Only 20% discount: $100 - 20% = $80 + $7 shipping = $87
        # (If $15 amount was applied: $100 - $15 = $85 + $7 = $92 - would be wrong)
        And cart "cart1" total with tax included should be '$87.00'
        And my cart "cart1" should have the following details:
            | total_products | $100.00 |
            | shipping       | $7.00   |
            | total_discount | -$20.00 |
            | total          | $87.00  |

