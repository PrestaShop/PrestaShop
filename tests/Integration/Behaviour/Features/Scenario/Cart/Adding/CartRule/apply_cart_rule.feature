# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags apply-cart-rule
@restore-all-tables-before-feature
@apply-cart-rule
Feature: Apply cart rule to cart
  As a customer
  I must be able to apply cart rules to my cart

  Background:
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And country "US" is enabled

  Scenario: Adding a cart rule to an empty cart should not be possible
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Then I should have 0 different products in my cart
    When I apply the discount code "foo1"
    Then I should get cart rule validation error saying "Cart is empty"
    When at least one cart rule applies today for customer with id 0
    Then I should have 0 products in my cart

  Scenario: 1 product in cart, percentage reduction cart rule is applied correctly
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    # 19.812 product +7 shipping (1 cent probably lost on price convertion?)
    And my cart total should be 26.8 tax excluded
    When I apply the discount code "foo1"
    # @todo: products discounted by 50% and then shipping is added. So (19.812/2)+7. Need to add shipping assertion to make it clearer
    Then my cart total should be 16.9 tax excluded
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart

  Scenario: 2 combinable cart rules are applied correctly
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Given there is a cart rule named "cartrule2" that applies a percent discount of 30.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    And my cart total should be 26.8 tax excluded
    When I apply the discount code "foo1"
    Then my cart total should be 16.9 tax excluded
    When I apply the discount code "foo2"
    # (19.812*0.5)*0.7 + 7 shipping
    Then my cart total should be 13.9 tax excluded
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart

  Scenario: Cart rule cannot be applied again when it is already in the cart
    Given I have an empty default cart
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    When I add 1 items of product "product1" in my cart
    Then I should have 1 different products in my cart
    And my cart total should be 26.8 tax excluded
    When I apply the discount code "foo1"
    Then my cart total should be 16.9 tax excluded
    When I apply the discount code "foo1"
    Then I should get cart rule validation error saying "This voucher is already in your cart"
    When at least one cart rule applies today for customer with id 0
    Then I should have 1 products in my cart

  Scenario: Percentage reduction cart rule and gift product cart rule applied to the same cart
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "cartrule1" has a discount code "foo1"
    And there is a cart rule named "cartrule12" that applies a percent discount of 10.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "cartrule12" has a discount code "foo12"
    And cart rule "cartrule12" offers a gift product "product3"
    And I add 1 items of product "product1" in my cart
    And I should have 1 different products in my cart
    And my cart total should be 26.8 tax excluded
    When I apply the discount code "foo12"
    Then my cart total should be 24.8 tax excluded
    And I should have 2 products in my cart
    When I apply the discount code "foo1"
    Then my cart total should be 15.9 tax excluded
    And I should have 2 products in my cart
    And at least one cart rule applies today for customer with id 0

  Scenario: 1 product in cart, cart rule giving gift out of stock, and global cart rule should be inserted without error (test PR #8361)
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product4" with a price of 35.567 and 1000 items in stock
    And product "product4" is out of stock
    And there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "cartrule1" has a discount code "foo1"
    And there is a cart rule named "cartrule13" that applies a percent discount of 10.0% with priority 13, quantity of 1000 and quantity per user 1000
    And cart rule "cartrule13" has a discount code "foo13"
    And cart rule "cartrule13" offers a gift product "product4"
    And I add 1 items of product "product1" in my cart
    And I should have 1 different products in my cart
    And my cart total should be 26.8 tax excluded
    When I apply the discount code "foo13"
    Then my cart total should be 24.8 tax excluded
    Then cart rule "cartrule1" can be applied to my cart
    When I apply the discount code "foo1"
    Then my cart total should be 15.9 tax excluded
    And I should have 1 products in my cart
    And at least one cart rule applies today for customer with id 0
