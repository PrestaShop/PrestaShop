# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-validation
@restore-all-tables-before-feature
@fo-cart-rule-validation
Feature: Cart rule application is validated before it is applied to cart
  As a customer
  I must not be able to apply invalid cart rules

  Background:
    Given there is customer "testCustomer" with email "pub@prestashop.com"
    And country "US" is enabled

  Scenario: Adding a cart rule to an empty cart should not be possible
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "cartrule1" has a discount code "foo1"
    And I should have 0 different products in my cart
    And total cart shipping fees should be 0.0 tax excluded
    And my cart total should be 0.0 tax excluded
    When I apply the discount code "foo1"
    Then I should get cart rule validation error saying "Cart is empty"
    And at least one cart rule applies today for customer with id 0
    And I should have 0 products in my cart
    And total cart shipping fees should be 0.0 tax excluded
    And my cart total should be 0.0 tax excluded
    Given there is a cart rule named "cartrule2" that applies an amount discount of 10.0 with priority 1, quantity of 10 and quantity per user 10
    Given cart rule "cartrule2" has a discount code "foo2"
    When I apply the discount code "foo2"
    Then I should get cart rule validation error saying "Cart is empty"
    And I should have 0 products in my cart
    And total cart shipping fees should be 0.0 tax excluded
    And my cart total should be 0.0 tax excluded
    Given there is a cart rule named "cartrule-free-shipping" that applies an amount discount of 0.0 with priority 1, quantity of 1 and quantity per user 1
    And cart rule "cartrule-free-shipping" offers free shipping
    And cart rule "cartrule-free-shipping" has a discount code "foo3"
    When I apply the discount code "foo2"
    Then I should get cart rule validation error saying "Cart is empty"
    And I should have 0 products in my cart
    And total cart shipping fees should be 0.0 tax excluded
    And my cart total should be 0.0 tax excluded

  Scenario: Cart rule cannot be applied again when it is already in the cart
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    And total cart shipping fees should be 7.0 tax excluded
    And my cart total should be 26.8 tax excluded
    When I apply the discount code "foo1"
    Then my cart total should be 16.9 tax excluded
    When I apply the discount code "foo1"
    Then I should get cart rule validation error saying "This voucher is already in your cart"
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart
