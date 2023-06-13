# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-cart-rules
@restore-all-tables-before-feature
@order-cart-rules
@clear-cache-before-feature
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  Background:
    Given email sending is disabled
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And the current currency is "USD"
    And country "US" is enabled
    And language with iso code "en" is the default one
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
    And there is a product in the catalog named "Test Product Cart Rule On Select Product" with a price of 15.0 and 100 items in stock
    And there is a cart rule "CartRuleAmountOnSelectedProduct" with following properties:
      | name[en-US]               | CartRuleAmountOnSelectedProduct          |
      | priority                  | 1                                        |
      | discount_amount           | 500                                      |
      | discount_currency         | usd                                      |
      | discount_application_type | specific_product                         |
      | discount_product          | Test Product Cart Rule On Select Product |
      | discount_includes_tax     | true                                     |
      | quantity                  | 100                                      |
      | quantity_per_user         | 100                                      |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product Cart Rule On Select Product |
      | amount | 1                                        |
      | price  | 15                                       |
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

  @restore-cart-rules-before-scenario
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
    And there is a product in the catalog named "Test Product Cart Rule On Select Product" with a price of 15.0 and 100 items in stock
    And there is a cart rule named "CartRuleAmountOnSelectedProduct" that applies an amount discount of 500.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "CartRuleAmountOnSelectedProduct" has no discount code
    And cart rule "CartRuleAmountOnSelectedProduct" is restricted to product "Test Product Cart Rule On Select Product"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product Cart Rule On Select Product |
      | amount | 1                                        |
      | price  | 15                                       |
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
      | amount | 3  |
      | price  | 15 |
    Then order "bo_order1" should have 5 products in total
    Then order "bo_order1" should contain 3 products "Test Product Cart Rule On Select Product"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$45.00"
    Then order "bo_order1" should have following details:
      | total_products           | 68.800 |
      | total_products_wt        | 72.930 |
      | total_discounts_tax_excl | 45.000 |
      | total_discounts_tax_incl | 47.7   |
      | total_paid_tax_excl      | 30.8   |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I edit product "Test Product Cart Rule On Select Product" to order "bo_order1" with following products details:
      | amount | 3  |
      | price  | 10 |
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

  # This scenario raised some problems regarding the multi invoice shipping (Order total are not in synced with totals from invoices)
  # @todo This test is commented for now and will have to be fixed along with the issue #20409
