# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-cart-rules
@reset-database-before-feature
@order-cart-rules
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Add product linked to a cart rule to an existing Order without invoice with free shipping and new invoice And remove this product
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "Test Product Cart Rule On Select Product" with a price of 15.0 and 100 items in stock
    And there is a cart rule named "CartRuleAmountOnSelectedProduct" that applies an amount discount of 500.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "CartRuleAmountOnSelectedProduct" has no discount code
    And cart rule "CartRuleAmountOnSelectedProduct" is restricted to product "Test Product Cart Rule On Select Product"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Cart Rule On Select Product  |
      | amount        | 1                                         |
      | price         | 15                                        |
      | free_shipping | true                                      |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product Cart Rule On Select Product"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$15.00"
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 15.000 |
      | total_discounts_tax_incl | 15.900 |
      | total_paid_tax_excl      | 30.8   |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove product "Test Product Cart Rule On Select Product" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should contain 0 product "Test Product Cart Rule On Select Product"
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product linked to a cart rule to an existing Order without invoice with free shipping and new invoice And update the product quantity and price
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "Test Product Cart Rule On Select Product" with a price of 15.0 and 100 items in stock
    And there is a cart rule named "CartRuleAmountOnSelectedProduct" that applies an amount discount of 500.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "CartRuleAmountOnSelectedProduct" has no discount code
    And cart rule "CartRuleAmountOnSelectedProduct" is restricted to product "Test Product Cart Rule On Select Product"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Cart Rule On Select Product  |
      | amount        | 1                                         |
      | price         | 15                                        |
      | free_shipping | true                                      |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product Cart Rule On Select Product"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$15.00"
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 15.000 |
      | total_discounts_tax_incl | 15.900 |
      | total_paid_tax_excl      | 30.8   |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I edit product "Test Product Cart Rule On Select Product" to order "bo_order1" with following products details:
      | amount        | 3                       |
      | price         | 15                      |
    Then order "bo_order1" should have 5 products in total
    Then order "bo_order1" should contain 3 products "Test Product Cart Rule On Select Product"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$45.00"
    Then order "bo_order1" should have following details:
      | total_products           | 68.800 |
      | total_products_wt        | 72.930 |
      | total_discounts_tax_excl | 45.000 |
      | total_discounts_tax_incl | 47.7 |
      | total_paid_tax_excl      | 30.8   |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I edit product "Test Product Cart Rule On Select Product" to order "bo_order1" with following products details:
      | amount        | 3                       |
      | price         | 10                      |
    Then order "bo_order1" should have 5 products in total
    Then order "bo_order1" should contain 3 products "Test Product Cart Rule On Select Product"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$30.00"
    Then order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.03  |
      | total_discounts_tax_excl | 30.000 |
      | total_discounts_tax_incl | 31.8   |
      | total_paid_tax_excl      | 30.8   |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product linked to a cart rule to an existing Order without invoice with free shipping and new invoice And add this same product a second time
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "Test Product Cart Rule On Select Product" with a price of 15.0 and 100 items in stock
    And there is a cart rule named "CartRuleAmountOnSelectedProduct" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "CartRuleAmountOnSelectedProduct" has no discount code
    And cart rule "CartRuleAmountOnSelectedProduct" is restricted to product "Test Product Cart Rule On Select Product"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Cart Rule On Select Product  |
      | amount        | 1                                         |
      | price         | 15                                        |
      | free_shipping | true                                      |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product Cart Rule On Select Product"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$7.50"
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 7.5000 |
      | total_discounts_tax_incl | 7.9500 |
      | total_paid_tax_excl      | 38.3   |
      | total_paid_tax_incl      | 40.600 |
      | total_paid               | 40.600 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Cart Rule On Select Product  |
      | amount        | 1                                         |
      | price         | 15                                        |
      | free_shipping | true                                      |
    Then order "bo_order1" should have 4 products in total
    Then order "bo_order1" should contain 2 product "Test Product Cart Rule On Select Product"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$15.00"
    Then order "bo_order1" should have following details:
      | total_products           | 53.800 |
      | total_products_wt        | 57.030 |
      | total_discounts_tax_excl | 15.000 |
      | total_discounts_tax_incl | 15.900 |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add discount to whole order, when a product is added the discount is applied, when a product is removed the discount should still be present
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "Test Product Cart Rule On Order" with a price of 15.0 and 100 items in stock
    Given there is a cart rule named "CartRuleAmountOnWholeOrder" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "CartRuleAmountOnWholeOrder" is applied on order
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Cart Rule On Order |
      | amount        | 1                               |
      | price         | 15                              |
      | free_shipping | true                            |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product Cart Rule On Order"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnWholeOrder" with amount "$19.40"
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 19.400 |
      | total_discounts_tax_incl | 20.570 |
      | total_paid_tax_excl      | 26.4   |
      | total_paid_tax_incl      | 27.980 |
      | total_paid               | 27.980 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove product "Test Product Cart Rule On Order" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should contain 0 product "Test Product Cart Rule On Order"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnWholeOrder" with amount "$11.90"
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 11.900 |
      | total_discounts_tax_incl | 12.620 |
      | total_paid_tax_excl      | 18.900 |
      | total_paid_tax_incl      | 20.030 |
      | total_paid               | 20.030 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  @remove-cart-rule
  Scenario: Add discount to whole order, I remove the discount from order, when I remove a product the generic discount is reapplied
    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a product in the catalog named "Test Product Cart Rule On Order" with a price of 15.0 and 100 items in stock
    Given there is a cart rule named "CartRuleAmountOnWholeOrder" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "CartRuleAmountOnWholeOrder" is applied on order
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Cart Rule On Order |
      | amount        | 1                               |
      | price         | 15                              |
      | free_shipping | true                            |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product Cart Rule On Order"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnWholeOrder" with amount "$19.40"
    Then order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 19.400 |
      | total_discounts_tax_incl | 20.570 |
      | total_paid_tax_excl      | 26.4   |
      | total_paid_tax_incl      | 27.980 |
      | total_paid               | 27.980 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove cart rule "CartRuleAmountOnWholeOrder" from order "bo_order1"
    Then order "bo_order1" should have 0 cart rule
    And order "bo_order1" should not have cart rule "CartRuleAmountOnWholeOrder"
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should have following details:
      | total_products           | 38.800 |
      | total_products_wt        | 41.130 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 45.800 |
      | total_paid_tax_incl      | 48.550 |
      | total_paid               | 48.550 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove product "Test Product Cart Rule On Order" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should contain 0 product "Test Product Cart Rule On Order"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnWholeOrder" with amount "$11.90"
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 11.900 |
      | total_discounts_tax_incl | 12.620 |
      | total_paid_tax_excl      | 18.900 |
      | total_paid_tax_incl      | 20.030 |
      | total_paid               | 20.030 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
