# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags order-level-with-free-shipping
@restore-all-tables-before-feature
@order-level-with-free-shipping
@clear-cache-before-feature
@clear-cache-after-feature
Feature: Order level discount combined with a free shipping discount
  An order level discount caps itself against the shipping total, and a free shipping discount
  changes that total, so the two evaluate against each other on the same cart

  Background:
    Given there is a customer named "testCustomer" whose email is "pub3@prestashop.com"
    And language with iso code "en" is the default one
    And shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  Scenario: Both discounts apply on the same cart
    Given I create an empty cart "combined_cart" for customer "testCustomer"
    And I enable feature flag "discount"
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I create a "free_shipping" discount "combined_free_shipping" with following properties:
      | name[en-US] | Free shipping |
      | active      | true          |
      | code        | COMBINED_FS   |
    And I create a "order_level" discount "combined_order_level" with following properties:
      | name[en-US]            | Discount on total order |
      | active                 | true                    |
      | code                   | COMBINED_OL             |
      | reduction_amount       | 5.0                     |
      | reduction_currency     | usd                     |
      | reduction_tax_included | true                    |
    And I add 1 product "product1" to the cart "combined_cart"
    And I use a voucher "combined_free_shipping" on the cart "combined_cart"
    And I use a voucher "combined_order_level" on the cart "combined_cart"
    Then my cart "combined_cart" should have the following details:
      | total_products | $19.81 |