#  Scenario: Add product linked to a cart rule to an existing Order without invoice with free shipping and new invoice And add this same product a second time
#    Given order with reference "bo_order1" does not contain product "Mug Today is a good day"
#    Then order "bo_order1" should have 2 products in total
#    Then order "bo_order1" should have 0 invoices
#    Then order "bo_order1" should have 0 cart rule
#    Then order "bo_order1" should have following details:
#      | total_products           | 23.800 |
#      | total_products_wt        | 25.230 |
#      | total_discounts_tax_excl | 0.0    |
#      | total_discounts_tax_incl | 0.0    |
#      | total_paid_tax_excl      | 30.800 |
#      | total_paid_tax_incl      | 32.650 |
#      | total_paid               | 32.650 |
#      | total_paid_real          | 0.0    |
#      | total_shipping_tax_excl  | 7.0    |
#      | total_shipping_tax_incl  | 7.42   |
#    Given shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
#    And there is a product in the catalog named "Test Product Cart Rule On Select Product" with a price of 15.0 and 100 items in stock
#    And there is a cart rule named "CartRuleAmountOnSelectedProduct" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
#    And cart rule "CartRuleAmountOnSelectedProduct" has no discount code
#    And cart rule "CartRuleAmountOnSelectedProduct" is restricted to product "Test Product Cart Rule On Select Product"
#    When I add products to order "bo_order1" with new invoice and the following products details:
#      | name          | Test Product Cart Rule On Select Product  |
#      | amount        | 1                                         |
#      | price         | 15                                        |
#      | free_shipping | true                                      |
#    Then order "bo_order1" should have 3 products in total
#    Then order "bo_order1" should contain 1 product "Test Product Cart Rule On Select Product"
#    Then order "bo_order1" should have 1 cart rule
#    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$7.50"
#    Then order "bo_order1" should have following details:
#      | total_products           | 38.800 |
#      | total_products_wt        | 41.130 |
#      | total_discounts_tax_excl | 7.5000 |
#      | total_discounts_tax_incl | 7.9500 |
#      | total_paid_tax_excl      | 38.3   |
#      | total_paid_tax_incl      | 40.600 |
#      | total_paid               | 40.600 |
#      | total_paid_real          | 0.0    |
#      | total_shipping_tax_excl  | 7.0    |
#      | total_shipping_tax_incl  | 7.42   |
#    Given I update order "bo_order1" status to "Payment accepted"
#    And order "bo_order1" should have 1 invoice
#    When I add products to order "bo_order1" with new invoice and the following products details:
#      | name          | Test Product Cart Rule On Select Product  |
#      | amount        | 1                                         |
#      | price         | 15                                        |
#      | free_shipping | true                                      |
#    Then order "bo_order1" should have 4 products in total
#    Then order "bo_order1" should contain 2 product "Test Product Cart Rule On Select Product"
#    Then order "bo_order1" should have 2 cart rule
#    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "$15.00"
#    Then order "bo_order1" should have cart rule "[Generated] CartRule for Free Shipping" with amount "$7.00"
#    Then order "bo_order1" should have following details:
#      | total_products           | 53.800 |
#      | total_products_wt        | 57.030 |
#      | total_discounts_tax_excl | 22.000 |
#      | total_discounts_tax_incl | 23.320 |
#      | total_paid_tax_excl      | 45.800 |
#      | total_paid_tax_incl      | 48.550 |
#      | total_paid               | 48.550 |
#      | total_paid_real          | 40.600 |
#      | total_shipping_tax_excl  | 14.00  |
#      | total_shipping_tax_incl  | 14.84  |

  @restore-cart-rules-before-scenario
  Scenario: Add discount to all orders, when a product is added the discount is applied, when a product is removed the discount should still be present
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
    And there is a product in the catalog named "Test Product Cart Rule On Order" with a price of 15.0 and 100 items in stock
    And there is a cart rule "CartRuleAmountOnEveryOrder" with following properties:
      | name[en-US]               | CartRuleAmountOnEveryOrder |
      | priority                  | 1                          |
      | free_shipping             | false                      |
      | discount_percentage       | 50                         |
      | discount_application_type | order_without_shipping     |
      | quantity                  | 1000                       |
      | quantity_per_user         | 1000                       |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product Cart Rule On Order |
      | amount | 1                               |
      | price  | 15                              |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product Cart Rule On Order"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnEveryOrder" with amount "$19.40"
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
    Then order "bo_order1" should have cart rule "CartRuleAmountOnEveryOrder" with amount "$11.90"
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

  @restore-cart-rules-before-scenario
  Scenario: Add discount to every orders, I remove the discount from order, it is automatically added again until I inactivate it
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
    And there is a product in the catalog named "Test Product Cart Rule On Order" with a price of 15.0 and 100 items in stock
    And there is a cart rule "CartRuleAmountOnEveryOrder" with following properties:
      | name[en-US]               | CartRuleAmountOnEveryOrder |
      | priority                  | 1                          |
      | free_shipping             | false                      |
      | discount_percentage       | 50                         |
      | discount_application_type | order_without_shipping     |
      | quantity                  | 1000                       |
      | quantity_per_user         | 1000                       |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product Cart Rule On Order |
      | amount | 1                               |
      | price  | 15                              |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product Cart Rule On Order"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnEveryOrder" with amount "$19.40"
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
    # The cart rule is removed but added again automatically when the order is synced with cart and shop cart rules
    When I remove cart rule "CartRuleAmountOnEveryOrder" from order "bo_order1"
    Then order "bo_order1" should have 0 cart rule
    And order "bo_order1" should not have cart rule "CartRuleAmountOnEveryOrder"
    Then order "bo_order1" should have following details:
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
    # Even after removing a product the cart rule is not automatically added
    When I remove product "Test Product Cart Rule On Order" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should contain 0 product "Test Product Cart Rule On Order"
    Then order "bo_order1" should have 0 cart rule
    And order "bo_order1" should not have cart rule "CartRuleAmountOnEveryOrder"
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 0.0000 |
      | total_discounts_tax_incl | 0.0000 |
      | total_paid_tax_excl      | 30.800 |
      | total_paid_tax_incl      | 32.650 |
      | total_paid               | 32.650 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  @restore-cart-rules-before-scenario
  Scenario: Add product with associated discount to order, Add discount to the specific order, when I remove a product the order specific discount is still present
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
    And there is a product in the catalog named "Test Product With Percent Discount" with a price of 350.00 and 100 items in stock
    And there is a cart rule "CartRulePercentForSpecificProduct" with following properties:
      | name[en-US]               | CartRulePercentForSpecificProduct  |
      | priority                  | 1                                  |
      | free_shipping             | false                              |
      | discount_percentage       | 50                                 |
      | discount_application_type | specific_product                   |
      | quantity                  | 1000                               |
      | quantity_per_user         | 1000                               |
      | discount_product          | Test Product With Percent Discount |
    And cart rule "CartRulePercentForSpecificProduct" is restricted to product "Test Product With Percent Discount"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product With Percent Discount |
      | amount | 1                                  |
      | price  | 350.00                             |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product With Percent Discount"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRulePercentForSpecificProduct" with amount "$175.00"
    Then order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 175.00 |
      | total_discounts_tax_incl | 185.50 |
      | total_paid_tax_excl      | 205.80 |
      | total_paid_tax_incl      | 218.15 |
      | total_paid               | 218.15 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I add discount to order "bo_order1" with following details:
      | name  | discount five-percent |
      | type  | percent               |
      | value | 5                     |
    Then order "bo_order1" should have 2 cart rule
    Then order "bo_order1" should have cart rule "CartRulePercentForSpecificProduct" with amount "$175.00"
    Then order "bo_order1" should have cart rule "discount five-percent" with amount "$9.94"
    Then order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 184.94 |
      | total_discounts_tax_incl | 196.04 |
      | total_paid_tax_excl      | 195.86 |
      | total_paid_tax_incl      | 207.61 |
      | total_paid               | 207.61 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove product "Test Product With Percent Discount" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should contain 0 product "Test Product With Percent Discount"
    # @todo: for some reason it still has 2 rules. The one that shouldn't exist (because product was deleted) is CartRulePercentForSpecificProduct, but after product removal, this cart rule value in order_cart_rule becomes 0 (instead of removing the cart rule completely). HOW THE F* IT WORKED BEFORE?
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "discount five-percent" with amount "$1.19"
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 1.190  |
      | total_discounts_tax_incl | 1.260  |
      | total_paid_tax_excl      | 29.610 |
      | total_paid_tax_incl      | 31.390 |
      | total_paid               | 31.390 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product with associated discount to order, Add discount to the specific order, I remove the discount of this product, if I add the product again the discount is still removed
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
    And there is a product in the catalog named "Test Product With Percent Discount" with a price of 350.00 and 100 items in stock
    Given there is a cart rule named "CartRulePercentForSpecificProduct" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "CartRulePercentForSpecificProduct" is restricted to product "Test Product With Percent Discount"
    And there is a cart rule "CartRulePercentForSpecificProduct" with following properties:
      | name[en-US]               | CartRulePercentForSpecificProduct  |
      | priority                  | 1                                  |
      | free_shipping             | false                              |
      | discount_percentage       | 50                                 |
      | discount_application_type | specific_product                   |
      | discount_application_type | specific_product                   |
      | quantity                  | 1000                               |
      | quantity_per_user         | 1000                               |
      | discount_product          | Test Product With Percent Discount |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product With Percent Discount |
      | amount | 1                                  |
      | price  | 350.00                             |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product With Percent Discount"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRulePercentForSpecificProduct" with amount "$175.00"
    Then order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 175.00 |
      | total_discounts_tax_incl | 185.50 |
      | total_paid_tax_excl      | 205.80 |
      | total_paid_tax_incl      | 218.15 |
      | total_paid               | 218.15 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I add discount to order "bo_order1" with following details:
      | name  | discount five-percent |
      | type  | percent               |
      | value | 5                     |
    Then order "bo_order1" should have 2 cart rule
    Then order "bo_order1" should have cart rule "CartRulePercentForSpecificProduct" with amount "$175.00"
    Then order "bo_order1" should have cart rule "discount five-percent" with amount "$9.94"
    Then order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 184.94 |
      | total_discounts_tax_incl | 196.04 |
      | total_paid_tax_excl      | 195.86 |
      | total_paid_tax_incl      | 207.61 |
      | total_paid               | 207.61 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove cart rule "CartRulePercentForSpecificProduct" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product With Percent Discount"
    And order "bo_order1" should not have cart rule "CartRulePercentForSpecificProduct"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "discount five-percent" with amount "$18.69"
    Then order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 18.69  |
      | total_discounts_tax_incl | 19.81  |
      | total_paid_tax_excl      | 362.11 |
      | total_paid_tax_incl      | 383.84 |
      | total_paid               | 383.84 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add product with associated discount to order, I remove the discount of this product, if I remove the product and add it again the discount is applied again
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
    And there is a product in the catalog named "Test Product With Percent Discount" with a price of 350.00 and 100 items in stock
    Given there is a cart rule named "CartRulePercentForSpecificProduct" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "CartRulePercentForSpecificProduct" is restricted to product "Test Product With Percent Discount"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product With Percent Discount |
      | amount | 1                                  |
      | price  | 350.00                             |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product With Percent Discount"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRulePercentForSpecificProduct" with amount "$175.00"
    Then order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 175.00 |
      | total_discounts_tax_incl | 185.50 |
      | total_paid_tax_excl      | 205.80 |
      | total_paid_tax_incl      | 218.15 |
      | total_paid               | 218.15 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove cart rule "CartRulePercentForSpecificProduct" from order "bo_order1"
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product With Percent Discount"
    And order "bo_order1" should not have cart rule "CartRulePercentForSpecificProduct"
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 0.000  |
      | total_discounts_tax_incl | 0.000  |
      | total_paid_tax_excl      | 380.80 |
      | total_paid_tax_incl      | 403.65 |
      | total_paid               | 403.65 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove product "Test Product With Percent Discount" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should contain 0 product "Test Product With Percent Discount"
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
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product With Percent Discount |
      | amount | 1                                  |
      | price  | 350.00                             |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product With Percent Discount"
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRulePercentForSpecificProduct" with amount "$175.00"
    Then order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 175.00 |
      | total_discounts_tax_incl | 185.50 |
      | total_paid_tax_excl      | 205.80 |
      | total_paid_tax_incl      | 218.15 |
      | total_paid               | 218.15 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |

  Scenario: Add discount to the specific order, then remove it When I perform add/remove product actions the discount is not reapplied
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
    When I add discount to order "bo_order1" with following details:
      | name  | discount five-percent |
      | type  | percent               |
      | value | 5                     |
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "discount five-percent" with amount "$1.19"
    Then order "bo_order1" should have following details:
      | total_products           | 23.800 |
      | total_products_wt        | 25.230 |
      | total_discounts_tax_excl | 1.190  |
      | total_discounts_tax_incl | 1.260  |
      | total_paid_tax_excl      | 29.610 |
      | total_paid_tax_incl      | 31.390 |
      | total_paid               | 31.390 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    And there is a product in the catalog named "Test Product With Percent Discount" with a price of 350.00 and 100 items in stock
    Given there is a cart rule named "CartRulePercentForSpecificProduct" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    And cart rule "CartRulePercentForSpecificProduct" is restricted to product "Test Product With Percent Discount"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name   | Test Product With Percent Discount |
      | amount | 1                                  |
      | price  | 350.00                             |
    Then order "bo_order1" should have 3 products in total
    Then order "bo_order1" should contain 1 product "Test Product With Percent Discount"
    Then order "bo_order1" should have 2 cart rule
    Then order "bo_order1" should have cart rule "discount five-percent" with amount "$18.69"
    Then order "bo_order1" should have cart rule "CartRulePercentForSpecificProduct" with amount "$166.25"
    Then order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 184.94 |
      | total_discounts_tax_incl | 196.04 |
      | total_paid_tax_excl      | 195.86 |
      | total_paid_tax_incl      | 207.61 |
      | total_paid               | 207.61 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove cart rule "discount five-percent" from order "bo_order1"
    Then order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have cart rule "CartRulePercentForSpecificProduct" with amount "$175.00"
    And order "bo_order1" should have following details:
      | total_products           | 373.80 |
      | total_products_wt        | 396.23 |
      | total_discounts_tax_excl | 175.00 |
      | total_discounts_tax_incl | 185.50 |
      | total_paid_tax_excl      | 205.80 |
      | total_paid_tax_incl      | 218.15 |
      | total_paid               | 218.15 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 7.0    |
      | total_shipping_tax_incl  | 7.42   |
    When I remove product "Test Product With Percent Discount" from order "bo_order1"
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should contain 0 product "Test Product With Percent Discount"

  Scenario: When a cart rule is associated to a carrier, when I change the carrier the cart rule should be added/removed accordingly
    Given there is a product in the catalog named "product1" with a price of 10.00 and 100 items in stock
    And there is a product in the catalog named "product2" with a price of 15.00 and 100 items in stock
    And there is a zone named "zone1"
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a carrier named "carrier1"
    And there is a carrier named "carrier2"
    And carrier "carrier1" applies shipping fees of 0.0 in zone "zone1" for price between 0 and 10000
    And carrier "carrier2" applies shipping fees of 0.0 in zone "zone1" for price between 0 and 10000
    And there is a cart rule named "FreeGift" that applies no discount with priority 1, quantity of 1 and quantity per user 1
    And cart rule "FreeGift" offers a gift product "product1"
    And cart rule "FreeGift" is restricted to carrier "carrier1"
    When I create an empty cart "dummy_cart_freegift" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart_freegift"
    And I add 1 products "product2" to the cart "dummy_cart_freegift"
    Then cart "dummy_cart_freegift" should contain 1 products
    When I select carrier "carrier1" for cart "dummy_cart_freegift"
    Then cart "dummy_cart_freegift" should contain 2 products
    When I select carrier "carrier2" for cart "dummy_cart_freegift"
    Then cart "dummy_cart_freegift" should contain 1 products

  Scenario: Add a cart rule with free shipping to an order with a total of 0
    Given there is a product in the catalog named "product1" with a price of 0.00 and 100 items in stock
    When I create an empty cart "dummy_cart_free_shipping" for customer "testCustomer"
    And I add 1 products "product1" to the cart "dummy_cart_free_shipping"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart_free_shipping"
    Then cart "dummy_cart_free_shipping" should contain 1 products
    When I add order "bo_order1" with the following details:
      | cart                | dummy_cart_free_shipping   |
      | message             |                            |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 0.000 |
      | total_products_wt        | 0.000 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 7.000 |
      | total_paid_tax_incl      | 7.420 |
      | total_paid               | 7.420 |
      | total_paid_real          | 0.000 |
      | total_shipping_tax_excl  | 7.000 |
      | total_shipping_tax_incl  | 7.420 |
    When I add discount to order "bo_order1" with following details:
      | name | Free Shipping |
      | type | free_shipping |
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have 1 products in total
    And order "bo_order1" should have following details:
      | total_products           | 0.000 |
      | total_products_wt        | 0.000 |
      | total_discounts_tax_excl | 7.000 |
      | total_discounts_tax_incl | 7.420 |
      | total_paid_tax_excl      | 0.000 |
      | total_paid_tax_incl      | 0.000 |
      | total_paid               | 0.000 |
      | total_paid_real          | 0.000 |
      | total_shipping_tax_excl  | 7.000 |
      | total_shipping_tax_incl  | 7.420 |

  Scenario: Add a cart rule with free shipping to an order with a total of 0 and existing order
    Given there is a product in the catalog named "product1" with a price of 0.00 and 100 items in stock
    When I create an empty cart "dummy_cart_free_shipping" for customer "testCustomer"
    And I add 1 products "product1" to the cart "dummy_cart_free_shipping"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart_free_shipping"
    Then cart "dummy_cart_free_shipping" should contain 1 products
    When I add order "bo_order1" with the following details:
      | cart                | dummy_cart_free_shipping |
      | message             |                          |
      | payment module name | dummy_payment            |
      | status              | Payment accepted         |
    Then order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 0.000 |
      | total_products_wt        | 0.000 |
      | total_discounts_tax_excl | 0.000 |
      | total_discounts_tax_incl | 0.000 |
      | total_paid_tax_excl      | 7.000 |
      | total_paid_tax_incl      | 7.420 |
      | total_paid               | 7.420 |
      | total_paid_real          | 7.420 |
      | total_shipping_tax_excl  | 7.000 |
      | total_shipping_tax_incl  | 7.420 |
    And order "bo_order1" should have invoice
    When I add discount to order "bo_order1" on first invoice and following details:
      | name | Free Shipping |
      | type | free_shipping |
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have 1 products in total
    And order "bo_order1" should have following details:
      | total_products           | 0.000 |
      | total_products_wt        | 0.000 |
      | total_discounts_tax_excl | 7.000 |
      | total_discounts_tax_incl | 7.420 |
      | total_paid_tax_excl      | 0.000 |
      | total_paid_tax_incl      | 0.000 |
      | total_paid               | 0.000 |
      | total_paid_real          | 7.420 |
      | total_shipping_tax_excl  | 7.000 |
      | total_shipping_tax_incl  | 7.420 |

  Scenario: Add a cart rule with free shipping to an order with a total of 0 and existing order
    Given there is a product in the catalog named "product_expensive" with a price of 123.00 and 100 items in stock
    And there is a product in the catalog named "product_cheap" with a price of 10.00 and 100 items in stock
    And I create an empty cart "dummy_cart_cart_rule_cheapest" for customer "testCustomer"
    And I add 1 products "product_expensive" to the cart "dummy_cart_cart_rule_cheapest"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart_cart_rule_cheapest"
    And cart "dummy_cart_cart_rule_cheapest" should contain 1 products
    ## Create an order with the expensive product
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart_cart_rule_cheapest |
      | message             |                               |
      | payment module name | dummy_payment                 |
      | status              | Payment accepted              |
    And order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 cart rule
    And order "bo_order1" should have following details:
      | total_products           | 123.000 |
      | total_products_wt        | 130.380 |
      | total_discounts_tax_excl | 0.000   |
      | total_discounts_tax_incl | 0.000   |
      | total_paid_tax_excl      | 130.000 |
      | total_paid_tax_incl      | 137.80  |
      | total_paid               | 137.80  |
      | total_paid_real          | 137.80  |
      | total_shipping_tax_excl  | 7.000   |
      | total_shipping_tax_incl  | 7.42    |
    And order "bo_order1" should have invoice
    ## Create a new cart rule
    And I create cart rule "cart_rule_1" with following properties:
      | name[en-US]                            | Cart Rule 50% which excludes discounted products and applies to cheapest product |
      | reduction_percentage                   | 50                                                                               |
      | reduction_apply_to_discounted_products | false                                                                            |
      | discount_application_type              | cheapest_product                                                                 |
    ## Add the product to the order
    When I add products to order "bo_order1" without invoice and the following products details:
      | name   | product_cheap |
      | amount | 1             |
      | price  | 10            |
    Then order "bo_order1" should have 2 products in total
    And order "bo_order1" should have 1 cart rule
    And order "bo_order1" should have a cart rule with name "Cart Rule 50% which excludes discounted products and applies to cheapest product"
    And order "bo_order1" should have following details:
      | total_products           | 133.000 |
      | total_products_wt        | 140.980 |
      | total_discounts_tax_excl | 5.000   |
      | total_discounts_tax_incl | 5.300   |
      | total_paid_tax_excl      | 135.000 |
      | total_paid_tax_incl      | 143.100 |
      | total_paid               | 143.100 |
      | total_paid_real          | 137.800 |
      | total_shipping_tax_excl  | 7.000   |
      | total_shipping_tax_incl  | 7.420   |
