# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-ecotax
@clear-cache-before-feature
@restore-all-tables-before-feature
@fo-cart-rule-ecotax
Feature: Cart rule (percent) calculation with one cart rule
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given I have an empty default cart
    And there is a zone named "zone1"
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is an address named "address1" with postcode "1" in state "state1"
    And shop configuration for "PS_USE_ECOTAX" is set to 1
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a tax named "tax4Percent" and rate 4.0%
    And there is a tax rule named "taxRule4Percent" in country "country1" where tax "tax4Percent" is applied
    And there is a product in the catalog named "product_without_ecotax" with a price of 10.000 and 1000 items in stock
    And product "product_without_ecotax" belongs to tax group "taxRule4Percent"
    And there is a product in the catalog named "product_with_ecotax" with a price of 10.000 and 1000 items in stock
    And the product "product_with_ecotax" ecotax is 2.00
    And product "product_with_ecotax" belongs to tax group "taxRule4Percent"

  @restore-cart-rules-after-scenario
  Scenario:
    And there is a cart rule "cartRuleFiftyPercent" with following properties:
      | name[en-US]                  | cartRuleFiftyPercent   |
      | priority                     | 2                      |
      | free_shipping                | false                  |
      | code                         | cartRuleFiftyPercent   |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | specific_product       |
      | discount_product             | product_without_ecotax |
    And I select address "address1" in my cart
    When I add 1 item of product "product_without_ecotax" in my cart
    Then my cart total should be 10.400 tax included
    And my cart total should be 10.000 tax excluded
    And I apply the voucher code "cartRuleFiftyPercent"
    And my cart total should be 5.200 tax included
    And my cart total should be 5.000 tax excluded

  @restore-cart-rules-after-scenario
  Scenario:
    And there is a cart rule "cartRuleFiftyPercent" with following properties:
      | name[en-US]                  | cartRuleFiftyPercent |
      | priority                     | 2                    |
      | free_shipping                | false                |
      | code                         | cartRuleFiftyPercent |
      | discount_percentage          | 50                   |
      | apply_to_discounted_products | true                 |
      | discount_application_type    | specific_product     |
      | discount_product             | product_with_ecotax  |
    And I select address "address1" in my cart
    When I add 1 item of product "product_with_ecotax" in my cart
    Then my cart total should be 12.400 tax included
    And my cart total should be 12.000 tax excluded
    And I apply the voucher code "cartRuleFiftyPercent"
    And my cart total should be 6.200 tax included
    And my cart total should be 6.000 tax excluded
    And I should have a voucher named "cartRuleFiftyPercent" with 6.2 of discount

  Scenario:
    And there is a cart rule "cartRuleFiftyPercent" with following properties:
      | name[en-US]                  | cartRuleFiftyPercent |
      | priority                     | 2                    |
      | free_shipping                | false                |
      | code                         | cartRuleFiftyPercent |
      | discount_percentage          | 50                   |
      | apply_to_discounted_products | true                 |
      | discount_application_type    | specific_product     |
      | discount_product             | product_with_ecotax  |
    Given Ecotax belongs to tax group "taxRule4Percent"
    And I select address "address1" in my cart
    When I add 1 item of product "product_with_ecotax" in my cart
    And my cart total should be precisely 12.000 tax excluded
    And my cart total should be precisely 12.48 tax included
    And I apply the voucher code "cartRuleFiftyPercent"
    And my cart total should be precisely 6.24 tax included
    And my cart total should be 6.000 tax excluded
    And I should have a voucher named "cartRuleFiftyPercent" with 6.24 of discount
