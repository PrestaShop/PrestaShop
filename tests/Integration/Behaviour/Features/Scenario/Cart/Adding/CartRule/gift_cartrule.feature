# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags cart-gift-cart-rule
@reset-database-before-feature
@cart-gift-cart-rule
Feature: Add cart rule in cart
  As a customer
  I must be able to correctly add cart rules in my cart

  Background:
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And country "US" is enabled

  Scenario: 1 product in cart, 1 automatic cart rule offering free gift, change the product quantity and check split cart quantities
    Given I am logged in as "test@prestashop.com" employee
    Given there is customer "customer1" with email "pub@prestashop.com"
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
