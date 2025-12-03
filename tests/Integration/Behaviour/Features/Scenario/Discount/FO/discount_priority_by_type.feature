# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-priority-by-type
@restore-all-tables-before-feature
@discount-priority-by-type
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Discount priority by type
    Test that discounts are sorted by their TYPE priority (product > cart > shipping)

    Background:
        Given there is a customer named "testCustomer" whose email is "customer@prestashop.com"
        Given language with iso code "en" is the default one
        Given shop "shop1" with name "test_shop" exists
        And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
        And currency "usd" is the default one
        And I enable feature flag "discount"
        And there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock

    @restore-cart-rules-before-scenario
    Scenario: Different discount types - Product level applies before Cart level
        # PRODUCT_LEVEL Discount - Priority 5 (lower field priority): 10% OFF product
        When I create a "product_level" discount "product_10pct_prio5" with following properties:
            | name[en-US]       | Product 10% OFF     |
            | active            | true                |
            | priority          | 5                   |
            | valid_from        | 2025-01-01 10:00:00 |
            | valid_to          | 2026-12-31 23:59:59 |
            | code              | PROD10              |
            | reduction_percent | 10.0                |
            | reduction_product | -1                  |
        And I set compatible types for discount "product_10pct_prio5" to:
            | cart_level |

        # CART_LEVEL Discount - Priority 1 (higher field priority): $20 OFF cart
        When I create a "cart_level" discount "cart_20dollar_prio1" with following properties:
            | name[en-US]        | Cart \$20 OFF       |
            | active             | true                |
            | priority           | 1                   |
            | valid_from         | 2025-01-01 10:00:00 |
            | valid_to           | 2026-12-31 23:59:59 |
            | code               | CART20              |
            | reduction_amount   | 20.0                |
            | reduction_currency | usd                 |
            | taxIncluded        | true                |
        And I set compatible types for discount "cart_20dollar_prio1" to:
            | product_level |

        # Create cart and add products BEFORE applying discounts
        Given I create an empty cart "cart_type_test" for customer "testCustomer"
        When I add 1 product "product1" to the cart "cart_type_test"

        # Check initial cart state
        And cart "cart_type_test" total with tax included should be '$107.00'

        # Apply both discounts
        When I use a voucher "product_10pct_prio5" on the cart "cart_type_test"
        When I use a voucher "cart_20dollar_prio1" on the cart "cart_type_test"

        # Expected order by TYPE priority (not field priority):
        # 1. product_10pct_prio5 (Product level - type priority 1) - applied FIRST
        # 2. cart_20dollar_prio1 (Cart level - type priority 2, but field priority 1) - applied SECOND
        #
        # Calculation with proper type priority:
        # Product discount: $100 - 10% = $90 (applied at product level)
        # Cart discount: $90 - $20 = $70
        # Shipping: $7
        # Total: $77

        # PROOF: If cart discount applied first (WRONG): $100 - $20 = $80, then $80 - 10% = $72 + $7 = $79
        #        If product discount applied first (CORRECT): $100 - 10% = $90, then $90 - $20 = $70 + $7 = $77

        # Note: Product discounts show differently in cart - total products remains original price
        # The discount is shown separately in total_discount
        And my cart "cart_type_test" should have the following details:
            | shipping | $7.00  |
            | total    | $77.00 |

    @restore-cart-rules-before-scenario
    Scenario: Complex type mixing - Product + Cart + Free shipping with different priorities
        # Create multiple discounts of different types to test comprehensive type priority

        # PRODUCT_LEVEL Discount 1 - Priority 3: 5% OFF product
        When I create a "product_level" discount "product_5pct_prio3" with following properties:
            | name[en-US]       | Product 5% OFF (Prio 3) |
            | active            | true                    |
            | priority          | 3                       |
            | valid_from        | 2025-01-01 10:00:00     |
            | valid_to          | 2026-12-31 23:59:59     |
            | code              | PROD5                   |
            | reduction_percent | 5.0                     |
            | reduction_product | -1                      |
        And I set compatible types for discount "product_5pct_prio3" to:
            | cart_level |

        # CART_LEVEL Discount 1 - Priority 2: $10 OFF cart (higher field priority)
        When I create a "cart_level" discount "cart_10dollar_prio2" with following properties:
            | name[en-US]        | Cart \$10 OFF (Prio 2) |
            | active             | true                   |
            | priority           | 2                      |
            | valid_from         | 2025-01-01 10:00:00    |
            | valid_to           | 2026-12-31 23:59:59    |
            | code               | CART10                 |
            | reduction_amount   | 10.0                   |
            | reduction_currency | usd                    |
            | taxIncluded        | true                   |
        And I set compatible types for discount "cart_10dollar_prio2" to:
            | product_level |
            | cart_level    |

        # CART_LEVEL Discount 2 - Priority 4: $5 OFF cart (lower field priority, same type)
        When I create a "cart_level" discount "cart_5dollar_prio4" with following properties:
            | name[en-US]        | Cart \$5 OFF (Prio 4) |
            | active             | true                  |
            | priority           | 4                     |
            | valid_from         | 2025-01-01 10:00:00   |
            | valid_to           | 2026-12-31 23:59:59   |
            | code               | CART5                 |
            | reduction_amount   | 5.0                   |
            | reduction_currency | usd                   |
            | taxIncluded        | true                  |
        And I set compatible types for discount "cart_5dollar_prio4" to:
            | product_level |
            | cart_level    |

        # FREE_SHIPPING Discount - Priority 1 (highest field priority)
        When I create a "free_shipping" discount "free_ship_prio1" with following properties:
            | name[en-US] | Free Shipping (Prio 1) |
            | active      | true                   |
            | priority    | 1                      |
            | valid_from  | 2025-01-01 10:00:00    |
            | valid_to    | 2026-12-31 23:59:59    |
            | code        | FREESHIP               |
        And I set compatible types for discount "free_ship_prio1" to:
            | product_level |
            | cart_level    |

        # Create cart
        Given I create an empty cart "cart_complex_types" for customer "testCustomer"
        When I add 1 product "product1" to the cart "cart_complex_types"
        And cart "cart_complex_types" total with tax included should be '$107.00'

        # Apply all discounts
        When I use a voucher "product_5pct_prio3" on the cart "cart_complex_types"
        When I use a voucher "cart_10dollar_prio2" on the cart "cart_complex_types"
        When I use a voucher "cart_5dollar_prio4" on the cart "cart_complex_types"

        # Try to add free shipping - may be incompatible with existing discounts
        When I use a voucher "free_ship_prio1" on the cart "cart_complex_types"
        Then I should get an error that the discount is invalid

        # Expected order by TYPE priority, then FIELD priority:
        # 1. product_5pct_prio3 (Product level - type 1, field 3) - applied FIRST
        # 2. cart_10dollar_prio2 (Cart level - type 2, field 2) - applied SECOND
        # 3. cart_5dollar_prio4 (Cart level - type 2, field 4) - applied THIRD
        # 4. free_ship_prio1 - rejected as incompatible
        #
        # Calculation with proper order:
        # Step 1 - Product discount: $100 - 5% = $95
        # Step 2 - Cart discount ($10): $95 - $10 = $85
        # Step 3 - Cart discount ($5): $85 - $5 = $80
        # Shipping: $7 (not waived)
        # Total: $87

        And my cart "cart_complex_types" should have the following details:
            | shipping | $7.00  |
            | total    | $87.00 |

