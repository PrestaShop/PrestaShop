# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-customer-groups-fo
@restore-all-tables-before-feature
@restore-languages-after-feature
@discount-customer-groups-fo
Feature: Customer using discount with customer groups eligibility
  PrestaShop allows customers to use discounts that are limited to their customer group
  As a customer
  I should be able to use a discount code that is available for my customer group
  And I should not be able to use a discount code that is not available for my customer group

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And currency "usd" is the default one
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    And there is a product in the catalog named "product1" with a price of 25.00 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 15.00 and 1000 items in stock
    And I create a customer group "group1" with the following details:
      | name[en-US]             | VIP Members       |
      | name[fr-FR]             | Membres VIP       |
      | reduction               | 0.0               |
      | displayPriceTaxExcluded | false             |
      | showPrice               | true              |
      | shopIds                 | shop1             |
    And I create a customer group "group2" with the following details:
      | name[en-US]             | Premium Members   |
      | name[fr-FR]             | Membres Premium   |
      | reduction               | 0.0               |
      | displayPriceTaxExcluded | false             |
      | showPrice               | true              |
      | shopIds                 | shop1             |
    And there is a customer named "john_vip" whose email is "john.vip@example.com"
    And there is a customer named "jane_premium" whose email is "jane.premium@example.com"
    And there is a customer named "bob_regular" whose email is "bob.regular@example.com"
    And customer "john_vip" belongs to group "group1"
    And customer "jane_premium" belongs to group "group2"

  Scenario: Customer in eligible group can use the discount
    Given I create a "cart_level" discount "vip_discount" with following properties:
      | name[en-US]              | VIP discount        |
      | name[fr-FR]              | Réduction VIP       |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2026-12-31 23:59:59 |
      | code                     | VIP_GROUP_20        |
      | reduction_percent        | 20.0                |
      | customer_groups          | group1              |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "vip_discount" should have the following properties:
      | customer_groups   | group1           |
    When I create an empty cart "john_cart" for customer "john_vip"
    And I add 2 product "product1" to the cart "john_cart"
    Then cart "john_cart" total with tax included should be '$57.00'
    And my cart "john_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $57.00 |
    When I use a voucher "vip_discount" on the cart "john_cart"
    Then cart "john_cart" total with tax included should be '$47.00'
    And my cart "john_cart" should have the following details:
      | total_products | $50.00  |
      | shipping       | $7.00   |
      | total_discount | -$10.00 |
      | total          | $47.00  |

  Scenario: Customer not in eligible group cannot use the discount
    Given I create a "cart_level" discount "vip_only_discount" with following properties:
      | name[en-US]              | VIP exclusive       |
      | name[fr-FR]              | Exclusif VIP        |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2026-12-31 23:59:59 |
      | code                     | VIP_EXCLUSIVE       |
      | reduction_percent        | 25.0                |
      | customer_groups          | group1              |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "vip_only_discount" should have the following properties:
      | customer_groups   | group1           |
    # Bob (regular customer, not in VIP group) tries to use VIP discount
    When I create an empty cart "bob_cart" for customer "bob_regular"
    And I add 2 product "product1" to the cart "bob_cart"
    Then cart "bob_cart" total with tax included should be '$57.00'
    And my cart "bob_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $57.00 |
    When I use a voucher "vip_only_discount" on the cart "bob_cart"
    Then I should get cart rule validation error
    And cart "bob_cart" total with tax included should be '$57.00'
    And my cart "bob_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $57.00 |

  Scenario: Customer in one of multiple eligible groups can use the discount
    Given I create a "cart_level" discount "multi_group_discount" with following properties:
      | name[en-US]              | Special promotion        |
      | name[fr-FR]              | Promotion spéciale       |
      | active                   | true                     |
      | valid_from               | 2025-01-01 00:00:00      |
      | valid_to                 | 2026-12-31 23:59:59      |
      | code                     | SPECIAL_30               |
      | reduction_percent        | 30.0                     |
      | customer_groups          | group1,group2            |
      | total_quantity           | 100                      |
      | quantity_per_user        | 1                        |
      | minimum_product_quantity | 0                        |
    # John (VIP group) can use it
    When I create an empty cart "john_multi_cart" for customer "john_vip"
    And I add 3 product "product2" to the cart "john_multi_cart"
    Then cart "john_multi_cart" total with tax included should be '$52.00'
    And my cart "john_multi_cart" should have the following details:
      | total_products | $45.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $52.00 |
    When I use a voucher "multi_group_discount" on the cart "john_multi_cart"
    Then cart "john_multi_cart" total with tax included should be '$38.50'
    And my cart "john_multi_cart" should have the following details:
      | total_products | $45.00  |
      | shipping       | $7.00   |
      | total_discount | -$13.50 |
      | total          | $38.50  |
    # Jane (Premium group) can also use it
    When I create an empty cart "jane_multi_cart" for customer "jane_premium"
    And I add 2 product "product1" to the cart "jane_multi_cart"
    Then cart "jane_multi_cart" total with tax included should be '$57.00'
    When I use a voucher "multi_group_discount" on the cart "jane_multi_cart"
    Then cart "jane_multi_cart" total with tax included should be '$42.00'
    And my cart "jane_multi_cart" should have the following details:
      | total_products | $50.00  |
      | shipping       | $7.00   |
      | total_discount | -$15.00 |
      | total          | $42.00  |
    # Bob (not in either group) cannot use it
    When I create an empty cart "bob_multi_cart" for customer "bob_regular"
    And I add 2 product "product1" to the cart "bob_multi_cart"
    And I use a voucher "multi_group_discount" on the cart "bob_multi_cart"
    Then I should get cart rule validation error
    And cart "bob_multi_cart" total with tax included should be '$57.00'

  Scenario: Free shipping discount limited to customer groups
    Given I create a "free_shipping" discount "free_ship_premium" with following properties:
      | name[en-US]              | Free shipping Premium      |
      | name[fr-FR]              | Livraison gratuite Premium |
      | active                   | true                       |
      | valid_from               | 2025-01-01 00:00:00        |
      | valid_to                 | 2026-12-31 23:59:59        |
      | code                     | FREESHIP_PREMIUM           |
      | customer_groups          | group2                     |
      | total_quantity           | 100                        |
      | quantity_per_user        | 1                          |
      | minimum_product_quantity | 0                          |
    # Jane (Premium member) can use it
    When I create an empty cart "jane_shipping_cart" for customer "jane_premium"
    And I add 1 product "product1" to the cart "jane_shipping_cart"
    Then cart "jane_shipping_cart" total with tax included should be '$32.00'
    When I use a voucher "free_ship_premium" on the cart "jane_shipping_cart"
    Then cart "jane_shipping_cart" total with tax included should be '$25.00'
    And my cart "jane_shipping_cart" should have the following details:
      | total_products | $25.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $25.00 |
    # John (VIP, not Premium) tries to use Premium free shipping
    When I create an empty cart "john_shipping_cart" for customer "john_vip"
    And I add 1 product "product1" to the cart "john_shipping_cart"
    And I use a voucher "free_ship_premium" on the cart "john_shipping_cart"
    Then I should get cart rule validation error
    And cart "john_shipping_cart" total with tax included should be '$32.00'

  Scenario: Public discount can be used by any customer regardless of group
    Given I create a "cart_level" discount "public_discount" with following properties:
      | name[en-US]              | Summer sale         |
      | name[fr-FR]              | Soldes d'été        |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2026-12-31 23:59:59 |
      | code                     | SUMMER_10           |
      | reduction_percent        | 10.0                |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "public_discount" should have the following properties:
      | name[en-US]       | Summer sale  |
      | name[fr-FR]       | Soldes d'été |
    # John (VIP) can use it
    When I create an empty cart "john_public_cart" for customer "john_vip"
    And I add 2 product "product1" to the cart "john_public_cart"
    And I use a voucher "public_discount" on the cart "john_public_cart"
    Then cart "john_public_cart" total with tax included should be '$52.00'
    And my cart "john_public_cart" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.00  |
      | total_discount | -$5.00 |
      | total          | $52.00 |
    # Jane (Premium) can use it too
    When I create an empty cart "jane_public_cart" for customer "jane_premium"
    And I add 2 product "product2" to the cart "jane_public_cart"
    And I use a voucher "public_discount" on the cart "jane_public_cart"
    Then cart "jane_public_cart" total with tax included should be '$34.00'
    And my cart "jane_public_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | -$3.00 |
      | total          | $34.00 |
    # Bob (Regular, no special group) can use it as well
    When I create an empty cart "bob_public_cart" for customer "bob_regular"
    And I add 1 product "product1" to the cart "bob_public_cart"
    And I use a voucher "public_discount" on the cart "bob_public_cart"
    Then cart "bob_public_cart" total with tax included should be '$29.50'
    And my cart "bob_public_cart" should have the following details:
      | total_products | $25.00 |
      | shipping       | $7.00  |
      | total_discount | -$2.50 |
      | total          | $29.50 |

