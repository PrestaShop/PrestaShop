# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags bo-delete-product
@restore-all-tables-before-feature
@bo-delete-product
Feature: Delete product from cart in Back Office (BO)
  As a BO user I must be able to delete products from cart
  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country

  Scenario: Delete standard product from cart
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And product "Mug The best is yet to come" quantity in cart "dummy_cart" should be 2 excluding gift products
    When I delete product "Mug The best is yet to come" from cart "dummy_cart"
    Then cart "dummy_cart" should not contain product "Mug The best is yet to come"

  Scenario: Delete standard product from cart when cart has another identical product added as a gift
    Given I create an empty cart "dummy_cart_2" for customer "testCustomer"
    And I add 2 products "Mountain fox notebook" to the cart "dummy_cart_2"
    And product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 2 excluding gift products
    When I use a voucher "giftFoxNotebook" which provides a gift product "Mountain fox notebook" on the cart "dummy_cart_2"
    And gifted product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 1
    When I delete product "Mountain fox notebook" from cart "dummy_cart_2"
    Then cart "dummy_cart_2" should not contain product "Mountain fox notebook" unless it is a gift
    And gifted product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 1
    And voucher "giftFoxNotebook" should still be applied to cart "dummy_cart_2"
