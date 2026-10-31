# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-round-type-lifecycle
@restore-all-tables-before-feature
@clear-cache-before-feature
@reset-all-tables-before-scenario
@order-round-type-lifecycle
Feature: Refunds and back office edits keep the order's rounding
  As a merchant
  I must refund exactly what was paid and edit an order with the rounding it was placed with

  # The product costs 7.80 tax excluded, 9.438 tax included with a 21% tax.
  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "FR" is enabled
    And I add new tax "odd-tax" with following properties:
      | name       | Odd Tax (21%) |
      | rate       | 21            |
      | is_enabled | true          |
    And I add the tax rule group "odd-tax-group" for the tax "odd-tax" with the following conditions:
      | name    | Odd Tax (21%) |
      | country | FR            |
    And there is a product in the catalog named "Test Product With Odd Tax" with a price of 7.80 and 100 items in stock
    And I set tax rule group "odd-tax-group" to product "Test Product With Odd Tax"
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "FR" country
    And a carrier "price_carrier" with name "My cheap carrier" exists
    And I edit carrier "price_carrier" with specified properties:
      | active | true |
    And I set tax rule "odd-tax-group" for carrier "price_carrier"

  # Each article: 9.44 x 80 = 755.20 paid, not 9.438 x 80 = 755.04
  Scenario: Full standard refund of an each-article order refunds what was paid (tax included customer)
    Given specific shop configuration for "rounding type" is set to round each article
    And price display method for the group of the customer having email "pub@prestashop.com" is "tax included"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 80 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    And order "bo_order1" should have following details:
      | total_products    | 624.00 |
      | total_products_wt | 755.20 |
    And return product is enabled
    When I issue a standard refund on "bo_order1" with credit slip without voucher on following products:
      | product_name              | quantity |
      | Test Product With Odd Tax | 80       |
    Then "bo_order1" has 1 credit slips
    And "bo_order1" last credit slip is:
      | amount                  | 755.20 |
      | total_products_tax_excl | 624.00 |
      | total_products_tax_incl | 755.20 |
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity_refunded | 80     |
      | total_refunded_tax_excl   | 624.00 |
      | total_refunded_tax_incl   | 755.20 |

  Scenario: Full standard refund of an each-article order refunds what was paid (tax excluded customer)
    Given specific shop configuration for "rounding type" is set to round each article
    And price display method for the group of the customer having email "pub@prestashop.com" is "tax excluded"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 80 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    And return product is enabled
    When I issue a standard refund on "bo_order1" with credit slip without voucher on following products:
      | product_name              | quantity |
      | Test Product With Odd Tax | 80       |
    Then "bo_order1" has 1 credit slips
    And "bo_order1" last credit slip is:
      | total_products_tax_excl | 624.00 |
      | total_products_tax_incl | 755.20 |
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | total_refunded_tax_excl | 624.00 |
      | total_refunded_tax_incl | 755.20 |

  # Each line: 9.438 x 3 = 28.314, paid 28.31; three refunds of 9.44 would refund 28.32
  Scenario: Refunding an each-line order unit by unit never refunds more than the line paid
    Given specific shop configuration for "rounding type" is set to round each line
    And price display method for the group of the customer having email "pub@prestashop.com" is "tax included"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 3 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | total_price_tax_incl | 28.31 |
    And return product is enabled
    When I issue a standard refund on "bo_order1" with credit slip without voucher on following products:
      | product_name              | quantity |
      | Test Product With Odd Tax | 1        |
    And return product is enabled
    And I issue a standard refund on "bo_order1" with credit slip without voucher on following products:
      | product_name              | quantity |
      | Test Product With Odd Tax | 1        |
    And return product is enabled
    And I issue a standard refund on "bo_order1" with credit slip without voucher on following products:
      | product_name              | quantity |
      | Test Product With Odd Tax | 1        |
    Then "bo_order1" has 3 credit slips
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity_refunded | 3     |
      | total_refunded_tax_incl   | 28.31 |

  Scenario: Editing the quantity of an each-article order keeps each-article rounding
    Given specific shop configuration for "rounding type" is set to round each article
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 80 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount | 79   |
      | price  | 7.80 |
    Then product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity     | 79     |
      | unit_price_tax_incl  | 9.44   |
      | total_price_tax_excl | 616.20 |
      | total_price_tax_incl | 745.76 |
    And order "bo_order1" should have following details:
      | total_products    | 616.20 |
      | total_products_wt | 745.76 |

  Scenario: Editing an each-article order after the shop switched to each-line keeps the order's rounding
    Given specific shop configuration for "rounding type" is set to round each article
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 80 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And specific shop configuration for "rounding type" is set to round each line
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount | 79   |
      | price  | 7.80 |
    Then product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity     | 79     |
      | unit_price_tax_incl  | 9.44   |
      | total_price_tax_incl | 745.76 |
    And order "bo_order1" should have following details:
      | total_products_wt | 745.76 |

  # Placed with half up rounding (9.438 -> 9.44), refunded after the shop switched to round down (9.43)
  Scenario: Refund uses the rounding mode the order was placed with
    Given specific shop configuration for "rounding type" is set to round each line
    And price display method for the group of the customer having email "pub@prestashop.com" is "tax included"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 1 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | total_price_tax_incl | 9.44 |
    And shop configuration for "PS_PRICE_ROUND_MODE" is set to 1
    And shop configuration for "PS_ORDER_RETURN" is set to 1
    When I issue a standard refund on "bo_order1" with credit slip without voucher on following products:
      | product_name              | quantity |
      | Test Product With Odd Tax | 1        |
    Then product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | total_refunded_tax_incl | 9.44 |

  Scenario: Credit slip of an each-article order refunded after the shop switched to each-line keeps the order's rounding
    Given specific shop configuration for "rounding type" is set to round each article
    And price display method for the group of the customer having email "pub@prestashop.com" is "tax excluded"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 80 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    And specific shop configuration for "rounding type" is set to round each line
    And shop configuration for "PS_ORDER_RETURN" is set to 1
    When I issue a standard refund on "bo_order1" with credit slip without voucher on following products:
      | product_name              | quantity |
      | Test Product With Odd Tax | 80       |
    Then "bo_order1" last credit slip is:
      | total_products_tax_excl | 624.00 |
      | total_products_tax_incl | 755.20 |

  # Each line: 9.438 x 79 = 745.60, each article would give 9.44 x 79 = 745.76
  Scenario: Editing an each-line order after the shop switched to each-article keeps lines and totals consistent
    Given specific shop configuration for "rounding type" is set to round each line
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 80 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And specific shop configuration for "rounding type" is set to round each article
    When I edit product "Test Product With Odd Tax" to order "bo_order1" with following products details:
      | amount | 79   |
      | price  | 7.80 |
    Then product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity     | 79     |
      | total_price_tax_incl | 745.60 |
    And order "bo_order1" should have following details:
      | total_products_wt | 745.60 |

  # Each line: 3 x 9.438 = 28.31 paid tax included; three refunds of 7.80 tax excluded would record 3 x 9.44 = 28.32
  Scenario: Partial refunds by amount never record more than the line paid
    Given specific shop configuration for "rounding type" is set to round each line
    And price display method for the group of the customer having email "pub@prestashop.com" is "tax excluded"
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 3 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | total_price_tax_excl | 23.40 |
      | total_price_tax_incl | 28.31 |
    When I issue a partial refund on "bo_order1" without restock with credit slip without voucher on following products:
      | product_name              | quantity | amount |
      | Test Product With Odd Tax | 1        | 7.80   |
    And I issue a partial refund on "bo_order1" without restock with credit slip without voucher on following products:
      | product_name              | quantity | amount |
      | Test Product With Odd Tax | 1        | 7.80   |
    And I issue a partial refund on "bo_order1" without restock with credit slip without voucher on following products:
      | product_name              | quantity | amount |
      | Test Product With Odd Tax | 1        | 7.80   |
    Then product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | total_refunded_tax_excl | 23.40 |
      | total_refunded_tax_incl | 28.31 |
