# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-priority-compatible-auto
@restore-all-tables-before-feature
@discount-priority-compatible-auto
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Discount priority - Compatible auto discounts
    Test that compatible auto discounts are all applied in correct priority order

    Background:
        Given there is a customer named "testCustomer" whose email is "customer@prestashop.com"
        Given language with iso code "en" is the default one
        Given shop "shop1" with name "test_shop" exists
        And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
        And currency "usd" is the default one
        And I enable feature flag "discount"
        And there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock

    Scenario: Compatible auto discounts - order matters (amount + percent)
        # Priority 5 (applied second): Fixed $20 amount discount
        When I create a "cart_level" discount "auto_amount_prio5" with following properties:
            | name[en-US]        | Auto Amount Priority 5 |
            | active             | true                   |
            | priority           | 5                      |
            | valid_from         | 2025-01-01 10:00:00    |
            | valid_to           | 2026-12-31 23:59:59    |
            | reduction_amount   | 20.0                   |
            | reduction_currency | usd                    |
            | taxIncluded        | true                   |
        And I update discount "auto_amount_prio5" with the condition of a minimum amount:
            | minimum_amount                   | 1.00  |
            | minimum_amount_currency          | usd   |
            | minimum_amount_tax_included      | true  |
            | minimum_amount_shipping_included | false |
        And I set compatible types for discount "auto_amount_prio5" to:
            | cart_level |
        # Priority 3 (applied first): 10% percentage discount
        When I create a "cart_level" discount "auto_percent_prio3" with following properties:
            | name[en-US]       | Auto Percent Priority 3 |
            | active            | true                    |
            | priority          | 3                       |
            | valid_from        | 2025-01-01 10:00:00     |
            | valid_to          | 2026-12-31 23:59:59     |
            | reduction_percent | 10.0                    |
        And I update discount "auto_percent_prio3" with the condition of a minimum amount:
            | minimum_amount                   | 1.00  |
            | minimum_amount_currency          | usd   |
            | minimum_amount_tax_included      | true  |
            | minimum_amount_shipping_included | false |
        And I set compatible types for discount "auto_percent_prio3" to:
            | cart_level |
        Given I create an empty cart "cart_auto" for customer "testCustomer"
        When I add 1 product "product1" to the cart "cart_auto"
        Then cart "cart_auto" should have 2 cart rules applied
        And discount "auto_percent_prio3" is applied to my cart
        And discount "auto_amount_prio5" is applied to my cart
        # If priority 3 applied first (CORRECT): $100 - 10% = $90, then $90 - $20 = $70 + $7 shipping = $77
        # If priority 5 applied first (WRONG): $100 - $20 = $80, then $80 - 10% = $72 + $7 shipping = $79
        And cart "cart_auto" total with tax included should be '$77.00'
        And my cart "cart_auto" should have the following details:
            | total_products | $100.00 |
            | shipping       | $7.00   |
            | total_discount | -$30.00 |
            | total          | $77.00  |

