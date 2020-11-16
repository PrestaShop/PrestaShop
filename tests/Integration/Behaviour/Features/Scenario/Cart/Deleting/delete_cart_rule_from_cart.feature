# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags bo-delete-cart-rule
@reset-database-before-feature
Feature: Delete cart rule from cart in Back Office (BO)
  As a BO user I must be able to delete cart rules from cart
  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"

  @bo-delete-cart-rule
  Scenario: Delete cart rule with gift product
    Given I update quantity of product "Mug The best is yet to come" in the cart "dummy_cart" to 2
    And product "Mug The best is yet to come" quantity in cart "dummy_cart" should be 2 excluding gift products
    And I use a voucher "giftFoxNotebook" which provides a gift product "Mountain fox notebook" on the cart "dummy_cart"
    And gifted product "Mountain fox notebook" quantity in cart "dummy_cart" should be 1
    When I delete voucher "giftFoxNotebook" from cart "dummy_cart"
    Then cart "dummy_cart" should not contain gift product "Mountain fox notebook"

  @bo-delete-cart-rule
  Scenario: Delete cart rule with gift product when same product as gift already exists in cart
    Given I create an empty cart "dummy_cart_2" for customer "testCustomer"
    And I update quantity of product "Mountain fox notebook" in the cart "dummy_cart_2" to 3
    And product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 3 excluding gift products
    And I use a voucher "giftFoxNotebook" which provides a gift product "Mountain fox notebook" on the cart "dummy_cart_2"
    And gifted product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 1
    When I delete voucher "giftFoxNotebook" from cart "dummy_cart_2"
    Then product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 3 excluding gift products
    But cart "dummy_cart_2" should not contain gift product "Mountain fox notebook"
