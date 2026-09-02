# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-validity-date-range
@restore-all-tables-before-feature
@discount-validity-date-range
Feature: Discount validity date range in FO
  Check that discount validity is enforced by valid_from and valid_to:
  - if current date is in the range, the discount can be used
  - if valid_to is in the past, the discount can't be used
  - if valid_to is null, the discount can be used

  Background:
    Given there is a customer named "testCustomer" whose email is "customer@prestashop.com"
    Given language with iso code "en" is the default one
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And currency "usd" is the default one
    And I enable feature flag "discount"
    And there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock

  Scenario: Discount with validTo in the future
    When I create a "cart_level" discount "VALID_TO_FUTURE" with following properties:
      | name[en-US]       | validTo in future |
      | active            | true                    |
      | valid_from        | 2026-02-24 00:00:00    |
      | valid_to          | 2028-12-31 23:59:59    |
      | code              | VALID_TO_FUTURE         |
      | reduction_percent | 10.0                   |
    Given I create an empty cart "cart_order" for customer "testCustomer"
    When I add 1 product "product1" to the cart "cart_order"
    And I use a voucher "VALID_TO_FUTURE" on the cart "cart_order"
    Then discount "VALID_TO_FUTURE" is applied to my cart
    And cart "cart_order" total with tax included should be '$97.00'
    And my cart "cart_order" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00   |
      | total_discount | -$10.00 |
      | total          | $97.00  |

  Scenario: Discount with validTo in the past
    When I create a "cart_level" discount "VALID_TO_PAST" with following properties:
      | name[en-US]       | validTo in past |
      | active            | true             |
      | valid_from        | 2026-01-01 00:00:00 |
      | valid_to          | 2026-01-31 00:00:00 |
      | code              | VALID_TO_PAST     |
      | reduction_percent | 15.0             |
    Given I create an empty cart "cart_order" for customer "testCustomer"
    When I add 1 product "product1" to the cart "cart_order"
    When I use a voucher "VALID_TO_PAST" on the cart "cart_order"
    Then I should get cart rule validation error
    And cart "cart_order" total with tax included should be '$107.00'
    And my cart "cart_order" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00   |
      | total_discount | $0.00   |
      | total          | $107.00 |

  Scenario: Discount where validTo is null 
    When I create a "cart_level" discount "VALID_TO_NULL" with following properties:
      | name[en-US]        | validTo is null |
      | active             | true                   |
      | valid_from         | 2026-01-01 00:00:00   |
      | period_never_expires | true                 |
      | code               | VALID_TO_NULL         |
      | reduction_percent  | 20.0                  |
    Given I create an empty cart "cart_order" for customer "testCustomer"
    When I add 1 product "product1" to the cart "cart_order"
    And I use a voucher "VALID_TO_NULL" on the cart "cart_order"
    Then discount "VALID_TO_NULL" is applied to my cart
    And cart "cart_order" total with tax included should be '$87.00'
    And my cart "cart_order" should have the following details:
      | total_products | $100.00 |
      | shipping       | $7.00   |
      | total_discount | -$20.00 |
      | total          | $87.00  |
