# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-usage-limits-fo
@discount-usage-limits-fo
@restore-all-tables-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
Feature: Customer using discount with usage limits in FO
  PrestaShop allows customers to use discounts that are limited by usage per user or total.
  As a customer
  I should be able to use a discount code that is limited by usage per user or total
  And I should not be able to use a discount code that has reached its usage limit

  Background:
    # The new rules for product level are only computed when the feature flag is enabled
    Given I enable feature flag "discount"
    Given language with iso code "en" is the default one
    Given shop "shop1" with name "test_shop" exists
    And the module "dummy_payment" is installed
    And country "US" is enabled
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a product in the catalog named "product1" with a price of 25.00 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 15.00 and 1000 items in stock
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And there is a customer named "testCustomer2" whose email is "pub2@prestashop.com"
    And I add new address to customer "testCustomer2" with following details:
      | Address alias | test-address                |
      | First name    | testFirstName               |
      | Last name     | testLastName                |
      | Address       | Work address st. 1234567890 |
      | City          | Birmingham                  |
      | Country       | United States               |
      | State         | Alabama                     |
      | Postal code   | 12345                       |
    And customer "testCustomer2" has address in "US" country

  Scenario: Discount Free shipping that is available only one time for all customers
    Given I create a "free_shipping" discount "vip_discount10" with following properties:
      | name[en-US]              | VIP Discount        |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2099-12-31 23:59:59 |
      | code                     | VIP_FREE_SHIPPING   |
      | total_quantity           | 1                   |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "vip_discount10" should have the following properties:
      | total_quantity    | 1 |
      | quantity_per_user | 1 |
    # First Johh's cart + discount with order placed
    When I create an empty cart "john_cart10" for customer "testCustomer"
    And I add 2 product "product1" to the cart "john_cart10"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "john_cart10"
    Then my cart "john_cart10" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.42  |
      | total_discount | $0.00  |
      | total          | $60.42 |
    When I use a voucher "vip_discount10" on the cart "john_cart10"
    Then my cart "john_cart10" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.42  |
      | total_discount | -$7.00 |
      | total          | $53.00 |
    And I add order "john_order" from cart "john_cart10" with "dummy_payment" payment method and "Payment accepted" order status
    # Now Jane's cart + discount
    When I create an empty cart "jane_cart10" for customer "testCustomer2"
    And I add 2 product "product1" to the cart "jane_cart10"
    And I select "US" address as delivery and invoice address for customer "testCustomer2" in cart "jane_cart10"
    And I use a voucher "vip_discount10" on the cart "jane_cart10"
    Then I should get an error that the discount is invalid

  Scenario: Discount product level that is available only one time for all customers
    Given I create a "product_level" discount "vip_discount11" with following properties:
      | name[en-US]              | VIP Discount        |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2099-12-31 23:59:59 |
      | code                     | VIP_PRODUCT_LEVEL   |
      | reduction_percent        | 10.0                |
      | reduction_product        | product1            |
      | total_quantity           | 1                   |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "vip_discount11" should have the following properties:
      | total_quantity    | 1 |
      | quantity_per_user | 1 |
    # First Johh's cart + discount with order placed
    When I create an empty cart "john_cart12" for customer "testCustomer"
    And I add 2 product "product1" to the cart "john_cart12"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "john_cart12"
    Then my cart "john_cart12" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.42  |
      | total_discount | $0.00  |
      | total          | $60.42 |
    When I use a voucher "vip_discount11" on the cart "john_cart12"
    Then my cart "john_cart12" should have the following details:
      | total_products | $50.00 |
      | shipping       | $7.42  |
      | total_discount | -$5.00 |
      | total          | $55.12 |
    And I add order "john_order" from cart "john_cart12" with "dummy_payment" payment method and "Payment accepted" order status
    # Now Jane's cart + discount
    When I create an empty cart "jane_cart12" for customer "testCustomer2"
    And I add 2 product "product1" to the cart "jane_cart12"
    And I select "US" address as delivery and invoice address for customer "testCustomer2" in cart "jane_cart12"
    And I use a voucher "vip_discount11" on the cart "jane_cart12"
    Then I should get an error that the discount is invalid

  Scenario: Discount that is available only one time per user
    Given I create a "cart_level" discount "vip_discount" with following properties:
      | name[en-US]              | VIP Discount        |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2099-12-31 23:59:59 |
      | code                     | VIP_20              |
      | reduction_percent        | 20.0                |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "vip_discount" should have the following properties:
      | total_quantity    | 100 |
      | quantity_per_user | 1   |
    # First Johh's cart + discount with order placed
    When I create an empty cart "john_cart" for customer "testCustomer"
    And I add 2 product "product1" to the cart "john_cart"
    Then cart "john_cart" total with tax included should be '$50.00'
    When I use a voucher "vip_discount" on the cart "john_cart"
    Then cart "john_cart" total with tax included should be '$40.00'
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "john_cart"
    And I add order "john_order" from cart "john_cart" with "dummy_payment" payment method and "Payment accepted" order status
    # Second John's cart with discount already used before
    When I create an empty cart "john_cart2" for customer "testCustomer"
    And I add 2 product "product1" to the cart "john_cart2"
    Then cart "john_cart2" total with tax included should be '$50.00'
    When I use a voucher "vip_discount" on the cart "john_cart2"
    Then I should get an error that the discount is invalid
    # Now Jane's cart + discount
    When I create an empty cart "jane_cart" for customer "testCustomer2"
    And I add 2 product "product1" to the cart "jane_cart"
    Then cart "jane_cart" total with tax included should be '$57.00'
    When I use a voucher "vip_discount" on the cart "jane_cart"
    Then cart "jane_cart" total with tax included should be '$47.00'

  Scenario: Discount that is available only one time for all customers
    Given I create a "cart_level" discount "vip_discount2" with following properties:
      | name[en-US]              | VIP Discount        |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2099-12-31 23:59:59 |
      | code                     | VIP_20_2            |
      | reduction_percent        | 20.0                |
      | total_quantity           | 1                   |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "vip_discount2" should have the following properties:
      | total_quantity    | 1 |
      | quantity_per_user | 1 |
    # First Johh's cart + discount with order placed
    When I create an empty cart "john_cart" for customer "testCustomer"
    And I add 2 product "product1" to the cart "john_cart"
    Then cart "john_cart" total with tax included should be '$50.00'
    When I use a voucher "vip_discount2" on the cart "john_cart"
    Then cart "john_cart" total with tax included should be '$40.00'
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "john_cart"
    And I add order "john_order" from cart "john_cart" with "dummy_payment" payment method and "Payment accepted" order status
    # Now Jane's cart + discount
    When I create an empty cart "jane_cart" for customer "testCustomer2"
    And I add 2 product "product1" to the cart "jane_cart"
    Then cart "jane_cart" total with tax included should be '$57.00'
    When I use a voucher "vip_discount2" on the cart "jane_cart"
    Then I should get an error that the discount is invalid

  Scenario: Discount that is available with no limit in total usage
    Given I create a "cart_level" discount "vip_discount4" with following properties:
      | name[en-US]              | VIP Discount        |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2099-12-31 23:59:59 |
      | code                     | VIP_20_4            |
      | reduction_percent        | 20.0                |
      | total_quantity           | null                |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    And discount "vip_discount4" should have the following properties:
      | total_quantity    | null |
      | quantity_per_user | 1    |
    # First Johh's cart + discount with order placed
    And I create an empty cart "john_cart3" for customer "testCustomer"
    And I add 2 product "product1" to the cart "john_cart3"
    Then cart "john_cart3" total with tax included should be '$50.00'
    When I use a voucher "vip_discount4" on the cart "john_cart3"
    Then cart "john_cart3" total with tax included should be '$40.00'
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "john_cart3"
    And I add order "john_order1" from cart "john_cart3" with "dummy_payment" payment method and "Payment accepted" order status
    # Second John's cart with discount already used before
    When I create an empty cart "john_cart2" for customer "testCustomer"
    And I add 2 product "product1" to the cart "john_cart2"
    Then cart "john_cart2" total with tax included should be '$50.00'
    When I use a voucher "vip_discount4" on the cart "john_cart2"
    Then I should get an error that the discount is invalid
    # Now Jane's cart + discount
    When I create an empty cart "jane_cart" for customer "testCustomer2"
    And I add 2 product "product1" to the cart "jane_cart"
    Then cart "jane_cart" total with tax included should be '$57.00'
    When I use a voucher "vip_discount4" on the cart "jane_cart"
    Then cart "jane_cart" total with tax included should be '$47.00'

  Scenario: Discount that is available with no limit per user
    Given I create a "cart_level" discount "vip_discount3" with following properties:
      | name[en-US]              | VIP Discount        |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2099-12-31 23:59:59 |
      | code                     | VIP_20_3            |
      | reduction_percent        | 20.0                |
      | total_quantity           | 10                  |
      | quantity_per_user        | null                |
      | minimum_product_quantity | 0                   |
    And discount "vip_discount3" should have the following properties:
      | total_quantity    | 10   |
      | quantity_per_user | null |
    # First Johh's cart + discount with order placed
    And I create an empty cart "john_cart1" for customer "testCustomer"
    And I add 2 product "product1" to the cart "john_cart1"
    Then cart "john_cart1" total with tax included should be '$50.00'
    When I use a voucher "vip_discount3" on the cart "john_cart1"
    Then cart "john_cart1" total with tax included should be '$40.00'
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "john_cart1"
    And I add order "john_order1" from cart "john_cart1" with "dummy_payment" payment method and "Payment accepted" order status
    # Second John's cart with discount already used before
    When I create an empty cart "john_cart2" for customer "testCustomer"
    And I add 2 product "product1" to the cart "john_cart2"
    Then cart "john_cart2" total with tax included should be '$50.00'
    When I use a voucher "vip_discount3" on the cart "john_cart2"
    Then cart "john_cart2" total with tax included should be '$40.00'
