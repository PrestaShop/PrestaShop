# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-compatibility-fo
@restore-all-tables-before-feature
@discount-compatibility-fo
@clear-cache-before-feature
@clear-cache-after-feature

Feature: Discount compatibility in cart
  PrestaShop validates discount compatibility when applying multiple discounts to a cart
  As a customer
  I should only be able to apply compatible discounts together

  Background:
    Given there is a customer named "testCustomer" whose email is "customer@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And currency "usd" is the default one
    And I enable feature flag "discount"
    And there is a product in the catalog named "product1" with a price of 100.0 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 50.0 and 1000 items in stock
    And there is a product in the catalog named "gift_product" with a price of 25.0 and 1000 items in stock

  Scenario: Apply two compatible cart level discounts successfully
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I create a "cart_level" discount "cart_discount_10" with following properties:
      | name[en-US]       | Cart 10% Off        |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | CART10              |
      | reduction_percent | 10.0                |
    And I set compatible types for discount "cart_discount_10" to:
      | cart_level |
    When I create a "cart_level" discount "cart_discount_5" with following properties:
      | name[en-US]       | Cart 5% Off         |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | CART5               |
      | reduction_percent | 5.0                 |
    And I set compatible types for discount "cart_discount_5" to:
      | cart_level |
    And I add 1 product "product1" to the cart "dummy_cart"
    When I use a voucher "cart_discount_10" on the cart "dummy_cart"
    And I use a voucher "cart_discount_5" on the cart "dummy_cart"
    Then cart "dummy_cart" should have 2 cart rules applied

  Scenario: Apply two incompatible cart level discounts and get an error
    Given I create an empty cart "dummy_cart2" for customer "testCustomer"
    When I create a "cart_level" discount "cart_discount_20" with following properties:
      | name[en-US]       | Cart 20% Off        |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | CART20              |
      | reduction_percent | 20.0                |
    And I set compatible types for discount "cart_discount_20" to:
      | free_shipping |
    When I create a "cart_level" discount "cart_discount_15" with following properties:
      | name[en-US]       | Cart 15% Off        |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | CART15              |
      | reduction_percent | 15.0                |
    And I set compatible types for discount "cart_discount_15" to:
      | product_level |
    And I add 1 product "product1" to the cart "dummy_cart2"
    When I use a voucher "cart_discount_20" on the cart "dummy_cart2"
    And I use a voucher "cart_discount_15" on the cart "dummy_cart2"
    Then I should get an error that the discount is invalid

  Scenario: Apply compatible cart level and free shipping discounts
    Given I create an empty cart "dummy_cart3" for customer "testCustomer"
    When I create a "cart_level" discount "cart_discount_combo" with following properties:
      | name[en-US]       | Cart Combo          |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | CARTCOMBO           |
      | reduction_percent | 10.0                |
    And I set compatible types for discount "cart_discount_combo" to:
      | free_shipping |
    When I create a "free_shipping" discount "free_ship" with following properties:
      | name[en-US] | Free Shipping       |
      | active      | true                |
      | valid_from  | 2025-01-01 00:00:00 |
      | valid_to    | 2026-12-31 23:59:59 |
      | code        | FREESHIP            |
    And I set compatible types for discount "free_ship" to:
      | cart_level |
    And I add 1 product "product1" to the cart "dummy_cart3"
    When I use a voucher "cart_discount_combo" on the cart "dummy_cart3"
    And I use a voucher "free_ship" on the cart "dummy_cart3"
    Then cart "dummy_cart3" should have 2 cart rules applied

  Scenario: Apply compatible product level and cart level discounts
    Given I create an empty cart "dummy_cart4" for customer "testCustomer"
    When I create a "product_level" discount "product_discount" with following properties:
      | name[en-US]       | Product 15% Off     |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | PROD15              |
      | reduction_percent | 15.0                |
      | reduction_product | product1            |
    And I set compatible types for discount "product_discount" to:
      | cart_level |
    When I create a "cart_level" discount "cart_for_product" with following properties:
      | name[en-US]       | Cart with Product   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | CARTWPROD           |
      | reduction_percent | 5.0                 |
    And I set compatible types for discount "cart_for_product" to:
      | product_level |
    And I add 1 product "product1" to the cart "dummy_cart4"
    When I use a voucher "product_discount" on the cart "dummy_cart4"
    And I use a voucher "cart_for_product" on the cart "dummy_cart4"
    Then cart "dummy_cart4" should have 2 cart rules applied

  Scenario: Apply exclusive discount prevents other discounts
    Given I create an empty cart "dummy_cart5" for customer "testCustomer"
    When I create a "cart_level" discount "exclusive_50" with following properties:
      | name[en-US]       | Exclusive 50% Off   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | EXCLUSIVE50         |
      | reduction_percent | 50.0                |
    And I set compatible types for discount "exclusive_50" to:
      |  |
    When I create a "cart_level" discount "cart_any" with following properties:
      | name[en-US]       | Cart Any            |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | CARTANY             |
      | reduction_percent | 10.0                |
    And I set compatible types for discount "cart_any" to:
      | cart_level |
    And I add 1 product "product1" to the cart "dummy_cart5"
    When I use a voucher "exclusive_50" on the cart "dummy_cart5"
    And I use a voucher "cart_any" on the cart "dummy_cart5"
    Then I should get an error that the discount is invalid

  Scenario: Apply three compatible discounts together
    Given I create an empty cart "dummy_cart6" for customer "testCustomer"
    When I create a "cart_level" discount "multi_cart" with following properties:
      | name[en-US]       | Multi Cart 5%       |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | MULTI5              |
      | reduction_percent | 5.0                 |
    And I set compatible types for discount "multi_cart" to:
      | product_level |
      | free_shipping |
    When I create a "product_level" discount "multi_product" with following properties:
      | name[en-US]       | Multi Product 10%   |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | MULTIPROD10         |
      | reduction_percent | 10.0                |
      | reduction_product | product1            |
    And I set compatible types for discount "multi_product" to:
      | cart_level    |
      | free_shipping |
    When I create a "free_shipping" discount "multi_ship" with following properties:
      | name[en-US] | Multi Free Ship     |
      | active      | true                |
      | valid_from  | 2025-01-01 00:00:00 |
      | valid_to    | 2026-12-31 23:59:59 |
      | code        | MULTISHIP           |
    And I set compatible types for discount "multi_ship" to:
      | cart_level    |
      | product_level |
    And I add 1 product "product1" to the cart "dummy_cart6"
    When I use a voucher "multi_cart" on the cart "dummy_cart6"
    And I use a voucher "multi_product" on the cart "dummy_cart6"
    And I use a voucher "multi_ship" on the cart "dummy_cart6"
    Then cart "dummy_cart6" should have 3 cart rules applied

  Scenario: Apply incompatible discount to cart with existing discount
    Given I create an empty cart "dummy_cart7" for customer "testCustomer"
    When I create a "cart_level" discount "first_discount" with following properties:
      | name[en-US]       | First Discount      |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | FIRST               |
      | reduction_percent | 10.0                |
    And I set compatible types for discount "first_discount" to:
      | free_shipping |
    When I create a "order_level" discount "order_discount" with following properties:
      | name[en-US]       | Order Discount      |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | ORDERDISC           |
      | reduction_percent | 15.0                |
    And I set compatible types for discount "order_discount" to:
      | free_gift |
    And I add 1 product "product1" to the cart "dummy_cart7"
    When I use a voucher "first_discount" on the cart "dummy_cart7"
    And I use a voucher "order_discount" on the cart "dummy_cart7"
    Then I should get an error that the discount is invalid

  Scenario: Apply free gift with compatible cart level discount
    Given I create an empty cart "dummy_cart8" for customer "testCustomer"
    When I create a "free_gift" discount "gift_promo" with following properties:
      | name[en-US]  | Gift Promotion      |
      | active       | true                |
      | valid_from   | 2025-01-01 00:00:00 |
      | valid_to     | 2026-12-31 23:59:59 |
      | code         | GIFTPROMO           |
      | gift_product | gift_product        |
    And I set compatible types for discount "gift_promo" to:
      | cart_level |
    When I create a "cart_level" discount "cart_with_gift" with following properties:
      | name[en-US]       | Cart with Gift      |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | CARTWGIFT           |
      | reduction_percent | 10.0                |
    And I set compatible types for discount "cart_with_gift" to:
      | free_gift |
    And I add 1 product "product1" to the cart "dummy_cart8"
    When I use a voucher "gift_promo" on the cart "dummy_cart8"
    And I use a voucher "cart_with_gift" on the cart "dummy_cart8"
    Then cart "dummy_cart8" should have 2 cart rules applied

  Scenario: Apply product discount with incompatible order level discount
    Given I create an empty cart "dummy_cart9" for customer "testCustomer"
    When I create a "order_level" discount "order_20" with following properties:
      | name[en-US]       | Order 20% Off       |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | ORDER20             |
      | reduction_percent | 20.0                |
    And I set compatible types for discount "order_20" to:
      | cart_level |
    When I create a "product_level" discount "product_25" with following properties:
      | name[en-US]       | Product 25% Off     |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | PROD25              |
      | reduction_percent | 25.0                |
      | reduction_product | product1            |
    And I set compatible types for discount "product_25" to:
      | free_shipping |
    And I add 1 product "product1" to the cart "dummy_cart9"
    And I use a voucher "product_25" on the cart "dummy_cart9"
    When I use a voucher "order_20" on the cart "dummy_cart9"
    Then I should get an error that the discount is invalid

  Scenario: Apply amount-based discount with compatible percentage discount
    Given I create an empty cart "dummy_cart10" for customer "testCustomer"
    When I create a "cart_level" discount "amount_discount" with following properties:
      | name[en-US]        | Amount 20 Off       |
      | active             | true                |
      | valid_from         | 2025-01-01 00:00:00 |
      | valid_to           | 2026-12-31 23:59:59 |
      | code               | AMOUNT20            |
      | reduction_amount   | 20.0                |
      | reduction_currency | usd                 |
      | taxIncluded        | true                |
    And I set compatible types for discount "amount_discount" to:
      | cart_level |
    When I create a "cart_level" discount "percent_discount" with following properties:
      | name[en-US]       | Percent 5% Off      |
      | active            | true                |
      | valid_from        | 2025-01-01 00:00:00 |
      | valid_to          | 2026-12-31 23:59:59 |
      | code              | PERCENT5            |
      | reduction_percent | 5.0                 |
    And I set compatible types for discount "percent_discount" to:
      | cart_level |
    And I add 2 products "product1" to the cart "dummy_cart10"
    When I use a voucher "amount_discount" on the cart "dummy_cart10"
    And I use a voucher "percent_discount" on the cart "dummy_cart10"
    Then cart "dummy_cart10" should have 2 cart rules applied
