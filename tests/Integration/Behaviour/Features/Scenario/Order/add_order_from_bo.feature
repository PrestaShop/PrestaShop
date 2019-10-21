# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order
@reset-database-before-feature
  @aaa
Feature: Add Order from Back Office
  PrestaShop allows BO users to add new orders in the Sell > Orders > Add new Order page
  As a BO user
  I must be able to place an order for FO customers

  Background:
    Given email sending is disabled

  Scenario: Add order from Back Office with free shipping
    Given I am logged in as "test@prestashop.com" employee
    Given the current currency is "USD"
    And country "US" is enabled
    And there is customer "customer1" with email "pub@prestashop.com"
    And customer "customer1" has address in "US" country
    And the module "dummy_payment" is installed
    When I create an empty cart "cart1" for customer "customer1"
    And I add 2 products with reference "demo_13" to the cart "cart1"
    And I select "US" address as delivery and invoice address for customer "customer1" in cart "cart1"
    And I set Free shipping to the cart "cart1"
    And I add order "order1" from cart "cart1" with "dummy_payment" payment method and "Payment accepted" order status
    Then order "order1" should have 2 products in total
    And order "order1" should have free shipping
    And order "order1" should have "dummy_payment" payment method
