# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags bo-delete-product
@reset-database-before-feature
Feature: Delete product from cart in Back Office (BO)
  As a BO user I must be able to delete products from cart
  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country

  @bo-delete-product
  Scenario: Delete standard product from cart
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And cart "dummy_cart" contains product "Mug The best is yet to come"
    When I delete product "Mug The best is yet to come" from cart "dummy_cart"
    Then cart "dummy_cart" should not contain product "Mug The best is yet to come"

  @bo-delete-product
  Scenario: Delete standard product from cart when cart has another identical product added as a gift
    Given I create an empty cart "dummy_cart_2" for customer "testCustomer"
    And I add 2 products "Mountain fox notebook" to the cart "dummy_cart_2"
    And cart "dummy_cart_2" contains product "Mountain fox notebook"
    When I use a voucher "giftFoxNotebook" for a gift product "Mountain fox notebook" on the cart "dummy_cart_2"
    Then cart "dummy_cart_2" should contain gift product "Mountain fox notebook"
    When I delete product "Mountain fox notebook" from cart "dummy_cart_2"
    Then cart "dummy_cart_2" should not contain product "Mountain fox notebook" unless it is a gift
    But cart "dummy_cart_2" should contain gift product "Mountain fox notebook"
    And voucher "giftFoxNotebook" should still be applied to cart "dummy_cart_2"
