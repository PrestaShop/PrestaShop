# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags bo-add-cart-rule
@reset-database-before-feature
Feature: Add cart rule in cart
  As a customer
  I must be able to correctly add cart rules in my cart

  Background:
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And country "US" is enabled

  @bo-add-cart-rule
  Scenario: No product in cart should give a not valid cart rule insertion
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Then I should have 0 different products in my cart
    Then cart rule "cartrule1" cannot be applied to my cart
    When I use the discount "cartrule1"
    When at least one cart rule applies today for customer with id 0
    Then I should have 0 products in my cart

  @bo-add-cart-rule
  Scenario: 1 product in cart, cart rule is inserted correctly
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    Then cart rule "cartrule1" can be applied to my cart
    When I use the discount "cartrule1"
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart

  @bo-add-cart-rule
  Scenario: 1 product in cart, cart rules are inserted correctly
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    Then cart rule "cartrule1" can be applied to my cart
    When I use the discount "cartrule1"
    Then cart rule "cartrule2" can be applied to my cart
    When I use the discount "cartrule2"
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart

  @bo-add-cart-rule
  Scenario: 1 product in cart, double cart rule not inserted
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    Then cart rule "cartrule1" can be applied to my cart
    When I use the discount "cartrule1"
    Then cart rule "cartrule1" cannot be applied to my cart
    When I use the discount "cartrule1"
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart

  @bo-add-cart-rule
  Scenario: 1 product in cart, cart rule giving gift, and global cart rule should be inserted without error
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Given there is a cart rule named "cartrule12" that applies a percent discount of 10.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule12" has a discount code "foo12"
    Given cart rule "cartrule12" offers a gift product "product3"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    Then cart rule "cartrule12" can be applied to my cart
    When I use the discount "cartrule12"
    Then cart rule "cartrule1" can be applied to my cart
    When I use the discount "cartrule1"
    When at least one cart rule applies today for customer with id 0
    Then I should have 2 products in my cart

  @bo-add-cart-rule
  Scenario: 1 product in cart, cart rule giving gift out of stock, and global cart rule should be inserted without error (test PR #8361)
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    Given product "product4" is out of stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Given there is a cart rule named "cartrule13" that applies a percent discount of 10.0% with priority 13, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule13" has a discount code "foo13"
    Given cart rule "cartrule13" offers a gift product "product4"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    Then cart rule "cartrule13" can be applied to my cart
    When I use the discount "cartrule13"
    Then cart rule "cartrule1" can be applied to my cart
    When I use the discount "cartrule1"
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart

  @bo-add-cart-rule
  Scenario: Add cart rule which provides gift product and free shipping
    Given I create an empty cart "dummy_cart_1" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart_1"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart_1"
    And product "Mug The best is yet to come" quantity in cart dummy_cart_1 should be 2 excluding gift products
    When I use a voucher "gift+freeShip" which provides a gift product "Mountain fox notebook" and free shipping on the cart "dummy_cart_1"
    Then gifted product "Mountain fox notebook" quantity in cart "dummy_cart_1" should be 1
    And cart "dummy_cart_1" should have free shipping
    And reduction value of voucher "gift+freeShip" in cart "dummy_cart_1" should be "19.9"

  @bo-add-cart-rule
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
