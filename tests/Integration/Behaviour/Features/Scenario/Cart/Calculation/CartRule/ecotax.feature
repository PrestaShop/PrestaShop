# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags cart-calculation-cartrule-ecotax

@restore-all-tables-before-feature
@clear-cache-before-scenario
@cart-calculation-cartrule-ecotax
Feature: Cart rule (percent) calculation with one cart rule
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given there is a zone named "zone1"
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is an address named "address1" with postcode "1" in state "state1"
    And shop configuration for "PS_USE_ECOTAX" is set to 1

  Scenario:
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a tax named "tax4Percent" and rate 4.0%
    And there is a tax rule named "taxRule4Percent" in country "country1" where tax "tax4Percent" is applied
    And I have an empty default cart
    ## Set Product
    And there is a product in the catalog named "product_without_ecotax" with a price of 10.000 and 1000 items in stock
    And product "product_without_ecotax" belongs to tax group "taxRule4Percent"
    ## Set Cart Rule
    When there is a cart rule named "cartRuleFiftyPercent" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    And cart rule "cartRuleFiftyPercent" is restricted to product "product_without_ecotax"
    And cart rule "cartRuleFiftyPercent" is restricted on the selection of products
    And cart rule "cartRuleFiftyPercent" has a discount code "cartRuleFiftyPercent"
    ## Add product
    When I select address "address1" in my cart
    And I add 1 item of product "product_without_ecotax" in my cart
    Then my cart total should be 10.400 tax included
    And my cart total should be 10.000 tax excluded
    And I use the discount "cartRuleFiftyPercent"
    Then my cart total should be 5.200 tax included
    And my cart total should be 5.000 tax excluded

  Scenario:
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a tax named "tax4Percent" and rate 4.0%
    And there is a tax rule named "taxRule4Percent" in country "country1" where tax "tax4Percent" is applied
    And I have an empty default cart
    ## Set Product
    And there is a product in the catalog named "product_with_ecotax" with a price of 10.000 and 1000 items in stock
    And the product "product_with_ecotax" ecotax is 2.00
    And product "product_with_ecotax" belongs to tax group "taxRule4Percent"
    ## Set Cart Rule
    When there is a cart rule named "cartRuleFiftyPercent" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    And cart rule "cartRuleFiftyPercent" is restricted to product "product_with_ecotax"
    And cart rule "cartRuleFiftyPercent" is restricted on the selection of products
    And cart rule "cartRuleFiftyPercent" has a discount code "cartRuleFiftyPercent"
    ## Add product
    When I select address "address1" in my cart
    And I add 1 item of product "product_with_ecotax" in my cart
    Then my cart total should be 12.400 tax included
    And my cart total should be 12.000 tax excluded
    And I use the discount "cartRuleFiftyPercent"
    Then my cart total should be 6.200 tax included
    And my cart total should be 6.000 tax excluded
    Then I should have a voucher named "cartRuleFiftyPercent" with 6.2 of discount

  Scenario:
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a tax named "tax4Percent" and rate 4.0%
    And there is a tax rule named "taxRule4Percent" in country "country1" where tax "tax4Percent" is applied
    And Ecotax belongs to tax group "taxRule4Percent"
    And I have an empty default cart
    ## Set Product
    And there is a product in the catalog named "product_with_ecotax" with a price of 10.000 and 1000 items in stock
    And the product "product_with_ecotax" ecotax is 2.00
    And product "product_with_ecotax" belongs to tax group "taxRule4Percent"
    ## Set Cart Rule
    When there is a cart rule named "cartRuleFiftyPercent" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    And cart rule "cartRuleFiftyPercent" is restricted to product "product_with_ecotax"
    And cart rule "cartRuleFiftyPercent" is restricted on the selection of products
    And cart rule "cartRuleFiftyPercent" has a discount code "cartRuleFiftyPercent"
    ## Add product
    When I select address "address1" in my cart
    And I add 1 item of product "product_with_ecotax" in my cart
    Then my cart total should be precisely 12.48 tax included
    And my cart total should be 12.000 tax excluded
    And I use the discount "cartRuleFiftyPercent"
    Then my cart total should be precisely 6.24 tax included
    And my cart total should be 6.000 tax excluded
    Then I should have a voucher named "cartRuleFiftyPercent" with 6.24 of discount
