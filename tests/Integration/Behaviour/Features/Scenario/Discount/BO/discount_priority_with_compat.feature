# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-priority-with-compat
@restore-all-tables-before-feature
@discount-priority-with-compat
Feature: Discount Priority with Compatibility
  PrestaShop applies discounts based on priority when they are compatible
  As a BO user
  I must be able to see priority working with type compatibility

  Background:
    Given I register a enabled feature flag "discount"
    Given there is a customer named "testCustomer3" whose email is "pub3@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a product in the catalog named "test_product1" with a price of 100.0 and 100 items in stock
    And I enable feature flag "discount"

  Scenario: Compatible discounts apply in priority order
    When I create a "cart_level" discount "discount_low_priority" with following properties:
      | name[en-US]        | Low Priority 5%     |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2099-12-01 00:00:00 |
      | priority           | 2                   |
      | reduction_percent  | 5                   |
      | code               | LOW5                |
    And I set compatible types for discount "discount_low_priority" to:
      | cart_level    |
      | free_shipping |
    When I create a "cart_level" discount "discount_high_priority" with following properties:
      | name[en-US]        | High Priority 10%   |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2099-12-01 00:00:00 |
      | priority           | 1                   |
      | reduction_percent  | 10                  |
      | code               | HIGH10              |
    And I set compatible types for discount "discount_high_priority" to:
      | cart_level    |
      | free_shipping |
    Given I create an empty cart "test_cart_priority" for customer "testCustomer3"
    When I add 1 product "test_product1" to the cart "test_cart_priority"
    And I use a voucher "discount_low_priority" on the cart "test_cart_priority"
    And I use a voucher "discount_high_priority" on the cart "test_cart_priority"
    # Both should apply, total reduction = 10% + (90% * 5%) = 14.5%
    Then cart "test_cart_priority" should have 2 cart rules applied

  Scenario: Same priority (default) with different types - cart level incompatible with free shipping
    When I create a "cart_level" discount "cart_incomp" with following properties:
      | name[en-US]       | Cart Incompatible   |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2099-12-01 00:00:00 |
      | reduction_percent | 15                  |
      | code              | CARTINCOMP          |
    And I set compatible types for discount "cart_incomp" to:
      | cart_level |
    When I create a "free_shipping" discount "shipping_incomp" with following properties:
      | name[en-US] | Shipping Incompatible |
      | active      | true                  |
      | valid_from  | 2019-01-01 11:05:00   |
      | valid_to    | 2099-12-01 00:00:00   |
      | code        | SHIPINCOMP            |
    And I set compatible types for discount "shipping_incomp" to:
      | free_shipping |
    Given I create an empty cart "test_cart_type_incomp" for customer "testCustomer3"
    When I add 1 product "test_product1" to the cart "test_cart_type_incomp"
    And I use a voucher "cart_incomp" on the cart "test_cart_type_incomp"
    And I use a voucher "shipping_incomp" on the cart "test_cart_type_incomp"
    # Both have default priority (1), but cart_level (weight 2) applies before free_shipping (weight 3)
    # Since they're incompatible, shipping_incomp is rejected with error
    Then I should get an error that voucher "shipping_incomp" is not compatible with existing cart rules
    And cart "test_cart_type_incomp" should contain cart rule "cart_incomp"
    And cart "test_cart_type_incomp" should not contain cart rule "shipping_incomp"

  Scenario: Same type and priority, creation date order - compatible cart level discounts
    When I create a "cart_level" discount "cart_older_compat" with following properties:
      | name[en-US]       | Older Cart Compat   |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2099-12-01 00:00:00 |
      | reduction_percent | 10                  |
      | code              | CARTOLD             |
    And I set compatible types for discount "cart_older_compat" to:
      | cart_level    |
      | free_shipping |
    When I create a "cart_level" discount "cart_newer_compat" with following properties:
      | name[en-US]       | Newer Cart Compat   |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2099-12-01 00:00:00 |
      | reduction_percent | 5                   |
      | code              | CARTNEW             |
    And I set compatible types for discount "cart_newer_compat" to:
      | cart_level    |
      | free_shipping |
    Given I create an empty cart "test_cart_date_compat" for customer "testCustomer3"
    When I add 1 product "test_product1" to the cart "test_cart_date_compat"
    And I use a voucher "cart_older_compat" on the cart "test_cart_date_compat"
    And I use a voucher "cart_newer_compat" on the cart "test_cart_date_compat"
    # Both have same priority (1) and type (cart_level), but are explicitly compatible
    # Both should apply and stack
    Then cart "test_cart_date_compat" should have 2 cart rules applied

  Scenario: Mixed types with priorities all compatible  
    When I create a "cart_level" discount "cart_p2_compat" with following properties:
      | name[en-US]        | Cart P2             |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2099-12-01 00:00:00 |
      | priority           | 2                   |
      | reduction_percent  | 10                  |
      | code               | CARTP2MIX           |
    And I set compatible types for discount "cart_p2_compat" to:
      | product_level |
      | cart_level    |
      | free_shipping |
    When I create a "product_level" discount "product_p1" with following properties:
      | name[en-US]        | Product P1          |
      | active             | true                |
      | valid_from         | 2019-01-01 11:05:00 |
      | valid_to           | 2099-12-01 00:00:00 |
      | priority           | 1                   |
      | reduction_percent  | 15                  |
      | reduction_product  | test_product1       |
      | code               | PRODP1MIX           |
    And I set compatible types for discount "product_p1" to:
      | cart_level    |
      | product_level |
      | free_shipping |
    When I create a "free_shipping" discount "freeship_p3" with following properties:
      | name[en-US] | Free Ship P3        |
      | active      | true                |
      | valid_from  | 2019-01-01 11:05:00 |
      | valid_to    | 2099-12-01 00:00:00 |
      | priority    | 3                   |
      | code        | FREESHIPP3MIX       |
    And I set compatible types for discount "freeship_p3" to:
      | cart_level    |
      | product_level |
      | free_shipping |
    Given I create an empty cart "test_cart_mixed" for customer "testCustomer3"
    When I add 1 product "test_product1" to the cart "test_cart_mixed"
    And I use a voucher "product_p1" on the cart "test_cart_mixed"
    And I use a voucher "cart_p2_compat" on the cart "test_cart_mixed"
    And I use a voucher "freeship_p3" on the cart "test_cart_mixed"
    # All three should apply (different types, all compatible, sorted by priority)
    Then cart "test_cart_mixed" should have 3 cart rules applied
