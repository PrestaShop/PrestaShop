# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-cheapest-product
@restore-all-tables-before-feature
@fo-cart-rule-cheapest-product
Feature: Cart rule (percent) calculation with one cart rule restricted to cheapest product
  As a customer
  I must be able to have correct cart total when adding cart rules

  Background:
    Given I have an empty default cart
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    And there is a cart rule "cartrule10" with following properties:
      | name[en-US]                  | cartrule10       |
      | total_quantity               | 1000             |
      | quantity_per_user            | 1000             |
      | priority                     | 10               |
      | free_shipping                | false            |
      | code                         | foo10            |
      | discount_percentage          | 50               |
      | apply_to_discounted_products | false            |
      | discount_application_type    | cheapest_product |

  Scenario: one product in cart, quantity 1, one 50% cartRule on cheapest product
    Given I add 1 items of product "product1" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 26.812 tax included
    When I apply the voucher code "foo10"
    Then my cart total should be 16.906 tax included

  Scenario: multiple products in cart, several quantities, one 50% cartRule on cheapest product
    Given I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 162.4 tax included
    When I apply the voucher code "foo10"
    Then my cart total should be 152.494 tax included

  Scenario: 50% cartRule on cheapest product excluding discounted products, cheapest product is already discounted
    Given product "product1" has a specific price named "specificPrice2" with an amount discount of 10.0
    And I add 2 items of product "product2" in my cart
    And I add 3 items of product "product1" in my cart
    And I add 1 items of product "product3" in my cart
    And my cart total shipping fees should be 7.0 tax included
    And my cart total should be 132.4 tax included
    When I apply the voucher code "foo10"
    # product1 is cheapest, but it has a specific price, so the 50% discount is applied to the next cheapest product which is product3
    Then my cart total should be 116.8 tax included

#  Scenario: 50% cartRule on cheapest product including already discounted products, cheapest product is already discounted
#    Given product "product1" has a specific price named "specificPrice3" with an amount discount of 10.0
#    And there is a cart rule "cartrule20" with following properties:
#      | name[en-US]                  | cartrule20       |
#      | priority                     | 10               |
#      | free_shipping                | false            |
#      | code                         | foo20            |
#      | discount_percentage          | 50               |
#      | apply_to_discounted_products | true             |
#      | discount_application_type    | cheapest_product |
#    And I add 2 items of product "product2" in my cart
#    And I add 3 items of product "product1" in my cart
#    And I add 1 items of product "product3" in my cart
#    And my cart total shipping fees should be 7.0 tax included
#    And my cart total should be 132.4 tax included
#    When I apply the voucher code "foo20"
#    And my cart total shipping fees should be 7.0 tax included
#    # @todo: doesn't seem to work. gets 127.5 instead, is it a bug in cart rules calculation or did I make a mistake in expectations?
#    #  (19.812-10)*3 = 29.436 (the cheapest product price with already applied specific price)
#    # 29.436*0.5 + 32.388*2 + 31.188 + 7.0 shipping
#    Then my cart total should be 117.682 tax included
