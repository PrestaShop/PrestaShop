# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-amount-multiple
@restore-all-tables-before-feature
@fo-cart-rule-amount-multiple
Feature: Cart rule (amount) calculation with multiple cart rules
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a product in the catalog named "product8" with a price of 12.345 and 1000 items in stock
    And there is a category named "Awesome"
    And product "product8" is in category "Awesome"
    And product "product8" is virtual
    And I create cart rule "reduction_5_dollar" with following properties:
      | name[en-US]                            | reduces $5             |
      | total_quantity                         | 1000                   |
      | quantity_per_user                      | 1000                   |
      | priority                               | 4                      |
      | free_shipping                          | false                  |
      | code                                   | reduce-5               |
      | reduction_amount                       | 5                      |
      | reduction_currency                     | usd                    |
      | reduction_tax                          | false                  |
      | reduction_apply_to_discounted_products | true                   |
      | discount_application_type              | order_without_shipping |
    And I create cart rule "reduction_500_dollar" with following properties:
      | name[en-US]                            | reduces $500           |
      | total_quantity                         | 1000                   |
      | quantity_per_user                      | 1000                   |
      | priority                               | 5                      |
      | free_shipping                          | false                  |
      | code                                   | reduce-500             |
      | reduction_amount                       | 500                    |
      | reduction_currency                     | usd                    |
      | reduction_tax                          | false                  |
      | reduction_apply_to_discounted_products | true                   |
      | discount_application_type              | order_without_shipping |
    And I create cart rule "reduction_10_dollar" with following properties:
      | name[en-US]                            | reduces $10            |
      | total_quantity                         | 1000                   |
      | quantity_per_user                      | 1000                   |
      | priority                               | 6                      |
      | free_shipping                          | false                  |
      | code                                   | reduce-10              |
      | reduction_amount                       | 10                     |
      | reduction_currency                     | usd                    |
      | reduction_tax                          | false                  |
      | reduction_apply_to_discounted_products | true                   |
      | discount_application_type              | order_without_shipping |

  Scenario: one product in cart, quantity 1, one 5€ global cartRule, one 10€ global cartRule
    Given I have an empty default cart
    And I add 1 items of product "product1" in my cart
    And total cart shipping fees should be 7.0 tax included
    And my cart total should be 26.812 tax included
    And my cart total using previous calculation method should be 26.812 tax included
    When I apply the discount code "reduce-5"
    Then my cart total should be 21.812 tax included
    And my cart total using previous calculation method should be 21.812 tax included
    And I apply the discount code "reduce-10"
    Then my cart total should be 11.812 tax included
    And my cart total using previous calculation method should be 11.812 tax included

  @restore-cart-rules-before-scenario
  Scenario: one product in cart, quantity 3, one 5€ global cartRule, one 10€ global cartRule
    Given I have an empty default cart
    And I add 3 items of product "product1" in my cart
    And total cart shipping fees should be 7.0 tax included
    And my cart total should be 66.436 tax included
    And my cart total using previous calculation method should be 66.436 tax included
    When I apply the discount code "reduce-5"
    Then my cart total should be 61.436 tax included
    And my cart total using previous calculation method should be 61.436 tax included
    When I apply the discount code "reduce-10"
    Then my cart total should be 51.436 tax included
    And my cart total using previous calculation method should be 51.436 tax included

  @restore-cart-rules-before-scenario
  Scenario: 3 products in cart, several quantities, one 5€ global cartRule (reduced product at first place)
    Given I have an empty default cart
    And I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    And total cart shipping fees should be 7.0 tax included
    And my cart total should be 162.4 tax included
    And my cart total using previous calculation method should be 162.4 tax included
    When I apply the discount code "reduce-5"
    Then my cart total should be 157.4 tax included
    And my cart total using previous calculation method should be 157.4 tax included
    When I apply the discount code "reduce-10"
    Then my cart total should be 147.4 tax included
    And my cart total using previous calculation method should be 147.4 tax included

  @restore-cart-rules-before-scenario
  Scenario: One product in my cart, one cart rule for free shipping and one for free gift
    Given I have an empty default cart
    And I create cart rule "cartrule-free-shipping" with following properties:
      | name[en-US]       | cartrule-free-shipping |
      | total_quantity    | 1                      |
      | quantity_per_user | 1                      |
      | priority          | 1                      |
      | free_shipping     | true                   |
    And I create cart rule "reduce-10-restricted" with following properties:
      | name[en-US]                            | reduce-10-restricted   |
      | total_quantity                         | 1                      |
      | quantity_per_user                      | 1                      |
      | priority                               | 1                      |
      | free_shipping                          | false                  |
      | reduction_amount                       | 10                     |
      | reduction_currency                     | usd                    |
      | reduction_tax                          | false                  |
      | reduction_apply_to_discounted_products | true                   |
      | discount_application_type              | order_without_shipping |
    And cart rule "reduce-10-restricted" is restricted to the category "Awesome" with a quantity of 1
    And cart rule "reduce-10-restricted" is restricted to product "product8"
    When I add 1 item of product "product8" in my cart
    And cart rule "reduce-10-restricted" is applied to my cart
    Then total cart shipping fees should be 0.0 tax included
    And my cart total should be precisely 2.35 tax included
    And my cart total should be 2.4 tax included
    And my cart total using previous calculation method should be 2.4 tax included

  @restore-cart-rules-before-scenario
  Scenario: One product in my cart, one 10€ global cartRule, 2 free gifts global cartRules
    Given I have an empty default cart
    And I create cart rule "reduce-10-global" with following properties:
      | name[en-US]                            | reduce-10-global       |
      | total_quantity                         | 1                      |
      | quantity_per_user                      | 1                      |
      | priority                               | 1                      |
      | free_shipping                          | false                  |
      | reduction_amount                       | 10                     |
      | reduction_currency                     | usd                    |
      | reduction_tax                          | false                  |
      | reduction_apply_to_discounted_products | true                   |
      | discount_application_type              | order_without_shipping |
    And I create cart rule "cartrule-gift-product2" with following properties:
      | name[en-US]       | cartrule-gift-product2 |
      | total_quantity    | 1                      |
      | quantity_per_user | 1                      |
      | priority          | 1                      |
      | free_shipping     | false                  |
      | gift_product      | product2               |
    And I create cart rule "cartrule-gift-product3" with following properties:
      | name[en-US]       | cartrule-gift-product3 |
      | total_quantity    | 1                      |
      | quantity_per_user | 1                      |
      | priority          | 1                      |
      | free_shipping     | false                  |
      | gift_product      | product3               |
    When I add 1 item of product "product1" in my cart
    Then cart rule "cartrule-gift-product2" is applied to my cart
    And cart rule "cartrule-gift-product3" is applied to my cart
    And cart rule "reduce-10-global" is applied to my cart
    And I should have 3 products in my cart
    And my cart total should be precisely 16.81 tax included
    And my cart total should be 16.8 tax included
    And my cart total using previous calculation method should be 16.8 tax included

  @restore-cart-rules-before-scenario
  Scenario: One product in my cart, one 30€ global cartRule (which is superior to the product bought), 2 free gifts global cartRules
    Given I have an empty default cart
    And I create cart rule "reduce-30-global" with following properties:
      | name[en-US]                            | reduce-30-global       |
      | total_quantity                         | 1                      |
      | quantity_per_user                      | 1                      |
      | priority                               | 1                      |
      | free_shipping                          | false                  |
      | reduction_amount                       | 30                     |
      | reduction_currency                     | usd                    |
      | reduction_tax                          | false                  |
      | reduction_apply_to_discounted_products | true                   |
      | discount_application_type              | order_without_shipping |
    And I create cart rule "cartrule-gift-product2" with following properties:
      | name[en-US]       | cartrule-gift-product2 |
      | total_quantity    | 1                      |
      | quantity_per_user | 1                      |
      | priority          | 1                      |
      | free_shipping     | false                  |
      | gift_product      | product2               |
    And I create cart rule "cartrule-gift-product3" with following properties:
      | name[en-US]       | cartrule-gift-product3 |
      | total_quantity    | 1                      |
      | quantity_per_user | 1                      |
      | priority          | 1                      |
      | free_shipping     | false                  |
      | gift_product      | product3               |
    When I add 1 item of product "product1" in my cart
    Then I should have 3 products in my cart
    And cart rule "cartrule-gift-product2" is applied to my cart
    And cart rule "cartrule-gift-product3" is applied to my cart
    And cart rule "reduce-30-global" is applied to my cart
    And total cart shipping fees should be 7.0 tax included
    And my cart total should be precisely 7.0 tax included
    And my cart total should be 7.0 tax included
    And my cart total using previous calculation method should be 7.0 tax included

  @restore-cart-rules-before-scenario
  Scenario: One product in my cart, one 30€ global cartRule (which is superior to the product bought) with free shipping, 2 free gifts global cartRules
    Given I have an empty default cart
    And I create cart rule "reduce-30-free-ship-global" with following properties:
      | name[en-US]                            | reduce-30-free-ship-global |
      | total_quantity                         | 1                          |
      | quantity_per_user                      | 1                          |
      | priority                               | 1                          |
      | free_shipping                          | true                       |
      | reduction_amount                       | 30                         |
      | reduction_currency                     | usd                        |
      | reduction_tax                          | false                      |
      | reduction_apply_to_discounted_products | true                       |
      | discount_application_type              | order_without_shipping     |
    And I create cart rule "cartrule-gift-product2" with following properties:
      | name[en-US]       | cartrule-gift-product2 |
      | total_quantity    | 1                      |
      | quantity_per_user | 1                      |
      | priority          | 1                      |
      | free_shipping     | false                  |
      | gift_product      | product2               |
    And I create cart rule "cartrule-gift-product3" with following properties:
      | name[en-US]       | cartrule-gift-product3 |
      | total_quantity    | 1                      |
      | quantity_per_user | 1                      |
      | priority          | 1                      |
      | free_shipping     | false                  |
      | gift_product      | product3               |
    When I add 1 item of product "product1" in my cart
    Then I should have 3 products in my cart
    And cart rule "cartrule-gift-product2" is applied to my cart
    And cart rule "cartrule-gift-product3" is applied to my cart
    And cart rule "reduce-30-free-ship-global" is applied to my cart
    And my cart total should be precisely 0.0 tax included
    And my cart total should be 0.0 tax included
    And my cart total using previous calculation method should be 0.0 tax included
