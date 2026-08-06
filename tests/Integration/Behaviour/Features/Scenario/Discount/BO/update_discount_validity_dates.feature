# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags update-discount-validity-dates
@restore-all-tables-before-feature
@restore-languages-after-feature
@update-discount-validity-dates
Feature: Update discount validity dates
    PrestaShop allows BO users to set and update validity date ranges for discounts
    As a BO user
    I must be able to set a start date and expiry date for discounts
    And the system should validate that the start date is before the expiry date

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given language with iso code "en" is the default one

  Scenario: Create discount with valid date range
    When I create a "free_shipping" discount "discount_with_date_range" with following properties:
      | name[en-US] | Time Limited Promotion |
      | valid_from  | 2024-01-01 00:00:00    |
      | valid_to    | 2024-12-31 23:59:00    |
    Then discount "discount_with_date_range" should have the following properties:
      | name[en-US] | Time Limited Promotion |
      | type        | free_shipping          |
      | valid_from  | 2024-01-01 00:00:00    |
      | valid_to    | 2024-12-31 23:59:00    |

  Scenario: Update discount with new valid date range
    When I create a "free_shipping" discount "discount_to_update" with following properties:
      | name[en-US] | Promotion to Update |
      | valid_from  | 2024-01-01 00:00:00 |
      | valid_to    | 2024-06-30 23:59:00 |
    Then discount "discount_to_update" should have the following properties:
      | name[en-US] | Promotion to Update |
      | type        | free_shipping       |
      | valid_from  | 2024-01-01 00:00:00 |
      | valid_to    | 2024-06-30 23:59:00 |
    When I update discount "discount_to_update" with the following properties:
      | valid_from | 2024-07-01 00:00:00 |
      | valid_to   | 2024-12-31 23:59:00 |
    Then discount "discount_to_update" should have the following properties:
      | name[en-US] | Promotion to Update |
      | type        | free_shipping       |
      | valid_from  | 2024-07-01 00:00:00 |
      | valid_to    | 2024-12-31 23:59:00 |

  Scenario: Create discount with start date equal to expiry date
    When I create a "free_shipping" discount "same_day_discount" with following properties:
      | name[en-US] | Same Day Promotion  |
      | valid_from  | 2024-06-15 00:00:00 |
      | valid_to    | 2024-06-15 23:59:00 |
    Then discount "same_day_discount" should have the following properties:
      | name[en-US] | Same Day Promotion  |
      | type        | free_shipping       |
      | valid_from  | 2024-06-15 00:00:00 |
      | valid_to    | 2024-06-15 23:59:00 |

  Scenario: Create discount with invalid date range
    When I create a "free_shipping" discount "invalid_date_discount" with following properties:
      | name[en-US] | Invalid Date Promotion |
      | valid_from  | 2024-12-31 23:59:00    |
      | valid_to    | 2024-01-01 00:00:00    |
    Then I should get an error that start date cannot be after expiry date

  Scenario: Update discount with invalid date range
    When I create a "free_shipping" discount "discount_invalid_update" with following properties:
      | name[en-US] | Promotion with Invalid Update |
      | valid_from  | 2024-01-01 00:00:00           |
      | valid_to    | 2024-06-30 23:59:00           |
    Then discount "discount_invalid_update" should have the following properties:
      | valid_from | 2024-01-01 00:00:00 |
      | valid_to   | 2024-06-30 23:59:00 |
    When I update discount "discount_invalid_update" with the following properties:
      | valid_from | 2024-12-31 23:59:00 |
      | valid_to   | 2024-01-01 00:00:00 |
    Then I should get an error that start date cannot be after expiry date

  Scenario: Create discount with date range using different time formats
    When I create a "cart_level" discount "discount_with_time_formats" with following properties:
      | name[en-US]       | Discount with Time  |
      | valid_from        | 2024-03-15 08:30:00 |
      | valid_to          | 2024-03-15 18:00:00 |
      | reduction_percent | 15.0                |
    Then discount "discount_with_time_formats" should have the following properties:
      | name[en-US]       | Discount with Time  |
      | type              | cart_level          |
      | valid_from        | 2024-03-15 08:30:00 |
      | valid_to          | 2024-03-15 18:00:00 |
      | reduction_percent | 15.0                |

  Scenario: Create discount with long-term validity
    When I create a "cart_level" discount "long_term_discount" with following properties:
      | name[en-US]       | Long Term Promotion |
      | valid_from        | 2024-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:00 |
      | reduction_percent | 20.0                |
    Then discount "long_term_discount" should have the following properties:
      | name[en-US]       | Long Term Promotion |
      | type              | cart_level          |
      | valid_from        | 2024-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:00 |
      | reduction_percent | 20.0                |

  Scenario: Update only start date while keeping expiry date unchanged
    When I create a "free_shipping" discount "discount_update_start_only" with following properties:
      | name[en-US] | Promotion Update Start |
      | valid_from  | 2024-01-01 00:00:00    |
      | valid_to    | 2024-12-31 23:59:00    |
    Then discount "discount_update_start_only" should have the following properties:
      | valid_from | 2024-01-01 00:00:00 |
      | valid_to   | 2024-12-31 23:59:00 |
    When I update discount "discount_update_start_only" with the following properties:
      | valid_from | 2024-02-01 00:00:00 |
      | valid_to   | 2024-12-31 23:59:00 |
    Then discount "discount_update_start_only" should have the following properties:
      | valid_from | 2024-02-01 00:00:00 |
      | valid_to   | 2024-12-31 23:59:00 |

  Scenario: Update only expiry date while keeping start date unchanged
    When I create a "free_shipping" discount "discount_update_end_only" with following properties:
      | name[en-US] | Promotion Update End |
      | valid_from  | 2024-01-01 00:00:00  |
      | valid_to    | 2024-06-30 23:59:00  |
    Then discount "discount_update_end_only" should have the following properties:
      | valid_from | 2024-01-01 00:00:00 |
      | valid_to   | 2024-06-30 23:59:00 |
    When I update discount "discount_update_end_only" with the following properties:
      | valid_from | 2024-01-01 00:00:00 |
      | valid_to   | 2024-12-31 23:59:00 |
    Then discount "discount_update_end_only" should have the following properties:
      | valid_from | 2024-01-01 00:00:00 |
      | valid_to   | 2024-12-31 23:59:00 |

  Scenario: Create discount with precise minute-level timing
    When I create a "cart_level" discount "precise_timing_discount" with following properties:
      | name[en-US]       | Flash Sale          |
      | valid_from        | 2024-11-25 14:00:00 |
      | valid_to          | 2024-11-25 16:30:00 |
      | reduction_percent | 50.0                |
    Then discount "precise_timing_discount" should have the following properties:
      | name[en-US]       | Flash Sale          |
      | type              | cart_level          |
      | valid_from        | 2024-11-25 14:00:00 |
      | valid_to          | 2024-11-25 16:30:00 |
      | reduction_percent | 50.0                |

  Scenario: Create discount that never expires
    When I create a "cart_level" discount "never_expires_discount" with following properties:
      | name[en-US]          | Never Expires Promotion |
      | valid_from           | 2024-01-01 00:00:00     |
      | period_never_expires | true                    |
      | reduction_percent    | 10.0                    |
    Then discount "never_expires_discount" should have the following properties:
      | name[en-US]          | Never Expires Promotion |
      | type                 | cart_level              |
      | valid_from           | 2024-01-01 00:00:00     |
      | period_never_expires | true                    |
      | reduction_percent    | 10.0                    |

  Scenario: Update discount to never expire
    When I create a "free_shipping" discount "discount_to_make_permanent" with following properties:
      | name[en-US] | Temporary Promotion |
      | valid_from  | 2024-01-01 00:00:00 |
      | valid_to    | 2024-12-31 23:59:00 |
    Then discount "discount_to_make_permanent" should have the following properties:
      | name[en-US]          | Temporary Promotion |
      | type                 | free_shipping       |
      | valid_from           | 2024-01-01 00:00:00 |
      | valid_to             | 2024-12-31 23:59:00 |
      | period_never_expires | false               |
    When I update discount "discount_to_make_permanent" with the following properties:
      | valid_from           | 2024-01-01 00:00:00 |
      | period_never_expires | true                |
    Then discount "discount_to_make_permanent" should have the following properties:
      | name[en-US]          | Temporary Promotion |
      | type                 | free_shipping       |
      | valid_from           | 2024-01-01 00:00:00 |
      | period_never_expires | true                |

  Scenario: Update never-expiring discount to have expiration date
    When I create a "cart_level" discount "permanent_to_temporary" with following properties:
      | name[en-US]          | Permanent Discount  |
      | valid_from           | 2024-01-01 00:00:00 |
      | period_never_expires | true                |
      | reduction_percent    | 15.0                |
    Then discount "permanent_to_temporary" should have the following properties:
      | name[en-US]          | Permanent Discount  |
      | type                 | cart_level          |
      | valid_from           | 2024-01-01 00:00:00 |
      | period_never_expires | true                |
      | reduction_percent    | 15.0                |
    When I update discount "permanent_to_temporary" with the following properties:
      | valid_from           | 2024-01-01 00:00:00 |
      | valid_to             | 2024-12-31 23:59:00 |
      | period_never_expires | false               |
    Then discount "permanent_to_temporary" should have the following properties:
      | name[en-US]          | Permanent Discount  |
      | type                 | cart_level          |
      | valid_from           | 2024-01-01 00:00:00 |
      | valid_to             | 2024-12-31 23:59:00 |
      | period_never_expires | false               |
      | reduction_percent    | 15.0                |

  Scenario: Verify never-expiring discount has no expiration date
    When I create a "cart_level" discount "verify_never_expires" with following properties:
      | name[en-US]          | Long Term Discount  |
      | valid_from           | 2024-01-01 00:00:00 |
      | period_never_expires | true                |
      | reduction_percent    | 20.0                |
    Then discount "verify_never_expires" should have the following properties:
      | period_never_expires | true |
    And discount "verify_never_expires" should have no expiration date

