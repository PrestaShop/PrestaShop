# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-cart-rule-quantity
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-cart-rule-quantity
Feature: Cart rule quantity of an order created from Back Office (BO)
  In order to limit how many times a voucher is used
  As a BO user
  I need the order validation to use one unit of the voucher quantity only when the order keeps it

  Background:
    Given email sending is disabled
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country

  Scenario Outline: The voucher quantity is used only by an order that is not in error or canceled
    Given there is a product in the catalog named "product<index>" with a price of 20.0 and 100 items in stock
    And there is a cart rule "voucher<index>" with following properties:
      | name[en-US]         | Voucher <index>        |
      | discount_percentage | 10                     |
      | code                | QUANTITYVOUCHER<index> |
      | total_quantity      | 2                      |
      | quantity_per_user   | 2                      |
    When I create an empty cart "dummy_cart<index>" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart<index>"
    And I add 1 products "product<index>" to the cart "dummy_cart<index>"
    And I use a voucher "voucher<index>" on the cart "dummy_cart<index>"
    And I add order "bo_order<index>" with the following details:
      | cart                | dummy_cart<index> |
      | message             |                   |
      | payment module name | dummy_payment     |
      | status              | <order_status>    |
    Then order "bo_order<index>" should have 1 cart rule
    And cart rule "voucher<index>" should have a quantity of <quantity_after>
    Examples:
      | index | order_status               | quantity_after |
      | 1     | Awaiting bank wire payment | 1              |
      | 2     | Payment accepted           | 1              |
      | 3     | Payment error              | 2              |
      | 4     | Canceled                   | 2              |

  Scenario: The voucher quantity is given back when the order validation is interrupted by an error
    Given there is a product in the catalog named "product5" with a price of 20.0 and 100 items in stock
    And there is a cart rule "voucher5" with following properties:
      | name[en-US]         | Voucher 5        |
      | discount_percentage | 10               |
      | code                | QUANTITYVOUCHER5 |
      | total_quantity      | 1                |
      | quantity_per_user   | 1                |
    When I create an empty cart "dummy_cart5" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart5"
    And I add 1 products "product5" to the cart "dummy_cart5"
    And I use a voucher "voucher5" on the cart "dummy_cart5"
    And I validate cart "dummy_cart5" with a payment module failing to create the order
    Then I should get error that the order creation failed
    And cart rule "voucher5" should have a quantity of 1

  Scenario: The voucher quantity is used when a payment module overrides the order cart rules creation
    Given there is a product in the catalog named "product6" with a price of 20.0 and 100 items in stock
    And there is a cart rule "voucher6" with following properties:
      | name[en-US]         | Voucher 6        |
      | discount_percentage | 10               |
      | code                | QUANTITYVOUCHER6 |
      | total_quantity      | 1                |
      | quantity_per_user   | 1                |
    When I create an empty cart "dummy_cart6" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart6"
    And I add 1 products "product6" to the cart "dummy_cart6"
    And I use a voucher "voucher6" on the cart "dummy_cart6"
    And I validate cart "dummy_cart6" with a payment module overriding the order cart rules creation
    Then cart rule "voucher6" should have a quantity of 0

  Scenario: The voucher quantity is used when a payment module creates the order cart rules outside validateOrder
    Given there is a product in the catalog named "product7" with a price of 20.0 and 100 items in stock
    And there is a cart rule "voucher7" with following properties:
      | name[en-US]         | Voucher 7        |
      | discount_percentage | 10               |
      | code                | QUANTITYVOUCHER7 |
      | total_quantity      | 3                |
      | quantity_per_user   | 3                |
    When I create an empty cart "dummy_cart7" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart7"
    And I add 1 products "product7" to the cart "dummy_cart7"
    And I use a voucher "voucher7" on the cart "dummy_cart7"
    And I add order "bo_order7" with the following details:
      | cart                | dummy_cart7   |
      | message             |               |
      | payment module name | dummy_payment |
      | status              | Payment accepted |
    Then cart rule "voucher7" should have a quantity of 2
    When I add the cart rules of cart "dummy_cart7" to order "bo_order7" with a payment module validating orders on its own
    Then cart rule "voucher7" should have a quantity of 1
