# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-priority-compatible
@restore-all-tables-before-feature
@discount-priority-compatible
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Discount priority - Compatible discounts
    Test that compatible discounts are all applied in correct priority order

    Background:
        Given there is a customer named "testCustomer" whose email is "customer@prestashop.com"
        Given language with iso code "en" is the default one
        Given shop "shop1" with name "test_shop" exists
        And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
        And currency "usd" is the default one
        And I enable feature flag "discount"
        And there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock

    Scenario: Compatible cart discounts - order matters (amount + percent) - both codes
        Given I create an empty cart "cart1" for customer "testCustomer"
        And I add 1 product "product1" to the cart "cart1"
        # Priority 5 (applied second): Fixed $20 amount discount
        When I create a "cart_level" discount "code_amount_prio5" with following properties:
            | name[en-US]        | Code Amount Priority 5 |
            | active             | true                   |
            | priority           | 5                      |
            | valid_from         | 2025-01-01 10:00:00    |
            | valid_to           | 2026-12-31 23:59:59    |
            | code               | AMOUNT_CODE5           |
            | reduction_amount   | 20.0                   |
            | reduction_currency | usd                    |
            | taxIncluded        | true                   |
        And I set compatible types for discount "code_amount_prio5" to:
            | cart_level |
        # Priority 3 (applied first): 10% percentage discount
        When I create a "cart_level" discount "code_percent_prio3" with following properties:
            | name[en-US]       | Code Percent Priority 3 |
            | active            | true                    |
            | priority          | 3                       |
            | valid_from        | 2025-01-01 10:00:00     |
            | valid_to          | 2026-12-31 23:59:59     |
            | code              | PERCENT_CODE3           |
            | reduction_percent | 10.0                    |
        And I set compatible types for discount "code_percent_prio3" to:
            | cart_level |
        When I use a voucher "code_amount_prio5" on the cart "cart1"
        And I use a voucher "code_percent_prio3" on the cart "cart1"
        Then cart "cart1" should have 2 cart rules applied
        And discount "code_percent_prio3" is applied to my cart
        And discount "code_amount_prio5" is applied to my cart
        # If priority 3 applied first (CORRECT): $100 - 10% = $90, then $90 - $20 = $70 + $7 shipping = $77
        # If priority 5 applied first (WRONG): $100 - $20 = $80, then $80 - 10% = $72 + $7 shipping = $79
        And cart "cart1" total with tax included should be '$77.00'
        And my cart "cart1" should have the following details:
            | total_products | $100.00 |
            | shipping       | $7.00   |
            | total_discount | -$30.00 |
            | total          | $77.00  |

