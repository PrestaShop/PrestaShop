# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-to-cart
@reset-database-before-feature
Feature: Check order to cart data copy
  As a BO user
  I must be able to duplicate a cart from an existing order

  @order-to-cart
  Scenario: Duplicate an order from Back Office
    Given I am logged in as "test@prestashop.com" employee
    And the current currency is "USD"
    And country "US" is enabled
    And there is customer "customer_for_customization" with email "pub@prestashop.com"
    And customer "customer_for_customization" has address in "US" country
    And the module "dummy_payment" is installed
    When I create an empty cart "dummy_custom_cart" for customer "customer_for_customization"
    And I add 1 customized products with reference "demo_14" to the cart "dummy_custom_cart"
    And I select "US" address as delivery and invoice address for customer "customer_for_customization" in cart "dummy_custom_cart"
    And I add order "bo_order_for_customization" from cart "dummy_custom_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then order "bo_order_for_customization" should have 1 products in total
    And order "bo_order_for_customization" should have "dummy_payment" payment method
    Given order "bo_order_for_customization" should have 1 products in total
    When I duplicate "bo_order_for_customization" to create cart "duplicated_bo_order_for_customization"
    # Additional checks should be added to validate the cart is correctly duplicated (even customization address)
