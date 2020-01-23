# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-refund
@reset-database-before-feature
Feature: Refund Order from Back Office (BO)
  In order to refund orders for FO customers
  As a BO user
  I need to be able to refund orders from the BO

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
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add 1 products "Mug Today is a good day" to the cart "dummy_cart"

  @order-refund
  Scenario: Partial refund of products
    Given I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress |
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And order "bo_order1" should contain 1 products "Mug Today is a good day"
    And there are 298 "Mug The best is yet to come" in stock
    And there are 299 "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order1" without restock on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 1                        | 10.5   |
      | Mug Today is a good day     | 1                        | 3.5    |
    Then "bo_order1" has following refunds:
      | amount   | 14   |
      | shipping | null |
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And order "bo_order1" should contain 1 products "Mug Today is a good day"
    And order "bo_order1" should contain 1 refunded products "Mug The best is yet to come"
    And order "bo_order1" should contain 1 refunded products "Mug Today is a good day"
    And there are 298 "Mug The best is yet to come" in stock
    And there are 299 "Mug Today is a good day" in stock

  @order-refund
  Scenario: Partial refund of products with restock
    Given I add order "bo_order2" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Processing in progress |
    And order "bo_order2" should contain 2 products "Mug The best is yet to come"
    And order "bo_order2" should contain 1 products "Mug Today is a good day"
    And there are 296 "Mug The best is yet to come" in stock
    And there are 298 "Mug Today is a good day" in stock
    When I issue a partial refund on "bo_order2" with restock on following products:
      | product_name                | quantity                 | amount |
      | Mug The best is yet to come | 2                        | 7.5    |
    Then "bo_order2" has following refunds:
      | amount   | 7.5  |
      | shipping | null |
    And order "bo_order2" should contain 2 products "Mug The best is yet to come"
    And order "bo_order2" should contain 1 products "Mug Today is a good day"
    And order "bo_order2" should contain 2 refunded products "Mug The best is yet to come"
    And order "bo_order2" should contain 0 refunded products "Mug Today is a good day"
    And there are 298 "Mug The best is yet to come" in stock
    And there are 298 "Mug Today is a good day" in stock
