# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-split-cart-rule
@restore-all-tables-before-feature
@clear-cache-before-feature
@reboot-kernel-before-feature
@restore-currencies-after-feature
@reboot-kernel-after-feature
@restore-currencies-before-scenario
@order-split-cart-rule
Feature: Cart rule on an order split by carrier
  As a shop owner
  When a cart is split into several orders because its products share no carrier
  I want the discount the customer accepted to still add up to the amount they were shown

  Background:
    Given email sending is disabled
    And shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 1.0
    And the current currency is "USD"
    And I add new currency "eur" with following properties:
      | iso_code         | EUR       |
      | exchange_rate    | 10.00     |
      | name             | My Euros  |
      | symbols[en-US]   | €         |
      | patterns[en-US]  | ¤#,##0.00 |
      | is_enabled       | 1         |
      | is_unofficial    | 0         |
      | shop_association | shop1     |
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And there is a zone "north_america" named "North America"
    When I create carrier "beer_carrier" with specified properties:
      | name             | Beer carrier  |
      | active           | true          |
      | shippingMethod   | price         |
      | zones            | north_america |
      | shippingHandling | false         |
    Then I set ranges for carrier "beer_carrier" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 1000     | 5           |
    When I create carrier "saucisson_carrier" with specified properties:
      | name             | Saucisson carrier |
      | active           | true              |
      | shippingMethod   | price             |
      | zones            | north_america     |
      | shippingHandling | false             |
    Then I set ranges for carrier "saucisson_carrier" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 1000     | 10          |
    When I add product "bottle_of_beer" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And I update product "bottle_of_beer" with following values:
      | price | 100 |
    When I update product "bottle_of_beer" stock with following information:
      | delta_quantity | 51  |
      | location       | dtc |
    And I assign product bottle_of_beer with following carriers:
      | beer_carrier |
    When I add product "saucisson" with following information:
      | name[en-US] | saucisson |
      | type        | standard  |
    And I update product "saucisson" with following values:
      | price | 100 |
    When I update product "saucisson" stock with following information:
      | delta_quantity | 42  |
      | location       | dtc |
    And I assign product saucisson with following carriers:
      | saucisson_carrier |

  Scenario: A fixed amount voucher is applied once across the orders the cart is split into
    Given I create an empty cart "split_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "split_cart"
    And I add 1 products "bottle of beer" to the cart "split_cart"
    And I add 1 products "saucisson" to the cart "split_cart"
    And I use a voucher "PROMO20" for a discount of 20.0 on the cart "split_cart"
    And my cart "split_cart" should have the following details:
      | total_discount | -$20.00 |
    When I add order "split_order" from cart "split_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then cart "split_cart" should have produced 2 orders
    And the orders produced by cart "split_cart" should add up to the cart total
    And the orders produced by cart "split_cart" should have the following total discount:
      | tax_excluded | 20.00 |
      | tax_included | 21.20 |
    And the orders produced by cart "split_cart" should have the following amounts:
      | total_paid_tax_incl | total_paid_tax_excl | total_discounts_tax_incl | total_discounts_tax_excl | cart_rules_tax_incl | cart_rules_tax_excl |
      | 89.80               | 85.00               | 21.20                    | 20.00                    | 21.20               | 20.00               |
      | 116.00              | 110.00              | 0.00                     | 0.00                     | 0.00                | 0.00                |

  Scenario: A fixed amount voucher larger than the first order spills over to the next one
    Given I create an empty cart "spill_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "spill_cart"
    And I add 1 products "bottle of beer" to the cart "spill_cart"
    And I add 1 products "saucisson" to the cart "spill_cart"
    And I use a voucher "PROMO150" for a discount of 150.0 on the cart "spill_cart"
    And my cart "spill_cart" should have the following details:
      | total_discount | -$150.00 |
    When I add order "spill_order" from cart "spill_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then cart "spill_cart" should have produced 2 orders
    And the orders produced by cart "spill_cart" should add up to the cart total
    And the orders produced by cart "spill_cart" should have the following amounts:
      | total_paid_tax_incl | total_paid_tax_excl | total_discounts_tax_incl | total_discounts_tax_excl | cart_rules_tax_incl | cart_rules_tax_excl |
      | 5.00                | 5.00                | 106.00                   | 100.00                   | 106.00              | 100.00              |
      | 63.00               | 60.00               | 53.00                    | 50.00                    | 53.00               | 50.00               |

  Scenario: Two fixed amount vouchers are shared out in the order they apply
    Given I create an empty cart "two_vouchers_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "two_vouchers_cart"
    And I add 1 products "bottle of beer" to the cart "two_vouchers_cart"
    And I add 1 products "saucisson" to the cart "two_vouchers_cart"
    And I use a voucher "PROMO80" for a discount of 80.0 on the cart "two_vouchers_cart"
    And I use a voucher "PROMO70" for a discount of 70.0 on the cart "two_vouchers_cart"
    And my cart "two_vouchers_cart" should have the following details:
      | total_discount | -$150.00 |
    When I add order "two_vouchers_order" from cart "two_vouchers_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then cart "two_vouchers_cart" should have produced 2 orders
    And the orders produced by cart "two_vouchers_cart" should add up to the cart total
    And the orders produced by cart "two_vouchers_cart" should have the following amounts:
      | total_paid_tax_incl | total_paid_tax_excl | total_discounts_tax_incl | total_discounts_tax_excl | cart_rules_tax_incl | cart_rules_tax_excl |
      | 5.00                | 5.00                | 106.00                   | 100.00                   | 106.00              | 100.00              |
      | 63.00               | 60.00               | 53.00                    | 50.00                    | 53.00               | 50.00               |

  Scenario: Only the amount of a voucher that also offers free shipping is shared out
    Given there is a cart rule "amount_and_free_shipping" with following properties:
      | name[en-US]       | Amount and free shipping |
      | discount_amount   | 150                      |
      | free_shipping     | true                     |
      | code              | PROMOSHIP                |
    And I create an empty cart "free_shipping_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "free_shipping_cart"
    And I add 1 products "bottle of beer" to the cart "free_shipping_cart"
    And I add 1 products "saucisson" to the cart "free_shipping_cart"
    And I use a voucher "amount_and_free_shipping" on the cart "free_shipping_cart"
    When I add order "free_shipping_order" from cart "free_shipping_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then cart "free_shipping_cart" should have produced 2 orders
    And the orders produced by cart "free_shipping_cart" should add up to the cart total
    And the orders produced by cart "free_shipping_cart" should have the following amounts:
      | total_paid_tax_incl | total_paid_tax_excl | total_discounts_tax_incl | total_discounts_tax_excl | cart_rules_tax_incl | cart_rules_tax_excl |
      | 0.00                | 0.00                | 111.00                   | 105.00                   | 111.00              | 105.00              |
      | 53.00               | 50.00               | 63.00                    | 60.00                    | 63.00               | 60.00               |

  Scenario: A percentage applied after a shared out amount follows what each order keeps
    Given there is a cart rule "amount_first" with following properties:
      | name[en-US]     | Amount first |
      | discount_amount | 20           |
      | priority        | 1            |
      | code            | AMOUNTFIRST  |
    And there is a cart rule "percent_after" with following properties:
      | name[en-US]         | Percent after |
      | discount_percentage | 10            |
      | priority            | 2             |
      | code                | PERCENTAFTER  |
    And I create an empty cart "mixed_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "mixed_cart"
    And I add 1 products "bottle of beer" to the cart "mixed_cart"
    And I add 1 products "saucisson" to the cart "mixed_cart"
    And I use a voucher "amount_first" on the cart "mixed_cart"
    And I use a voucher "percent_after" on the cart "mixed_cart"
    When I add order "mixed_order" from cart "mixed_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then cart "mixed_cart" should have produced 2 orders
    And the orders produced by cart "mixed_cart" should add up to the cart total
    And the orders produced by cart "mixed_cart" should have the following amounts:
      | total_paid_tax_incl | total_paid_tax_excl | total_discounts_tax_incl | total_discounts_tax_excl | cart_rules_tax_incl | cart_rules_tax_excl |
      | 81.32               | 77.00               | 29.68                    | 28.00                    | 29.68               | 28.00               |
      | 105.40              | 100.00              | 10.60                    | 10.00                    | 10.60               | 10.00               |

  Scenario: A fixed amount voucher is shared out in the currency of the cart
    Given I create an empty cart "eur_cart" for customer "testCustomer"
    And I update the cart "eur_cart" currency to "eur"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "eur_cart"
    And I add 1 products "bottle of beer" to the cart "eur_cart"
    And I add 1 products "saucisson" to the cart "eur_cart"
    And I use a voucher "PROMO150USD" for a discount of 150.0 on the cart "eur_cart"
    When I add order "eur_order" from cart "eur_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then cart "eur_cart" should have produced 2 orders
    And the orders produced by cart "eur_cart" should add up to the cart total
    And the orders produced by cart "eur_cart" should have the following amounts:
      | total_paid_tax_incl | total_paid_tax_excl | total_discounts_tax_incl | total_discounts_tax_excl | cart_rules_tax_incl | cart_rules_tax_excl |
      | 50.00               | 50.00               | 1060.00                  | 1000.00                  | 1060.00             | 1000.00             |
      | 630.00              | 600.00              | 530.00                   | 500.00                   | 530.00              | 500.00              |

  @restore-cart-rules-after-scenario
  @reboot-kernel-before-scenario
  Scenario: An amount per unit of a product selection is not shared out
    Given I enable feature flag "discount"
    And I create an empty cart "segment_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "segment_cart"
    And I add 2 products "bottle of beer" to the cart "segment_cart"
    And I add 1 products "saucisson" to the cart "segment_cart"
    And I create a "product_level" discount "selection_discount" with following properties:
      | name[en-US]                | Ten off each unit         |
      | active                     | true                      |
      | valid_from                 | 2025-01-01 00:00:00       |
      | valid_to                   | 2099-12-31 00:00:00       |
      | code                       | UNIT10                    |
      | reduction_amount           | 10.0                      |
      | reduction_currency         | usd                       |
      | reduction_tax_included     | false                     |
      | productConditionQuantity   | 1                         |
      | productCondition[products] | bottle of beer, saucisson |
    And I use a voucher "selection_discount" on the cart "segment_cart"
    And my cart "segment_cart" should have the following details:
      | total_discount | -$30.00 |
    When I add order "segment_order" from cart "segment_cart" with "dummy_payment" payment method and "Payment accepted" order status
    Then cart "segment_cart" should have produced 2 orders
    And the orders produced by cart "segment_cart" should add up to the cart total
    And the orders produced by cart "segment_cart" should have the following total discount:
      | tax_excluded | 30.00 |
      | tax_included | 31.80 |
    And I disable feature flag "discount"
