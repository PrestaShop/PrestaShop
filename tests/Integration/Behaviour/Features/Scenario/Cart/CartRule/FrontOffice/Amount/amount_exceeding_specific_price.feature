# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-amount-exceeding-specific-price
@restore-all-tables-before-feature
@fo-cart-rule-amount-exceeding-specific-price
Feature: Cart rule (amount) exceeding the products total of products with a specific price
  As a customer
  I must not pay anything for the products when a voucher covers more than their total

  Background:
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is an address named "address1" with postcode "1" in state "state1"
    And there is a tax named "tax20" and rate 20.0%
    And there is a tax rule named "taxrule20" in country "country1" and state "state1" where tax "tax20" is applied
    And there is a product in the catalog named "tshirt" with a price of 23.9 and 1000 items in stock
    And product "tshirt" belongs to tax group "taxrule20"
    And product "tshirt" has a specific price named "tshirt_20_percent" with a discount of 20.0 percent
    And there is a product in the catalog named "tshirt2" with a price of 23.9 and 1000 items in stock
    And product "tshirt2" belongs to tax group "taxrule20"
    And product "tshirt2" has a specific price named "tshirt2_20_percent" with a discount of 20.0 percent
    And there is a cart rule "reduction_100" with following properties:
      | name[en-US]           | reduces 100 |
      | priority              | 1           |
      | free_shipping         | true        |
      | code                  | reduce-100  |
      | discount_amount       | 100         |
      | discount_includes_tax | false       |
    And I have an empty default cart

  Scenario Outline: voucher above the products total leaves nothing to pay for <quantity> products
    Given I add <quantity> items of product "tshirt" in my cart
    And I select address "address1" in my cart
    When I apply the voucher code "reduce-100"
    Then my cart total should be precisely 0.0 tax excluded
    And my cart total should be precisely 0.0 tax included

    Examples:
      | quantity |
      | 1        |
      | 2        |
      | 3        |

  Scenario Outline: voucher above the products total split on two rows leaves nothing to pay for <quantity1> and <quantity2> products
    Given I add <quantity1> items of product "tshirt" in my cart
    And I add <quantity2> items of product "tshirt2" in my cart
    And I select address "address1" in my cart
    When I apply the voucher code "reduce-100"
    Then my cart total should be precisely 0.0 tax excluded
    And my cart total should be precisely 0.0 tax included

    Examples:
      | quantity1 | quantity2 |
      | 1         | 1         |
      | 1         | 2         |
      | 1         | 3         |
