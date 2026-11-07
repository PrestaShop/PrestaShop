# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-message-draft
@restore-all-tables-before-feature
@order-message-draft
@clear-cache-before-feature
Feature: Order message written while preparing an order in the back office
  As a BO user
  I want the message I typed before creating the order to be the order message

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 1 products "Mug The best is yet to come" to the cart "dummy_cart"

  Scenario: The stored draft is attached to the order created from the cart
    When I store the order message "Please gift wrap it" on the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | Please gift wrap it        |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" must have customer message with content "Please gift wrap it"

  Scenario: A draft the order creation would refuse is refused when it is stored
    When I store the order message "Hello <b>there</b>" on the cart "dummy_cart"
    Then I should get an error that the order message is invalid

  Scenario: Storing a new draft replaces the previous one
    When I store the order message "Leave it at the door" on the cart "dummy_cart"
    And I store the order message "Please gift wrap it" on the cart "dummy_cart"
    And I add order "bo_order1" from cart "dummy_cart" with "dummy_payment" payment method and "Awaiting bank wire payment" order status
    Then order "bo_order1" must have customer message with content "Please gift wrap it"
    And the messages of order "bo_order1" should be, from the most recent:
      | author   |
      | employee |
      | customer |

  Scenario: Clearing the draft removes it from the cart
    When I store the order message "Leave it at the door" on the cart "dummy_cart"
    And I store the order message "" on the cart "dummy_cart"
    And I add order "bo_order1" from cart "dummy_cart" with "dummy_payment" payment method and "Awaiting bank wire payment" order status
    Then the messages of order "bo_order1" should be, from the most recent:
      | author   |
      | employee |

  Scenario: The message of a cart that already has an order cannot be changed
    When I store the order message "Please gift wrap it" on the cart "dummy_cart"
    And I add order "bo_order1" from cart "dummy_cart" with "dummy_payment" payment method and "Awaiting bank wire payment" order status
    And I store the order message "Changed afterwards" on the cart "dummy_cart"
    Then I should get an error that the cart already has an order
