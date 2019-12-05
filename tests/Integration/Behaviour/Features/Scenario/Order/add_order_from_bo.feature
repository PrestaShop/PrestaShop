# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order
@reset-database-before-feature
Feature: Add Order from Back Office
  PrestaShop allows BO users to add new orders in the Sell > Orders > Add new Order page
  As a BO user
  I must be able to place an order for FO customers

  Background:
    Given email sending is disabled

  Scenario: Add order from Back Office with free shipping
    Given I am logged in as "test@prestashop.com" employee
    And the current currency is "USD"
    And country "US" is enabled
    And there is customer "customer_for_free_shipping" with email "pub@prestashop.com"
    And customer "customer_for_free_shipping" has address in "US" country
    And the module "dummy_payment" is installed
    When I create an empty cart "dummy_cart" for customer "customer_for_free_shipping"
    And I add 2 products with reference "demo_13" to the cart "dummy_cart"
    And I select "US" address as delivery and invoice address for customer "customer_for_free_shipping" in cart "dummy_cart"
    And I set Free shipping to the cart "dummy_cart"
    And I add order "bo_order_for_free_shipping" from cart "dummy_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then order "bo_order_for_free_shipping" should have 2 products in total
    And order "bo_order_for_free_shipping" should have free shipping
    And order "bo_order_for_free_shipping" should have "dummy_payment" payment method
