# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-specific-price-quantity-discount
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-specific-price-quantity-discount
Feature: Order detail of a product carrying a specific price
  As a shop owner
  When a product is ordered with a specific price that keeps the catalogue price and only applies a
  reduction, I want the order detail to record a discount that matches the price actually charged

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists

  Scenario: A percentage specific price does not inflate the recorded quantity discount
    Given there is a product in the catalog named "Discounted18658" with a price of 63.05 and 100 items in stock
    And product "Discounted18658" has a specific price named "ten_percent" with a discount of 10.0 percent
    And I create an empty cart "cart18658" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "cart18658"
    And I add 1 products "Discounted18658" to the cart "cart18658"
    When I add order "bo_order18658" from cart "cart18658" with "dummy_payment" payment method and "Payment accepted" order status
    Then product "Discounted18658" in order "bo_order18658" has following details:
      | unit_price_tax_excl       | 56.745 |
      | product_quantity_discount | 60.15 |
