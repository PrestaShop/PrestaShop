# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags multiple-currencies-to-order
@reset-database-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@multiple-currencies-to-order
Feature: Multiple currencies for Order in Back Office (BO)
  In order to manage multiple currencies for orders in BO
  As a BO user
  I need to be able to change order informations and have correct results

  Background:
    Given email sending is disabled
    Given shop "shop1" with name "test_shop" exists
    And the current currency is "USD"
    And country "US" is enabled
    And country "FR" is enabled
    And language "French" with locale "fr-FR" exists
    And I add new currency "currency2" with following properties:
      | iso_code         | EUR        |
      | exchange_rate    | 10.00      |
      | name             | My Euros   |
      | symbols[en-US]   | €          |
      | symbols[fr-FR]   | €          |
      | patterns[en-US]  | ¤#,##0.00  |
      | patterns[fr-FR]  | #,##0.00 ¤ |
      | is_enabled       | 1          |
      | is_unofficial    | 0          |
      | shop_association | shop1      |
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I update the cart "dummy_cart" currency to "currency2"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And the default category of product "Mug The best is yet to come" has no group reduction
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Then order "bo_order1" has 0 payments
    Then order "bo_order1" should have 2 products in total
    Then order "bo_order1" should have 0 invoices
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 308.00 |
      | total_paid_tax_incl      | 326.48 |
      | total_paid               | 326.48 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2      |
      | original_product_price      | 119.00 |
      | product_price               | 119.00 |
      | unit_price_tax_incl         | 126.14 |
      | unit_price_tax_excl         | 119.00 |
      | total_price_tax_incl        | 252.28 |
      | total_price_tax_excl        | 238.00 |

  Scenario: Add cart rule of type 'amount' to an order with secondary currency
    Given I add discount to order "bo_order1" with following details:
      | name  | discount ten-euros |
      | type  | amount             |
      | value | 10                 |
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "discount ten-euros" with amount "€9.43"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 9.43   |
      | total_discounts_tax_incl | 10.0   |
      | total_paid_tax_excl      | 298.57 |
      | total_paid_tax_incl      | 316.48 |
      | total_paid               | 316.48 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |

  Scenario: Add cart rule of type 'percent' to an order with secondary currency
    Given I add discount to order "bo_order1" with following details:
      | name  | discount ten-percents |
      | type  | percent               |
      | value | 10                    |
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "discount ten-percents" with amount "€23.80"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 23.80  |
      | total_discounts_tax_incl | 25.23  |
      | total_paid_tax_excl      | 284.20 |
      | total_paid_tax_incl      | 301.25 |
      | total_paid               | 301.25 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |

  Scenario: Add cart rule of type 'free-shipping' to an order with secondary currency
    Given I add discount to order "bo_order1" with following details:
      | name | discount free-shipping |
      | type | free_shipping          |
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "discount free-shipping" with amount "€70.00"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 70.00  |
      | total_discounts_tax_incl | 74.20  |
      | total_paid_tax_excl      | 238.00 |
      | total_paid_tax_incl      | 252.28 |
      | total_paid               | 252.28 |
      | total_paid_real          | 0.00   |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |

  Scenario: Add cart rule of type 'amount' to an order with secondary currency and a product with specific price
    Given there is a product in the catalog named "Test Product With Discount and SpecificPrice" with a price of 16.0 and 100 items in stock
    And product "Test Product With Discount and SpecificPrice" has a specific price named "discount25" with a discount of 25.0 percent
    And product "Test Product With Discount and SpecificPrice" should have specific price "discount25" with following settings:
      | price          | -1         |
      | from_quantity  | 1          |
      | reduction      | 0.25       |
      | reduction_type | percentage |
      | reduction_tax  | 1          |
    And there is a cart rule named "CartRuleAmountOnSelectedProduct" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
    And cart rule "CartRuleAmountOnSelectedProduct" has no discount code
    And cart rule "CartRuleAmountOnSelectedProduct" is restricted to product "Test Product With Discount and SpecificPrice"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product With Discount and SpecificPrice |
      | amount        | 2                                            |
      | price         | 120                                          |
    Then product "Test Product With Discount and SpecificPrice" in order "bo_order1" should have no specific price
