# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-cart-rule-exhausted
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-cart-rule-exhausted
Feature: Editing an order whose voucher ran out of uses
  A voucher applied to an order was redeemed when the order was placed, so re-validating it against
  today's remaining quantity is wrong: CartRule::checkValidity() exempts an already ordered cart from
  the availability checks. Without that exemption OrderAmountUpdater::updateOrderCartRules() finds the
  rule missing from the recomputed cart and hard deletes the order_cart_rule row, silently changing the
  total of a paid order.

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And there is a cart rule "orderVoucher" with following properties:
      | name[en-US]     | Order voucher |
      | description     | Order voucher |
      | priority        | 1             |
      | code            | ORDER_VOUCHER |
      | discount_amount | 5             |
      | total_quantity  | 10            |
    And I use a voucher "orderVoucher" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |

  Scenario: A redeemed voucher survives an address change even after it runs out of uses
    Given order "bo_order1" should have 1 cart rules
    And I create customer "otherCustomer" with following details:
      | firstName | testFirstName       |
      | lastName  | testLastName        |
      | email     | other@mailexample.eu|
      | password  | secret              |
    And I add new address to customer "otherCustomer" with following details:
      | Address alias | other-address               |
      | First name    | testFirstName               |
      | Last name     | testLastName                |
      | Address       | Work address st. 1234567890 |
      | City          | Birmingham                  |
      | Country       | United States               |
      | State         | Alabama                     |
      | Postal code   | 12345                       |
    When I update quantity for cart rule "orderVoucher" to 0
    And I change order "bo_order1" shipping address to "other-address"
    Then order "bo_order1" should have 1 cart rules
