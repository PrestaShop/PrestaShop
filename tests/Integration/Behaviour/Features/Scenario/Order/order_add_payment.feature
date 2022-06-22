# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags add-payment-to-order
@restore-all-tables-before-feature
@clear-cache-before-feature
@reboot-kernel-before-feature
@restore-currencies-after-feature
@reboot-kernel-after-feature
@restore-currencies-before-scenario
@add-payment-to-order
Feature: Add payment to Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to add payment to orders from the BO

  Background:
    Given email sending is disabled
    Given shop "shop1" with name "test_shop" exists
    And the current currency is "USD"
    And country "US" is enabled
    And country "FR" is enabled
    And language "French" with locale "fr-FR" exists
    And I add new currency "currency2" with following properties:
      | iso_code         | EUR        |
      | exchange_rate    | 0.88       |
      | name             | My Euros   |
      | symbols[en-US]   | €          |
      | symbols[fr-FR]   | €          |
      | patterns[en-US]  | ¤#,##0.00  |
      | patterns[fr-FR]  | #,##0.00 ¤ |
      | is_enabled       | 1          |
      | is_unofficial    | 0          |
      | shop_association | shop1      |
    And I add new currency "currency3" with following properties:
      | iso_code         | JPY             |
      | exchange_rate    | 107.52          |
      | name             | My Japanese Yen |
      | symbols[en-US]   | ¥               |
      | symbols[fr-FR]   | ¥               |
      | patterns[en-US]  | ¤#,##0.00       |
      | patterns[fr-FR]  | #,##0.00 ¤      |
      | is_enabled       | 1               |
      | is_unofficial    | 0               |
      | shop_association | shop1           |
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

# 4 cases
#
#    Order is in default_currency + Payment is in Order currency
#       for example default = 1, order = 1, payment = 1
#       ==> NO conversion to do
#    Order is in default_currency + Payment is NOT in Order currency
#       for example default = 1, order = 1, payment = 2
#       ==> convert payment in order's currency
#    Order is NOT in default_currency + Payment is in Order currency
#       for example default = 1, order = 2, payment = 2
#       ==> NO conversion to do
#    Order is NOT in default_currency + Payment is NOT in Order currency
#       for example default = 1, order = 2, payment = 3
#       ==> As conversion rates are set regarding the default currency,
#           convert payment to default and from default to order's currency

  Scenario: Add a payment when Order is in default_currency and Payment is in Order currency
    When order "bo_order1" has 0 payments
    When I pay order "bo_order1" with the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | currency       | USD                 |
      | amount         | 6.00                |
    Then order "bo_order1" payment in first position should have the following details:
      | date           | 2019-11-26 13:56:23 |
      | paymentMethod  | Payments by check   |
      | transactionId  | test123             |
      | amount         | $6.00               |
      | employee       | Puff Daddy          |
    And order "bo_order1" should have the following details:
      | total_paid_real | 6.000000 |

  Scenario: Add a payment when Order is in default_currency and Payment is NOT in Order currency
    When order "bo_order1" has 0 payments
    When I pay order "bo_order1" with the following details:
      | date           | 2019-11-26 13:56:23 |
      | payment_method | Payments by check   |
      | transaction_id | test123             |
      | currency       | currency2           |
      | amount         | 6.00                |
    Then order "bo_order1" payment in first position should have the following details:
      | date           | 2019-11-26 13:56:23 |
      | paymentMethod  | Payments by check   |
      | transactionId  | test123             |
      | amount         | €6.00               |
      | employee       | Puff Daddy          |
    And order "bo_order1" should have the following details:
      | total_paid_real | 6.820000 |

#  Scenario: Add a payment when Order is NOT in default_currency and Payment is in Order currency
#    Given I update the cart "dummy_cart" currency to "currency2"
#    And I add order "bo_order2" with the following details:
#      | cart                | dummy_cart                 |
#      | message             | test                       |
#      | payment module name | dummy_payment              |
#      | status              | Awaiting bank wire payment |
#    When order "bo_order2" has 0 payments
#    When I pay order "bo_order2" with the following details:
#      | date           | 2019-11-26 13:56:23 |
#      | payment_method | Payments by check   |
#      | transaction_id | test123             |
#      | currency       | currency2           |
#      | amount         | 6.00                |
#    Then order "bo_order2" payments should have the following details:
#      | date           | 2019-11-26 13:56:23 |
#      | payment_method | Payments by check   |
#      | transaction_id | test123             |
#      | amount         | €6.00               |
#    And order "bo_order2" should have the following details:
#      | total_paid_real           | 6.000000 |
#
#  Scenario: Add a payment when Order is NOT in default_currency and Payment is NOT in Order currency
#    Given I update the cart "dummy_cart" currency to "currency2"
#    And I add order "bo_order2" with the following details:
#      | cart                | dummy_cart                 |
#      | message             | test                       |
#      | payment module name | dummy_payment              |
#      | status              | Awaiting bank wire payment |
#    When order "bo_order2" has 0 payments
#    When I pay order "bo_order2" with the following details:
#      | date           | 2019-11-26 13:56:23 |
#      | payment_method | Payments by check   |
#      | transaction_id | test123             |
#      | currency       | currency3           |
#      | amount         | 6.00                |
#    Then order "bo_order2" payments should have the following details:
#      | date           | 2019-11-26 13:56:23 |
#      | payment_method | Payments by check   |
#      | transaction_id | test123             |
#      | amount         | ¥6               |
#    And order "bo_order2" should have the following details:
#      | total_paid_real           | 0.050000 |
