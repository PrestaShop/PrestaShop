# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-validation
@restore-all-tables-before-feature
@fo-cart-rule-validation
Feature: Cart rule application is validated before it is applied to cart
  As a customer
  I must not be able to apply invalid cart rules

  Background:
    Given there is customer "testCustomer" with email "pub@prestashop.com"
    And country "US" is enabled
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And I create cart rule "cart_rule_1" with following properties:
      | name[en-US]                            | cartrule1              |
      | total_quantity                         | 1000                   |
      | quantity_per_user                      | 1000                   |
      | free_shipping                          | false                  |
      | code                                   | foo1                   |
      | reduction_percentage                   | 50                     |
      | reduction_apply_to_discounted_products | true                   |
      | discount_application_type              | order_without_shipping |
    And I create cart rule "cart_rule_2" with following properties:
      | name[en-US]                            | cartrule2              |
      | total_quantity                         | 10                     |
      | quantity_per_user                      | 10                     |
      | free_shipping                          | false                  |
      | code                                   | foo2                   |
      | reduction_amount                       | 10                     |
      | reduction_currency                     | usd                    |
      | reduction_tax                          | false                  |
      | reduction_apply_to_discounted_products | true                   |
      | discount_application_type              | order_without_shipping |
    And I create cart rule "cart_rule_3" with following properties:
      | name[en-US]       | cartrule3              |
      | total_quantity    | 10                     |
      | quantity_per_user | 10                     |
      | free_shipping     | true                   |
      | code              | foo3                   |

  Scenario: Adding a cart rule to an empty cart should not be possible
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And discount code "foo1" is not applied to my cart
    And discount code "foo2" is not applied to my cart
    And discount code "foo3" is not applied to my cart
    And I should have 0 different products in my cart
    And total cart shipping fees should be 0.0 tax excluded
    And my cart total should be 0.0 tax excluded
    # try applying percentage discount
    When I apply the discount code "foo1"
    Then I should get cart rule validation error saying "Cart is empty"
    And discount code "foo1" is not applied to my cart
    And I should have 0 products in my cart
    And total cart shipping fees should be 0.0 tax excluded
    And my cart total should be 0.0 tax excluded
    # try applying amount discount
    When I apply the discount code "foo2"
    Then I should get cart rule validation error saying "Cart is empty"
    And discount code "foo2" is not applied to my cart
    And I should have 0 products in my cart
    And total cart shipping fees should be 0.0 tax excluded
    And my cart total should be 0.0 tax excluded
    # try applying free shipping discount
    When I apply the discount code "foo3"
    Then I should get cart rule validation error saying "Cart is empty"
    And discount code "foo3" is not applied to my cart
    And I should have 0 products in my cart
    And total cart shipping fees should be 0.0 tax excluded
    And my cart total should be 0.0 tax excluded

  @restore-cart-rules-before-scenario
  Scenario: Cart rule cannot be applied again when it is already in the cart
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And I add 1 items of product "product1" in my cart
    And I should have 1 different products in my cart
    And total cart shipping fees should be 7.0 tax excluded
    And my cart total should be 26.8 tax excluded
    And discount code "foo1" is not applied to my cart
    And I apply the discount code "foo1"
    And discount code "foo1" is applied to my cart
    And my cart total should be 16.9 tax excluded
    When I apply the discount code "foo1"
    Then I should get cart rule validation error saying "This voucher is already in your cart"
    And my cart total should be 16.9 tax excluded
    And I should have 1 products in my cart
