# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-single-customer-fo
@restore-all-tables-before-feature
@discount-single-customer-fo
Feature: Customer using discount with single customer eligibility
  PrestaShop allows customers to use discounts that are limited to them
  As a customer
  I should be able to use a discount code that is available only for me
  And I should not be able to use a discount code that is not available for me

  Background:
    Given language with iso code "en" is the default one
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a product in the catalog named "product1" with a price of 25.00 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 15.00 and 1000 items in stock
    Given there is a customer named "john_doe" whose email is "john.doe@example.com"
    And there is a customer named "jane_smith" whose email is "jane.smith@example.com"
    And there is a customer named "bob_johnson" whose email is "bob.johnson@example.com"

  Scenario: Customer can use a discount code that is available only for them
    Given I create a "cart_level" discount "vip_discount_john" with following properties:
      | name[en-US]              | VIP Discount for John |
      | active                   | true                  |
      | valid_from               | 2025-01-01 00:00:00   |
      | valid_to                 | 2026-12-31 23:59:59   |
      | code                     | JOHN_VIP_20           |
      | reduction_percent        | 20.0                  |
      | customer                 | john_doe              |
      | total_quantity           | 100                   |
      | quantity_per_user        | 1                     |
      | minimum_product_quantity | 0                     |
    And discount "vip_discount_john" should have the following properties:
      | customer          | john_doe              |
    When I create an empty cart "john_cart" for customer "john_doe"
    And I add 2 product "product1" to the cart "john_cart"
    Then cart "john_cart" total with tax included should be '$57.00'
    And my cart "john_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $57.00 |
    When I use a voucher "vip_discount_john" on the cart "john_cart"
    Then cart "john_cart" total with tax included should be '$47.00'
    And my cart "john_cart" should have the following details:
      | total_products | $50.00  |
      | shipping       | $7.00   |
      | total_discount | -$10.00 |
      | total          | $47.00  |

  Scenario: Customer cannot use a discount code that is not available for them
    Given I create a "cart_level" discount "vip_discount_jane" with following properties:
      | name[en-US]              | VIP Discount for Jane |
      | active                   | true                  |
      | valid_from               | 2025-01-01 00:00:00   |
      | valid_to                 | 2026-12-31 23:59:59   |
      | code                     | JANE_VIP_25           |
      | reduction_percent        | 25.0                  |
      | customer                 | jane_smith            |
      | total_quantity           | 100                   |
      | quantity_per_user        | 1                     |
      | minimum_product_quantity | 0                     |
    And discount "vip_discount_jane" should have the following properties:
      | customer          | jane_smith            |
    When I create an empty cart "bob_cart" for customer "bob_johnson"
    And I add 2 product "product1" to the cart "bob_cart"
    Then cart "bob_cart" total with tax included should be '$57.00'
    And my cart "bob_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $57.00 |
    # Bob tries to use Jane's discount code
    When I use a voucher "vip_discount_jane" on the cart "bob_cart"
    Then I should get cart rule validation error
    And cart "bob_cart" total with tax included should be '$57.00'
    And my cart "bob_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $57.00 |

  Scenario: Customer who owns the discount tries to use it - should succeed
    Given I create a "cart_level" discount "vip_discount_bob" with following properties:
      | name[en-US]              | VIP Discount for Bob |
      | active                   | true                 |
      | valid_from               | 2025-01-01 00:00:00  |
      | valid_to                 | 2026-12-31 23:59:59  |
      | code                     | BOB_VIP_30           |
      | reduction_percent        | 30.0                 |
      | customer                 | bob_johnson          |
      | total_quantity           | 100                  |
      | quantity_per_user        | 1                    |
      | minimum_product_quantity | 0                    |
    When I create an empty cart "bob_vip_cart" for customer "bob_johnson"
    And I add 3 product "product2" to the cart "bob_vip_cart"
    Then cart "bob_vip_cart" total with tax included should be '$52.00'
    And my cart "bob_vip_cart" should have the following details:
      | total_products | $45.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $52.00 |
    When I use a voucher "vip_discount_bob" on the cart "bob_vip_cart"
    Then cart "bob_vip_cart" total with tax included should be '$38.50'
    And my cart "bob_vip_cart" should have the following details:
      | total_products | $45.00  |
      | shipping       | $7.00   |
      | total_discount | -$13.50 |
      | total          | $38.50  |

  Scenario: Public discount can be used by any customer
    Given I create a "cart_level" discount "public_discount" with following properties:
      | name[en-US]              | Public Summer Sale  |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2026-12-31 23:59:59 |
      | code                     | SUMMER_10           |
      | reduction_percent        | 10.0                |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "public_discount" should have the following properties:
      | name[en-US]       | Public Summer Sale |
    # John can use it
    When I create an empty cart "john_public_cart" for customer "john_doe"
    And I add 2 product "product1" to the cart "john_public_cart"
    And I use a voucher "public_discount" on the cart "john_public_cart"
    Then cart "john_public_cart" total with tax included should be '$52.00'
    And my cart "john_public_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | -$5.00 |
      | total          | $52.00 |
    # Jane can use it too
    When I create an empty cart "jane_public_cart" for customer "jane_smith"
    And I add 2 product "product2" to the cart "jane_public_cart"
    And I use a voucher "public_discount" on the cart "jane_public_cart"
    Then cart "jane_public_cart" total with tax included should be '$34.00'
    And my cart "jane_public_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | -$3.00 |
      | total          | $34.00 |

  Scenario: Free shipping discount limited to a customer
    Given I create a "free_shipping" discount "free_ship_jane" with following properties:
      | name[en-US]              | Free Shipping for Jane |
      | active                   | true                   |
      | valid_from               | 2025-01-01 00:00:00    |
      | valid_to                 | 2026-12-31 23:59:59    |
      | code                     | FREESHIP_JANE          |
      | customer                 | jane_smith             |
      | total_quantity           | 100                    |
      | quantity_per_user        | 1                      |
      | minimum_product_quantity | 0                      |
    When I create an empty cart "jane_shipping_cart" for customer "jane_smith"
    And I add 1 product "product1" to the cart "jane_shipping_cart"
    Then cart "jane_shipping_cart" total with tax included should be '$32.00'
    When I use a voucher "free_ship_jane" on the cart "jane_shipping_cart"
    Then cart "jane_shipping_cart" total with tax included should be '$25.00'
    And my cart "jane_shipping_cart" should have the following details:
      | total_products | $25.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $25.00 |
    # John tries to use Jane's free shipping code
    When I create an empty cart "john_shipping_cart" for customer "john_doe"
    And I add 1 product "product1" to the cart "john_shipping_cart"
    And I use a voucher "free_ship_jane" on the cart "john_shipping_cart"
    Then I should get cart rule validation error
    And cart "john_shipping_cart" total with tax included should be '$32.00'

