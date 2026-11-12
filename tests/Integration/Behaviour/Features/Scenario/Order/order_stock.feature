# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-stock
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-stock
Feature: Stock management of order from Back Office (BO)
  In order to manage product stock quantities
  As a BO user
  I need to update stock quantities of ordered products

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists

  Scenario Outline: Check no stock movement is added by new order without status flagged as shipped
    Given there is a product in the catalog named "product<index>" with a price of 17.0 and 100 items in stock
    When I create an empty cart "dummy_cart<index>" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart<index>"
    And I add 2 products "product<index>" to the cart "dummy_cart<index>"
    And I add order "bo_order<index>" with the following details:
      | cart                | dummy_cart<index> |
      | message             | test<index>       |
      | payment module name | dummy_payment     |
      | status              | <order_status>    |
    Then product "product<index>" last stock movements should be:
      | employee   | delta_quantity |
      | Puffin Mummy | 100            |
    Examples:
      | index | order_status                         |
      | 1     | Awaiting check payment               |
      | 2     | Payment accepted                     |
      | 3     | Processing in progress               |
      | 4     | Canceled                             |
      | 5     | Refunded                             |
      | 6     | Payment error                        |
      | 7     | On backorder (paid)                  |
      | 8     | On backorder (not paid)              |
      | 9     | Awaiting bank wire payment           |
      | 10    | Remote payment accepted              |
      | 11    | Awaiting Cash On Delivery validation |

  Scenario Outline: Check stock movement is added by new order with status flagged as shipped
    Given there is a product in the catalog named "product<index>" with a price of 17.0 and 100 items in stock
    When I create an empty cart "dummy_cart<index>" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart<index>"
    And I add 2 products "product<index>" to the cart "dummy_cart<index>"
    And I add order "bo_order<index>" with the following details:
      | cart                | dummy_cart<index> |
      | message             | test<index>       |
      | payment module name | dummy_payment     |
      | status              | <order_status>    |
    Then product "product<index>" last stock movements should be:
      | employee   | delta_quantity |
      |            | -2             |
      | Puffin Mummy | 100            |
    Examples:
      | index | order_status |
      | 1     | Shipped      |
      | 2     | Delivered    |

  Scenario: Cancelling an order does not restock quantities that a partial refund already restocked
    Given there is a product in the catalog named "product_restock_then_cancel" with a price of 17.0 and 100 items in stock
    When I create an empty cart "cart_restock_then_cancel" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "cart_restock_then_cancel"
    And I add 1 products "product_restock_then_cancel" to the cart "cart_restock_then_cancel"
    And I add order "order_restock_then_cancel" with the following details:
      | cart                | cart_restock_then_cancel |
      | message             | test                     |
      | payment module name | dummy_payment            |
      | status              | Payment accepted         |
    Then the available stock for product "product_restock_then_cancel" should be 99
    When I issue a partial refund on "order_restock_then_cancel" with restock with credit slip without voucher on following products:
      | product_name                | quantity | amount |
      | product_restock_then_cancel | 1        | 17.0   |
    Then the available stock for product "product_restock_then_cancel" should be 100
    And product "product_restock_then_cancel" in order "order_restock_then_cancel" has following details:
      | product_quantity            | 1 |
      | product_quantity_refunded   | 1 |
      | product_quantity_reinjected | 1 |
    When I update order "order_restock_then_cancel" status to "Canceled"
    Then the available stock for product "product_restock_then_cancel" should be 100
    When I update order "order_restock_then_cancel" status to "Payment accepted"
    Then the available stock for product "product_restock_then_cancel" should be 100

  Scenario: Cancelling an order still restocks the quantity a refund without restock left out of stock
    Given there is a product in the catalog named "product_refund_no_restock" with a price of 17.0 and 100 items in stock
    When I create an empty cart "cart_refund_no_restock" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "cart_refund_no_restock"
    And I add 1 products "product_refund_no_restock" to the cart "cart_refund_no_restock"
    And I add order "order_refund_no_restock" with the following details:
      | cart                | cart_refund_no_restock |
      | message             | test                   |
      | payment module name | dummy_payment          |
      | status              | Payment accepted       |
    Then the available stock for product "product_refund_no_restock" should be 99
    When I update order "order_refund_no_restock" status to "Delivered"
    And I issue a partial refund on "order_refund_no_restock" without restock with credit slip without voucher on following products:
      | product_name             | quantity | amount |
      | product_refund_no_restock | 1        | 17.0   |
    Then the available stock for product "product_refund_no_restock" should be 99
    And product "product_refund_no_restock" in order "order_refund_no_restock" has following details:
      | product_quantity            | 1 |
      | product_quantity_reinjected | 0 |
    When I update order "order_refund_no_restock" status to "Canceled"
    Then the available stock for product "product_refund_no_restock" should be 100
