@reset-database-before-feature
Feature: Add Order from Back Office
  PrestaShop allows BO users to add new orders in the Sell > Orders > Add new Order page
  As a BO user
  I must be able to place an order for FO customers

  Scenario: Add order from Back Office with free shipping
    Given I am logged in as "test@prestashop.com" employee
    And there is customer with email "pub@prestashop.com"
    And customer has address in "US" country
    And the module "ps_checkpayment" is installed
    When I create an empty cart for customer with email "pub@prestashop.com"
    And I add "2" products with reference "demo_13" to the cart
    And I select "US" address as delivery and invoice address
    And I set Free shipping to cart
    And I place order with "ps_checkpayment" payment method and "Payment accepted" order status
#    Then order from cart should be created
