# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-priority-order
@restore-all-tables-before-feature
@discount-priority-order
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Discount priority - Verify application order
  Test that discounts are applied in the correct priority order using amount-based discounts

  Background:
    Given there is a customer named "testCustomer" whose email is "customer@prestashop.com"
    Given language with iso code "en" is the default one
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And currency "usd" is the default one
    And I enable feature flag "discount"
    And there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock

  Scenario: Verify priority field determines order - amount-based discounts
    Given I create an empty cart "cart_order" for customer "testCustomer"
    And I add 1 product "product1" to the cart "cart_order"
    # Create discount with priority 5 - fixed $20 off
    When I create a "cart_level" discount "amount_prio5" with following properties:
      | name[en-US]        | Amount Priority 5   |
      | active             | true                |
      | priority           | 5                   |
      | valid_from         | 2025-01-01 10:00:00 |
      | valid_to           | 2026-12-31 23:59:59 |
      | code               | AMOUNT5             |
      | reduction_amount   | 20.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    And I set compatible types for discount "amount_prio5" to:
      | cart_level |
    # Create discount with priority 3 - 10% off
    When I create a "cart_level" discount "percent_prio3" with following properties:
      | name[en-US]       | Percent Priority 3  |
      | active            | true                |
      | priority          | 3                   |
      | valid_from        | 2025-01-01 10:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | PERCENT3            |
      | reduction_percent | 10.0                |
    And I set compatible types for discount "percent_prio3" to:
      | cart_level |
    When I use a voucher "amount_prio5" on the cart "cart_order"
    And I use a voucher "percent_prio3" on the cart "cart_order"
    Then cart "cart_order" should have 2 cart rules applied
    And discount "percent_prio3" is applied to my cart
    And discount "amount_prio5" is applied to my cart
    # MATHEMATICAL PROOF OF CORRECT ORDER:
    # If priority 3 first (CORRECT): $100 - 10% = $90, then $90 - $20 = $70 + $7 shipping = $77
    # If priority 5 first (WRONG): $100 - $20 = $80, then $80 - 10% = $72 + $7 shipping = $79
    And cart "cart_order" total with tax included should be '$77.00'
    And my cart "cart_order" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00   |
      | total_discount | -$30.00 |
      | total          | $77.00  |

  Scenario: Verify only higher priority discount is applied when incompatible
    Given I create an empty cart "cart_single" for customer "testCustomer"
    And I add 1 product "product1" to the cart "cart_single"
    # Priority 5 (lower priority): $30 amount discount
    When I create a "cart_level" discount "incompat_amount30" with following properties:
      | name[en-US]        | Incompatible Amount $30 |
      | active             | true                    |
      | priority           | 5                       |
      | valid_from         | 2025-01-01 10:00:00     |
      | valid_to           | 2026-12-31 23:59:59     |
      | code               | INCOMPAT_AMT30          |
      | reduction_amount   | 30.0                    |
      | reduction_currency | usd                     |
      | taxIncluded        | true                    |
    And I set compatible types for discount "incompat_amount30" to:
      | free_shipping |
    # Priority 3 (higher priority): 15% percentage discount
    When I create a "cart_level" discount "incompat_percent15" with following properties:
      | name[en-US]       | Incompatible Percent 15% |
      | active            | true                     |
      | priority          | 3                        |
      | valid_from        | 2025-01-01 10:00:00      |
      | valid_to          | 2026-12-31 23:59:59      |
      | code              | INCOMPAT_PCT15           |
      | reduction_percent | 15.0                     |
    And I set compatible types for discount "incompat_percent15" to:
      | free_shipping |
    When I use a voucher "incompat_amount30" on the cart "cart_single"
    And I use a voucher "incompat_percent15" on the cart "cart_single"
    Then cart "cart_single" should have 1 cart rules applied
    And discount "incompat_percent15" is applied to my cart
    And discount code "incompat_amount30" is not applied to my cart
    # PROOF: Only 15% discount applied: $100 - 15% = $85 + $7 shipping = $92
    # (If $30 amount was applied: $100 - $30 = $70 + $7 = $77 - would prove wrong discount)
    And cart "cart_single" total with tax included should be '$92.00'
    And my cart "cart_single" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00   |
      | total_discount | -$15.00 |
      | total          | $92.00  |

