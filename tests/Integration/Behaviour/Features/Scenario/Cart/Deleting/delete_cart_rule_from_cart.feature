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

#  @bo-delete-cart-rule
#  Scenario: Delete cart rule with gift product
#    Given I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
#    And cart "dummy_cart" contains product "Mug The best is yet to come"
#    And I use a voucher "giftFoxNotebook" for a gift product "Mountain fox notebook" on the cart "dummy_cart"
#    And cart "dummy_cart" contains gift product "Mountain fox notebook"
#    When I delete voucher "giftFoxNotebook" from cart "dummy_cart"
#    Then cart "dummy_cart" should not contain gift product "Mountain fox notebook"

  @bo-delete-cart-rule
  Scenario: Delete cart rule with gift product when same product as gift already exists in cart
    Given I create an empty cart "dummy_cart_2" for customer "testCustomer"
    And I add 3 products "Mountain fox notebook" to the cart "dummy_cart_2"
    And cart "dummy_cart_2" contains product "Mountain fox notebook"
    And I use a voucher "giftFoxNotebook" for a gift product "Mountain fox notebook" on the cart "dummy_cart_2"
    And cart "dummy_cart_2" contains gift product "Mountain fox notebook"
    When I delete voucher "giftFoxNotebook" from cart "dummy_cart_2"
    Then cart "dummy_cart_2" should not contain gift product "Mountain fox notebook"
    But cart "dummy_cart_2" should contain product "Mountain fox notebook"
