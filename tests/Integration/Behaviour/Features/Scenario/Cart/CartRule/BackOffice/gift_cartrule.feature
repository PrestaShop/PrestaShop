# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags bo-cart-gift-cart-rule
@restore-all-tables-before-feature
@bo-cart-gift-cart-rule
Feature: Apply cart rule to cart from Back Office (BO)
  As an employee
  I must be able to correctly apply various cart rules to cart when creating order in BO

  Background:
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And country "US" is enabled

  Scenario: 1 product in cart, 1 automatic cart rule offering free gift, change the product quantity and check split cart quantities
    Given I am logged in as "test@prestashop.com" employee
    Given I create an empty cart "dummy_custom_cart" for customer "customer1"
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    Given there is a cart rule named "cartrule13" that applies no discount with priority 13, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule13" offers a gift product "product4"
    When I add 1 products "product1" to the cart "dummy_custom_cart"
    Then product "product1" quantity in cart "dummy_custom_cart" should be 1 excluding gift products
    And gifted product "product4" quantity in cart "dummy_custom_cart" should be 1
    And cart "dummy_custom_cart" should contain 2 products
    And cart "dummy_custom_cart" should contain 1 products excluding gifts
    When I update quantity of product "product1" in the cart "dummy_custom_cart" to 2
    Then product "product1" quantity in cart "dummy_custom_cart" should be 2 excluding gift products
    And gifted product "product4" quantity in cart "dummy_custom_cart" should be 1
    And cart "dummy_custom_cart" should contain 2 products
    And cart "dummy_custom_cart" should contain 1 products excluding gifts
    When I add 1 products "product1" to the cart "dummy_custom_cart"
    Then product "product1" quantity in cart "dummy_custom_cart" should be 3 excluding gift products
    And gifted product "product4" quantity in cart "dummy_custom_cart" should be 1
    And cart "dummy_custom_cart" should contain 2 products
    And cart "dummy_custom_cart" should contain 1 products excluding gifts
    When I add 1 products "product4" to the cart "dummy_custom_cart"
    Then product "product4" quantity in cart "dummy_custom_cart" should be 1 excluding gift products
    And gifted product "product4" quantity in cart "dummy_custom_cart" should be 1
    When I update quantity of product "product4" in the cart "dummy_custom_cart" to 3
    Then product "product4" quantity in cart "dummy_custom_cart" should be 2 excluding gift products
    And gifted product "product4" quantity in cart "dummy_custom_cart" should be 1
    And cart "dummy_custom_cart" should contain 3 products
    And cart "dummy_custom_cart" should contain 2 products excluding gifts

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
