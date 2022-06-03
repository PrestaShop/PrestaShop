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
      | Puff Daddy | 100            |
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
      | Puff Daddy | 100            |
    Examples:
      | index | order_status |
      | 1     | Shipped      |
      | 2     | Delivered    |
