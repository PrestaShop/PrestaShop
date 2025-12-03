# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-priority-creation-date
@restore-all-tables-before-feature
@discount-priority-creation-date
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Discount priority - Creation date ordering
  Test that when type and priority field are same, older discount has higher priority

  Background:
    Given there is a customer named "testCustomer" whose email is "customer@prestashop.com"
    Given language with iso code "en" is the default one
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And currency "usd" is the default one
    And I enable feature flag "discount"
    And there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock

  Scenario: Compatible discounts - same type, same priority, creation date determines order
    # Create FIRST discount (older) - will have earlier date_add
    When I create a "cart_level" discount "older_percent" with following properties:
      | name[en-US]       | Older Percent Discount |
      | active            | true                   |
      | priority          | 1                      |
      | valid_from        | 2025-01-01 10:00:00    |
      | valid_to          | 2026-12-31 23:59:59    |
      | code              | OLDER_PCT              |
      | reduction_percent | 15.0                   |
    And I set compatible types for discount "older_percent" to:
      | cart_level |
    # Create SECOND discount (newer) - will have later date_add
    When I create a "cart_level" discount "newer_amount" with following properties:
      | name[en-US]        | Newer Amount Discount |
      | active             | true                  |
      | priority           | 1                     |
      | valid_from         | 2025-01-01 10:00:00   |
      | valid_to           | 2026-12-31 23:59:59   |
      | code               | NEWER_AMT             |
      | reduction_amount   | 25.0                  |
      | reduction_currency | usd                   |
      | taxIncluded        | true                  |
    And I set compatible types for discount "newer_amount" to:
      | cart_level |
    Given I create an empty cart "cart_date" for customer "testCustomer"
    When I add 1 product "product1" to the cart "cart_date"
    And I use a voucher "older_percent" on the cart "cart_date"
    And I use a voucher "newer_amount" on the cart "cart_date"
    Then cart "cart_date" should have 2 cart rules applied
    And discount "older_percent" is applied to my cart
    And discount "newer_amount" is applied to my cart
    # PROOF: Older discount (15%) applied FIRST, newer ($25) applied SECOND
    # Correct order (older first): $100 - 15% = $85, then $85 - $25 = $60 + $7 = $67
    # Wrong order (newer first): $100 - $25 = $75, then $75 - 15% = $63.75 + $7 = $70.75
    And cart "cart_date" total with tax included should be '$67.00'
    And my cart "cart_date" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00   |
      | total_discount | -$40.00 |
      | total          | $67.00  |

  Scenario: Incompatible discounts - same type, same priority, older wins
    # Create FIRST discount (older) - $35 amount
    When I create a "cart_level" discount "older_amount_incompat" with following properties:
      | name[en-US]        | Older Amount Incompatible |
      | active             | true                      |
      | priority           | 1                         |
      | valid_from         | 2025-01-01 10:00:00       |
      | valid_to           | 2026-12-31 23:59:59       |
      | code               | OLDER_AMT_IC              |
      | reduction_amount   | 35.0                      |
      | reduction_currency | usd                       |
      | taxIncluded        | true                      |
    And I set compatible types for discount "older_amount_incompat" to:
      | free_shipping |
    # Create SECOND discount (newer) - 25% percentage
    When I create a "cart_level" discount "newer_percent_incompat" with following properties:
      | name[en-US]       | Newer Percent Incompatible |
      | active            | true                       |
      | priority          | 1                          |
      | valid_from        | 2025-01-01 10:00:00        |
      | valid_to          | 2026-12-31 23:59:59        |
      | code              | NEWER_PCT_IC               |
      | reduction_percent | 25.0                       |
    And I set compatible types for discount "newer_percent_incompat" to:
      | free_shipping |
    Given I create an empty cart "cart_date_incompat" for customer "testCustomer"
    When I add 1 product "product1" to the cart "cart_date_incompat"
    And I use a voucher "older_amount_incompat" on the cart "cart_date_incompat"
    And I use a voucher "newer_percent_incompat" on the cart "cart_date_incompat"
    # Older discount should win when both have same type and priority
    Then I should get an error that the discount is invalid
    Then cart "cart_date_incompat" should have 1 cart rules applied
    And discount "older_amount_incompat" is applied to my cart
    # PROOF: Only older discount ($35) applied: $100 - $35 = $65 + $7 = $72
    # (If newer 25% was applied: $100 - 25% = $75 + $7 = $82 - would prove wrong)
    And cart "cart_date_incompat" total with tax included should be '$72.00'
    And my cart "cart_date_incompat" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00   |
      | total_discount | -$35.00 |
      | total          | $72.00  |

