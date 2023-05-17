# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-amount-mono-virtual
@restore-all-tables-before-feature
@fo-cart-rule-amount-mono-virtual
Feature: Cart rule (amount) calculation with one cart rule
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a product in the catalog named "product8" with a price of 12.345 and 1000 items in stock
    And product "product8" is virtual
    And I create cart rule "reduction_5_dollar" with following properties:
      | name[en-US]                  | reduces $5             |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 4                      |
      | free_shipping                | false                  |
      | code                         | reduce-5               |
      | discount_amount              | 5                      |
      | discount_currency            | usd                    |
      | discount_includes_tax        | false                  |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And I create cart rule "reduction_500_dollar" with following properties:
      | name[en-US]                  | reduces $500           |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 5                      |
      | free_shipping                | false                  |
      | code                         | reduce-500             |
      | discount_amount              | 500                    |
      | discount_currency            | usd                    |
      | discount_includes_tax        | false                  |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |

  Scenario: 4 products in cart, one is virtual, several quantities, one 5€ global voucher
    Given I have an empty default cart
    And I add 3 items of product "product1" in my cart
    And I add 2 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And I add 2 items of product "product8" in my cart
    And total cart shipping fees should be 7.0 tax included
    And my cart total should be 187.09 tax included
    And my cart total using previous calculation method should be 187.09 tax included
    When I apply the discount code "reduce-5"
    Then my cart total should be 182.09 tax included
    And my cart total using previous calculation method should be 182.09 tax included

  @restore-cart-rules-before-scenario
  Scenario: Only virtual product in my cart, one 5€ global voucher
    Given I have an empty default cart
    And I add 2 items of product "product8" in my cart
    And total cart shipping fees should be 0.0 tax included
    And my cart total should be 24.69 tax included
    And my cart total using previous calculation method should be 24.69 tax included
    When I apply the discount code "reduce-5"
    Then my cart total should be 19.69 tax included
    And my cart total using previous calculation method should be 19.69 tax included
