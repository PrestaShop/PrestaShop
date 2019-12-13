@reset-database-before-feature
Feature: Add product to an existing Order
  In order to manage customer order products
  As a BO user
  I should be able to add product to an existing Order

  Background:
    Given email sending is disabled
    And the current currency is "USD"

  Scenario: Add product to an existing Order with free shipping and new invoice
    Given there is order with reference "XKBKNABJK"
    And there is product with reference "demo_5"
    And order with reference "XKBKNABJK" does not contain product with reference "demo_5"
    When I add 2 products with reference "demo_5", price 16 and free shipping to order "XKBKNABJK" with new invoice
    Then order "XKBKNABJK" should contain 2 products with reference "demo_5"
    # id_product = 4 is same as referenced by demo_5
    When I add products to order with new invoice with the following products details:
    | amount | id_order | price | free_shipping | id_product |
    | 2      | 1        | 16    | true          | 4          |
    Then order "XKBKNABJK" should contain 4 products with reference "demo_5"
    # no exception is thrown when zero/negative amount is passed and nothing changes in the db`
    When I add products to order with new invoice with the following products details:
    | amount | id_order | price | free_shipping | id_product |
    | -1     | 1        | 16    | true          | 4          |
    Then order "XKBKNABJK" should contain 4 products with reference "demo_5"
    When I add products to order with new invoice with the following products details:
    | amount | id_order | price | free_shipping | id_product |
    | 0      | 1        | 16    | true          | 4          |
    Then order "XKBKNABJK" should contain 4 products with reference "demo_5"
    When I add products to order with new invoice with the following products details:
    | amount | id_order | price | free_shipping | id_product |
    | 1      | 1        | 16    | true          | 4          |
    Then order "XKBKNABJK" should contain 5 products with reference "demo_5"
