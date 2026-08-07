# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-amount-over-total-specific-price
@restore-all-tables-before-feature
@fo-cart-rule-amount-over-total-specific-price
Feature: Cart rule (amount) greater than the products total on products with specific prices
  As a customer
  When I apply a cart discount greater than my whole products total on taxed products
  that carry a specific price, the remaining products total must be exactly 0,
  not a residual cent (https://github.com/PrestaShop/PrestaShop/issues/39436)

  Background:
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is an address named "address1" with postcode "1" in state "state1"
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a tax named "tax20" and rate 20.0%
    And there is a tax rule named "taxrule20" in country "country1" and state "state1" where tax "tax20" is applied
    And there is a product in the catalog named "product1" with a price of 12.34 and 1000 items in stock
    And product "product1" belongs to tax group "taxrule20"
    And product "product1" has a specific price named "sp1" with a discount of 10.0 percent
    And there is a product in the catalog named "product2" with a price of 45.67 and 1000 items in stock
    And product "product2" belongs to tax group "taxrule20"
    And product "product2" has a specific price named "sp2" with a discount of 10.0 percent
    And there is a cart rule "overTotal" with following properties:
      | name[en-US]           | over total voucher |
      | priority              | 1                  |
      | free_shipping         | false              |
      | code                  | overtotal          |
      | discount_amount       | 1000               |
      | discount_currency     | usd                |
      | discount_includes_tax | true               |

  Scenario: an amount discount over the products total leaves no residual cent (quantities 2 + 1)
    Given I have an empty default cart
    When I select address "address1" in my cart
    And I add 2 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    And I apply the voucher code "overtotal"
    Then my cart total should be precisely 0.0 tax included
    And my cart total should be precisely 0.0 tax excluded

  Scenario: an amount discount over the products total leaves no residual cent (quantities 3 + 1)
    Given I have an empty default cart
    When I select address "address1" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    And I apply the voucher code "overtotal"
    Then my cart total should be precisely 0.0 tax included
    And my cart total should be precisely 0.0 tax excluded
