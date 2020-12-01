# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags cart-minimum-amount-cart-rule
@reset-database-before-feature
@cart-minimum-amount-cart-rule
Feature: Add cart rule in cart
  As a customer
  I must be able to correctly add cart rules in my cart

  Background:
    Given there is customer "testCustomer" with email "pub@prestashop.com"
    And country "US" is enabled

  Scenario: cart rule with minimum amount doesn't apply if cart total is lower
    Given I am logged in as "test@prestashop.com" employee
    And there is customer "customer1" with email "pub@prestashop.com"
    And I create an empty cart "dummy_custom_cart" for customer "customer1"
    And email sending is disabled
    And shipping handling fees are set to 2.0
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    Given I want to create a new cart rule
    And I specify its name in default language as "CartRule with minimum amount"
    And I specify its "description" as "CartRule with minimum amount"
    And I specify that its active from "2019-01-01 11:05:00"
    And I specify that its active until "2029-12-01 00:00:00"
    And I specify that its "quantity" is "10"
    And I specify that its "quantity per user" is "1"
    And I specify that its "priority" is "2"
    And I specify that partial use is disabled for it
    And I specify its status as enabled
    And I specify its "code" as "CART_RULE_MIN_AMOUNT"
    And its minimum purchase amount in currency "USD" is "50"
    And its minimum purchase amount is tax excluded
    And its minimum purchase amount is shipping included
    And it gives a reduction amount of "2" in currency "USD" which is tax included and applies to order without shipping
    And I save it
    When I add 1 products "product1" to the cart "dummy_custom_cart"
    And I add 1 products "product4" to the cart "dummy_custom_cart"
    Then cart "dummy_custom_cart" should contain 2 products
    When I use a voucher "CART_RULE_MIN_AMOUNT" on the cart "dummy_custom_cart"
    Then reduction value of voucher "CART_RULE_MIN_AMOUNT" in cart "dummy_custom_cart" should be "2"
    And cart "dummy_custom_cart" total with tax included should be "$53.38"
    When I delete product "product1" from cart "dummy_custom_cart"
    Then cart "dummy_custom_cart" should contain 1 products
    And cart "dummy_custom_cart" total with tax included should be "$35.57"
    And voucher "CART_RULE_MIN_AMOUNT" should not be applied to cart "dummy_custom_cart"
