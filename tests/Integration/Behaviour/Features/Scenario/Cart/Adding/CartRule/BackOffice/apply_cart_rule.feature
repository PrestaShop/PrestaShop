# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags bo-apply-cart-rule
@restore-all-tables-before-feature
@bo-apply-cart-rule
Feature: Apply cart rule to cart from Back Office (BO)
  As an employee
  I must be able to correctly apply various cart rules to cart when creating order in BO

  Background:
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And country "US" is enabled

  Scenario: Add cart rule which provides gift product and free shipping
    Given I create an empty cart "dummy_cart_1" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart_1"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart_1"
    And product "Mug The best is yet to come" quantity in cart dummy_cart_1 should be 2 excluding gift products
    When I use a voucher "gift+freeShip" which provides a gift product "Mountain fox notebook" and free shipping on the cart "dummy_cart_1"
    Then gifted product "Mountain fox notebook" quantity in cart "dummy_cart_1" should be 1
    And cart "dummy_cart_1" should have free shipping
    And reduction value of voucher "gift+freeShip" in cart "dummy_cart_1" should be "19.9"

  Scenario: Add multiple cart rules which uses same gift product to the cart which already has paid products identical to those gifts
    Given I create an empty cart "dummy_cart_2" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart_2"
    And I add 2 products "Mountain fox notebook" to the cart "dummy_cart_2"
    And product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 2 excluding gift products
    When I use a voucher "foxGift1" which provides a gift product "Mountain fox notebook" on the cart "dummy_cart_2"
    Then product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 2 excluding gift products
    And gifted product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 1
    And reduction value of voucher "foxGift1" in cart "dummy_cart_2" should be "12.9"
    When I use a voucher "foxGift2" which provides a gift product "Mountain fox notebook" on the cart "dummy_cart_2"
    Then product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 2 excluding gift products
    And gifted product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 2
    And reduction value of voucher "foxGift2" in cart "dummy_cart_2" should be "12.9"
