@reset-database-before-feature
Feature: Adding products to an existing Order
  PrestaShop allows BO users to manage Orders in the Sell > Orders
  As a BO user
  I must be able to add product to an existing Order

  Scenario: Add product to an existing Order with free shipping and new invoice
    Given there is order with reference "XKBKNABJK"
    And there is product with reference "demo_5"
    And order with reference "XKBKNABJK" does not contain product with reference "demo_5"
    When I add 2 products with reference "demo_5", price 16 and free shipping to order "XKBKNABJK" with new invoice
    Then order "XKBKNABJK" should contain 2 products with reference "demo_5"