#    For product "Test Product With Discount and SpecificPrice"
#    Due to the specific price 25% of €160, the customer have to pay 75% of the product price : €120
#    We set 120 here to simulate what the user see by default in the BO
    Then order "bo_order1" should have 1 cart rule
    Then order "bo_order1" should have cart rule "CartRuleAmountOnSelectedProduct" with amount "€10.00"
#    The cart rule adds a discount of €10. He will pay a final price of 110
    Then order "bo_order1" should have following details:
      | total_products           | 478.00 |
      | total_products_wt        | 506.68 |
      | total_discounts_tax_excl | 10.00  |
      | total_discounts_tax_incl | 10.60  |
      | total_paid_tax_excl      | 538.00 |
      | total_paid_tax_incl      | 570.28 |
      | total_paid               | 570.28 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 2        |
      | product_price               | 119.00   |
      | original_product_price      | 119.00   |
      | unit_price_tax_incl         | 126.14   |
      | unit_price_tax_excl         | 119.00   |
      | total_price_tax_incl        | 252.28   |
      | total_price_tax_excl        | 238.00   |
    And product "Test Product With Discount and SpecificPrice" in order "bo_order1" has following details:
      | product_quantity       | 2      |
      | original_product_price | 160.00 |
      | product_price          | 120.00 |
      | unit_price_tax_incl    | 127.20 |
      | unit_price_tax_excl    | 120.00 |
      | total_price_tax_incl   | 254.40 |
      | total_price_tax_excl   | 240.00 |

  Scenario: Add product to an order with secondary currency
    When I add products to order "bo_order1" without invoice and the following products details:
      | name   | Mug Today is a good day |
      | amount | 5                       |
      | price  | 15                      |
    Then order "bo_order1" should have 0 cart rule
    Then order "bo_order1" should have following details:
      | total_products           | 313.00 |
      | total_products_wt        | 331.78 |
      | total_discounts_tax_excl | 0.00   |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 383.00 |
      | total_paid_tax_incl      | 405.98 |
      | total_paid               | 405.98 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity       | 2     |
      | original_product_price | 119.00 |
      | product_price          | 119.00 |
      | unit_price_tax_incl    | 126.14 |
      | unit_price_tax_excl    | 119.00 |
      | total_price_tax_incl   | 252.28 |
      | total_price_tax_excl   | 238.00 |

  Scenario: Update product in order with secondary currency
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount | 3     |
      | price  | 12.00 |
    Then order "bo_order1" should contain 3 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity       | 3      |
      | original_product_price | 119.00 |
      | product_price          | 12.00  |
      | unit_price_tax_incl    | 12.72  |
      | unit_price_tax_excl    | 12.00  |
      | total_price_tax_incl   | 38.16  |
      | total_price_tax_excl   | 36.00  |
    And order "bo_order1" should have following details:
      | total_products           | 36.00  |
      | total_products_wt        | 38.16  |
      | total_discounts_tax_excl | 0.00   |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 106.00 |
      | total_paid_tax_incl      | 112.36 |
      | total_paid               | 112.36 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |

  Scenario: Update product quantity in order with secondary currency when its category has discount
    Given the default category of product "Mug The best is yet to come" has a group reduction of 50.00% for the customer "testCustomer"
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | 3                       |
      | price         | 11.90                   |
    Then order "bo_order1" should contain 3 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 3      |
      | original_product_price      | 119.00 |
      | product_price               | 11.90  |
      | unit_price_tax_incl         | 12.614 |
      | unit_price_tax_excl         | 11.90  |
      | total_price_tax_incl        | 37.84  |
      | total_price_tax_excl        | 35.70  |
    And order "bo_order1" should have following details:
      | total_products           | 35.70 |
      | total_products_wt        | 37.84 |
      | total_discounts_tax_excl | 0.00   |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 105.70 |
      | total_paid_tax_incl      | 112.04 |
      | total_paid               | 112.04 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount        | 3                       |
      | price         | 20.00                   |
    Then order "bo_order1" should contain 3 products "Mug The best is yet to come"
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity            | 3      |
      | original_product_price      | 119.00 |
      | product_price               | 20.00  |
      | unit_price_tax_incl         | 21.20  |
      | unit_price_tax_excl         | 20.00  |
      | total_price_tax_incl        | 63.60  |
      | total_price_tax_excl        | 60.00  |
    And order "bo_order1" should have following details:
      | total_products           | 60.00 |
      | total_products_wt        | 63.60 |
      | total_discounts_tax_excl | 0.00   |
      | total_discounts_tax_incl | 0.00   |
      | total_paid_tax_excl      | 130.00 |
      | total_paid_tax_incl      | 137.80 |
      | total_paid               | 137.80 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |

  Scenario: Check invoice for an order with secondary currency and discount
    Given I add discount to order "bo_order1" with following details:
      | name  | discount ten-euros |
      | type  | amount             |
      | value | 10                 |
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have 1 invoice
    And the first invoice from order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 238.00 |
      | total_products_wt       | 252.28 |
      | total_discount_tax_excl | 9.43   |
      | total_discount_tax_incl | 10.0   |
      | total_paid_tax_excl     | 298.57 |
      | total_paid_tax_incl     | 316.48 |
      | total_shipping_tax_excl | 70.00  |
      | total_shipping_tax_incl | 74.20  |

  Scenario: Change delivery address
    Given I add new address to customer "testCustomer" with following details:
      | Address alias | test-customer-france-address |
      | First name    | testFirstName                |
      | Last name     | testLastName                 |
      | Address       | 36 Avenue des Champs Elysees |
      | City          | Paris                        |
      | Country       | France                       |
      | Postal code   | 75008                        |
    And I change order "bo_order1" shipping address to "test-customer-france-address"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 238.00 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 308.00 |
      | total_paid_tax_incl      | 308.00 |
      | total_paid               | 308.00 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 70.00  |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 70.00 |
      | shipping_cost_tax_incl | 70.00 |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity       | 2      |
      | original_product_price | 119.00 |
      | product_price          | 119.00 |
      | unit_price_tax_incl    | 119.00 |
      | unit_price_tax_excl    | 119.00 |
      | total_price_tax_incl   | 238.00 |
      | total_price_tax_excl   | 238.00 |

  Scenario: Change invoice address
    Given I add new address to customer "testCustomer" with following details:
      | Address alias | test-customer-france-address |
      | First name    | testFirstName                |
      | Last name     | testLastName                 |
      | Address       | 36 Avenue des Champs Elysees |
      | City          | Paris                        |
      | Country       | France                       |
      | Postal code   | 75008                        |
    And I change order "bo_order1" invoice address to "test-customer-france-address"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 308.00 |
      | total_paid_tax_incl      | 326.48 |
      | total_paid               | 326.48 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 70.00  |
      | total_shipping_tax_incl  | 74.20  |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 70.00 |
      | shipping_cost_tax_incl | 74.20 |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity       | 2      |
      | original_product_price | 119.00 |
      | product_price          | 119.00 |
      | unit_price_tax_incl    | 126.14 |
      | unit_price_tax_excl    | 119.00 |
      | total_price_tax_incl   | 252.28 |
      | total_price_tax_excl   | 238.00 |

  Scenario: Carrier change for an order with secondary currency
    Given a carrier "default_carrier" with name "My carrier" exists
    And a carrier "price_carrier" with name "My cheap carrier" exists
    And I enable carrier "price_carrier"
    And order "bo_order1" should have "default_carrier" as a carrier
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 70.00 |
      | shipping_cost_tax_incl | 74.20 |
    When I update order "bo_order1" Tracking number to "TEST1234" and Carrier to "price_carrier"
    Then order "bo_order1" should have following details:
      | total_products           | 238.00 |
      | total_products_wt        | 252.28 |
      | total_discounts_tax_excl | 0.0    |
      | total_discounts_tax_incl | 0.0    |
      | total_paid_tax_excl      | 298.00 |
      | total_paid_tax_incl      | 315.88 |
      | total_paid               | 315.88 |
      | total_paid_real          | 0.0    |
      | total_shipping_tax_excl  | 60.00  |
      | total_shipping_tax_incl  | 63.60  |
    And order "bo_order1" carrier should have following details:
      | weight                 | 0.600 |
      | shipping_cost_tax_excl | 60.00 |
      | shipping_cost_tax_incl | 63.60 |
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity       | 2      |
      | original_product_price | 119.00 |
      | product_price          | 119.00 |
      | unit_price_tax_incl    | 126.14 |
      | unit_price_tax_excl    | 119.00 |
      | total_price_tax_incl   | 252.28 |
      | total_price_tax_excl   | 238.00 |

  @reset-database-before-scenario
    # We reset database before this scenario to be sure only default_carrier is enabled
    Scenario: I add the product with associated gift when the order already has the gift
      Given there is a product in the catalog named "Test Product With Auto Gift" with a price of 12.0 and 100 items in stock
      And there is a product in the catalog named "Test Product Gifted" with a price of 15.0 and 100 items in stock
      And there is a cart rule named "MultiGiftAutoCartRule" that applies an amount discount of 1.0 with priority 1, quantity of 100 and quantity per user 100
      And cart rule "MultiGiftAutoCartRule" has no discount code
      And cart rule "MultiGiftAutoCartRule" is restricted to product "Test Product With Auto Gift"
      And cart rule "MultiGiftAutoCartRule" offers free shipping
      And cart rule "MultiGiftAutoCartRule" offers a gift product "Test Product Gifted"
      And I add products to order "bo_order1" with new invoice and the following products details:
        | name   | Test Product Gifted |
        | amount | 1                   |
        | price  | 150.0               |
      Then order "bo_order1" should have 3 products in total
      And order "bo_order1" should have 0 invoice
      And order "bo_order1" should have 0 cart rule
      And order "bo_order1" should have "default_carrier" as a carrier
      And order "bo_order1" should have following details:
        | total_products           | 388.00 |
        | total_products_wt        | 411.28 |
        | total_discounts_tax_excl | 0.0    |
        | total_discounts_tax_incl | 0.0    |
        | total_paid_tax_excl      | 458.00 |
        | total_paid_tax_incl      | 485.48 |
        | total_paid               | 485.48 |
        | total_paid_real          | 0.0    |
        | total_shipping_tax_excl  | 70.00  |
        | total_shipping_tax_incl  | 74.20  |
      And order "bo_order1" carrier should have following details:
        | weight                 | 0.600 |
        | shipping_cost_tax_excl | 70.00 |
        | shipping_cost_tax_incl | 74.20 |
      And product "Mug The best is yet to come" in order "bo_order1" has following details:
        | product_quantity       | 2      |
        | original_product_price | 119.00 |
        | product_price          | 119.00 |
        | unit_price_tax_incl    | 126.14 |
        | unit_price_tax_excl    | 119.00 |
        | total_price_tax_incl   | 252.28 |
        | total_price_tax_excl   | 238.00 |
      And product "Test Product Gifted" in order "bo_order1" has following details:
        | product_quantity       | 1      |
        | original_product_price | 150.00 |
        | product_price          | 150.00 |
        | unit_price_tax_incl    | 159.00 |
        | unit_price_tax_excl    | 150.00 |
        | total_price_tax_incl   | 159.00 |
        | total_price_tax_excl   | 150.00 |
      When I add products to order "bo_order1" with new invoice and the following products details:
        | name   | Test Product With Auto Gift |
        | amount | 1                           |
        | price  | 120.00                      |
      Then order "bo_order1" should have 5 products in total
      And order "bo_order1" should have 0 invoice
      And order "bo_order1" should have 1 cart rule
      And order "bo_order1" should have following details:
        | total_products           | 658.00 |
        | total_products_wt        | 697.48 |
        | total_discounts_tax_excl | 230.0  |
        | total_discounts_tax_incl | 243.80 |
        | total_paid_tax_excl      | 498.00 |
        | total_paid_tax_incl      | 527.88 |
        | total_paid               | 527.88 |
        | total_paid_real          | 0.0    |
        | total_shipping_tax_excl  | 70.00  |
        | total_shipping_tax_incl  | 74.20  |
      And order "bo_order1" carrier should have following details:
        | weight                 | 0.600 |
        | shipping_cost_tax_excl | 70.00 |
        | shipping_cost_tax_incl | 74.20 |
      And order "bo_order1" should have cart rule "MultiGiftAutoCartRule" with amount "€230.00"
      And product "Mug The best is yet to come" in order "bo_order1" has following details:
        | product_quantity       | 2      |
        | original_product_price | 119.00 |
        | product_price          | 119.00 |
        | unit_price_tax_incl    | 126.14 |
        | unit_price_tax_excl    | 119.00 |
        | total_price_tax_incl   | 252.28 |
        | total_price_tax_excl   | 238.00 |
      And product "Test Product Gifted" in order "bo_order1" has following details:
        | product_quantity       | 2      |
        | original_product_price | 150.00 |
        | product_price          | 150.00 |
        | unit_price_tax_incl    | 159.00 |
        | unit_price_tax_excl    | 150.00 |
        | total_price_tax_incl   | 318.00 |
        | total_price_tax_excl   | 300.00 |
      And product "Test Product With Auto Gift" in order "bo_order1" has following details:
        | product_quantity       | 1      |
        | original_product_price | 120.00 |
        | product_price          | 120.00 |
        | unit_price_tax_incl    | 127.20 |
        | unit_price_tax_excl    | 120.00 |
        | total_price_tax_incl   | 127.20 |
        | total_price_tax_excl   | 120.00 |
      When I remove cart rule "MultiGiftAutoCartRule" from order "bo_order1"
      Then order "bo_order1" should have 4 products in total
      And order "bo_order1" should have 0 invoice
      And order "bo_order1" should have 0 cart rule
      And order "bo_order1" should have following details:
        | total_products           | 508.00 |
        | total_products_wt        | 538.48 |
        | total_discounts_tax_excl | 0.00   |
        | total_discounts_tax_incl | 0.00   |
        | total_paid_tax_excl      | 578.00 |
        | total_paid_tax_incl      | 612.68 |
        | total_paid               | 612.68 |
        | total_paid_real          | 0.0    |
        | total_shipping_tax_excl  | 70.00  |
        | total_shipping_tax_incl  | 74.20  |
      And order "bo_order1" carrier should have following details:
        | weight                 | 0.600 |
        | shipping_cost_tax_excl | 70.00 |
        | shipping_cost_tax_incl | 74.20 |
      And product "Mug The best is yet to come" in order "bo_order1" has following details:
        | product_quantity       | 2      |
        | original_product_price | 119.00 |
        | product_price          | 119.00 |
        | unit_price_tax_incl    | 126.14 |
        | unit_price_tax_excl    | 119.00 |
        | total_price_tax_incl   | 252.28 |
        | total_price_tax_excl   | 238.00 |
      And product "Test Product Gifted" in order "bo_order1" has following details:
        | product_quantity       | 1      |
        | original_product_price | 150.00 |
        | product_price          | 150.00 |
        | unit_price_tax_incl    | 159.00 |
        | unit_price_tax_excl    | 150.00 |
        | total_price_tax_incl   | 159.00 |
        | total_price_tax_excl   | 150.00 |
      And product "Test Product With Auto Gift" in order "bo_order1" has following details:
        | product_quantity       | 1      |
        | original_product_price | 120.00 |
        | product_price          | 120.00 |
        | unit_price_tax_incl    | 127.20 |
        | unit_price_tax_excl    | 120.00 |
        | total_price_tax_incl   | 127.20 |
        | total_price_tax_excl   | 120.00 |
