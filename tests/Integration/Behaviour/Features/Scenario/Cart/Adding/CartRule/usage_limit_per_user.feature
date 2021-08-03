# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml --tags remove-cart-rule
@reset-database-before-feature
@cart-rule-usage-limit

Feature: A cart rule's usage limit per user is detected

  Background:
    Given country "US" is enabled
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I am logged in as "test@prestashop.com" employee
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And the module "dummy_payment" is installed
    And there is a cart rule named "limitedCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 2 and quantity per user 1
    And cart rule "limitedCartRule" has a discount code "foo1"
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock

  Scenario: Cart rule usage limit is detected when the cart rule is created anonymously then assigned to a customer who already used it
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I add 1 items of product "product1" in my cart
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    Then I should have 1 different products in my cart
    And cart rule "limitedCartRule" can be applied to my cart
    When I use the discount "limitedCartRule"
    Then at least one cart rule applies today for customer with id 0
    And I should have 1 products in my cart
    When I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I create an empty anonymous cart "anonymousDummyCart"
    And I add 1 product "product1" to the cart "anonymousDummyCart"
    And I use a voucher "foo1" on the cart "anonymousDummyCart"
    And I assign customer "testCustomer" to cart "anonymousDummyCart"
    Then usage limit per user for cart rule "limitedCartRule" is detected
