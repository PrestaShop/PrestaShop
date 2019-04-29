@reset-database-before-feature
Feature: Add Order from Back Office
  PrestaShop allows BO users to add new orders in the Sell > Orders > Add new Order page
  As a BO user
  I must be able to place an order for FO customers

  Scenario: Add order from Back Office with free shipping
    Given there is a customer named "Customer1" whose email is "customer1@prestashop.com"
    And I create an empty cart for customer "Customer1"
    And I add "2" products with reference "demo_13" to the cart
    And I set Free shipping to cart
