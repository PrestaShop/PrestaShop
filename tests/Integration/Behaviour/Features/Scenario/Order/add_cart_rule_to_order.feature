# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags add-discounts-to-order
@reset-database-before-feature
@reboot-kernel-before-feature
@add-discounts-to-order
Feature: Add discounts to order from Back Office (BO)
  As a BO user
  I need to be able to add discounts to existing orders from the BO

  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And customer "testCustomer" has an empty cart "dummy_cart"
    And cart "dummy_cart" delivery and invoice address country for customer "testCustomer" is "US"
    And cart dummy_cart delivery and invoice address for customer "testCustomer" is in "Florida" state of "US" country
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And tax "Sales-taxes US-FL 6%" is applied to order bo_order1
    And order "bo_order1" should have following details:
      | total_products           | 23.8  |
      | total_products_wt        | 25.23 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 0.0   |
      | total_discounts_tax_incl | 0.0   |
      | total_paid_tax_excl      | 30.8  |
      | total_paid_tax_incl      | 32.65 |
      | total_paid               | 32.65 |
      | total_paid_real          | 0     |
    # Displayed prices are tax excluded because of the customer's group
    And Order "bo_order1" has following prices:
      | products      | $23.80   |
      | discounts     | $0.00    |
      | shipping      | $7.00    |
      | taxes         | $1.85    |
      | total         | $32.65   |

  Scenario: Add amount type discount to order which has no invoices
    Given order "bo_order1" does not have any invoices
    When I add discount to order "bo_order1" with following details:
      | name      | discount fpf |
      | type      | amount       |
      | value     | 5.50         |
    Then order "bo_order1" should have following details:
      | total_products           | 23.8  |
      | total_products_wt        | 25.23 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 5.19  |
      | total_discounts_tax_incl | 5.5   |
      | total_paid_tax_excl      | 25.61 |
      | total_paid_tax_incl      | 27.15 |
      | total_paid               | 27.15 |
      | total_paid_real          | 0     |
    And Order "bo_order1" should have following prices:
      | products      | $23.80 |
      | discounts     | $5.19  |
      | shipping      | $7.00  |
      | taxes         | $1.54  |
      | total         | $27.15 |

  Scenario: Add percent type discount to order which has no invoices
    Given order "bo_order1" does not have any invoices
    When I add discount to order "bo_order1" with following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    Then order "bo_order1" should have following details:
      | total_products           | 23.8  |
      | total_products_wt        | 25.23 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 11.90 |
      | total_discounts_tax_incl | 12.62 |
      | total_paid_tax_excl      | 18.9  |
      | total_paid_tax_incl      | 20.03 |
      | total_paid               | 20.03 |
      | total_paid_real          | 0     |
    Then Order "bo_order1" should have following prices:
      | products      | $23.80 |
      | discounts     | $11.90 |
      | shipping      | $7.00  |
      | taxes         | $1.13  |
      | total         | $20.03 |

  # The discount amount was voluntarily computed on whole total (shipping included 32.648) excluded tax and then applied the tax rate
  # This emphasizes the difference compared to performing rounding on each steps which makes a 0.01 difference
  Scenario: Add amount discount matching fifty percent on whole total
    Given order "bo_order1" does not have any invoices
    When I add discount to order "bo_order1" with following details:
      | name      | discount fifty-fifty |
      | type      | amount               |
      | value     | 16.324               |
    Then order "bo_order1" should have following details:
      | total_products           | 23.8  |
      | total_products_wt        | 25.23 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 15.4  |
      | total_discounts_tax_incl | 16.32 |
      | total_paid_tax_excl      | 15.4  |
      | total_paid_tax_incl      | 16.33 |
      | total_paid               | 16.33 |
      | total_paid_real          | 0     |
    Then Order "bo_order1" should have following prices:
      | products      | $23.80 |
      | discounts     | $15.40 |
      | shipping      | $7.00  |
      | taxes         | $0.93  |
      | total         | $16.33 |

  Scenario: Add amount discount matching fifty percent on products only
    Given order "bo_order1" does not have any invoices
    When I add discount to order "bo_order1" with following details:
      | name      | discount fifty-fifty |
      | type      | amount               |
      | value     | 12.614               |
    Then order "bo_order1" should have following details:
      | total_products           | 23.8  |
      | total_products_wt        | 25.23 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 11.9  |
      | total_discounts_tax_incl | 12.61 |
      | total_paid_tax_excl      | 18.9  |
      | total_paid_tax_incl      | 20.04 |
      | total_paid               | 20.04 |
      | total_paid_real          | 0     |
    Then Order "bo_order1" should have following prices:
      | products      | $23.80 |
      | discounts     | $11.90 |
      | shipping      | $7.00  |
      | taxes         | $1.14  |
      | total         | $20.04 |

  Scenario: Add amount type discount to order and update single invoice
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add discount to order "bo_order1" on last invoice and following details:
      | name      | discount fpf |
      | type      | amount       |
      | value     | 5.50         |
    Then order "bo_order1" should have following details:
      | total_products           | 23.8  |
      | total_products_wt        | 25.23 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 5.19  |
      | total_discounts_tax_incl | 5.5   |
      | total_paid_tax_excl      | 25.61 |
      | total_paid_tax_incl      | 27.15 |
      | total_paid               | 27.15 |
      | total_paid_real          | 0     |
    And Order "bo_order1" should have following prices:
      | products      | $23.80    |
      | discounts     | $5.19     |
      | shipping      | $7.00     |
      | taxes         | $1.54     |
      | total         | $27.15    |
    And the last invoice for order "bo_order1" should have following prices:
      | products                  | 23.80 |
      | discounts tax excluded    | 5.19  |
      | discounts tax included    | 5.50  |
      | shipping tax excluded     | 7.00  |
      | shipping tax included     | 7.42  |
      | total paid tax excluded   | 25.61 |
      | total paid tax included   | 27.15 |

  Scenario: Add percent type discount to order and update single invoice
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add discount to order "bo_order1" on last invoice and following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    Then order "bo_order1" should have following details:
      | total_products           | 23.8  |
      | total_products_wt        | 25.23 |
      | total_shipping           | 7.42  |
      | total_shipping_tax_excl  | 7.0   |
      | total_shipping_tax_incl  | 7.42  |
      | total_discounts_tax_excl | 11.9  |
      | total_discounts_tax_incl | 12.62 |
      | total_paid_tax_excl      | 18.9  |
      | total_paid_tax_incl      | 20.03 |
      | total_paid               | 20.03 |
      | total_paid_real          | 0     |
    Then Order "bo_order1" should have following prices:
      | products      | $23.80 |
      | discounts     | $11.90 |
      | shipping      | $7.00  |
      | taxes         | $1.13  |
      | total         | $20.03 |
    And the last invoice for order "bo_order1" should have following prices:
      | products                  | 23.80 |
      | discounts tax excluded    | 11.90 |
      | discounts tax included    | 12.62 |
      | shipping tax excluded     | 7.00  |
      | shipping tax included     | 7.42  |
      | total paid tax excluded   | 18.90 |
      | total paid tax included   | 20.03 |

  Scenario: Add invalid percent value
    Given order "bo_order1" does not have any invoices
    When I add discount to order "bo_order1" with following details:
      | name      | invalid percent |
      | type      | percent         |
      | value     | -1              |
    Then I should get error that cart rule has invalid min percent
    When I add discount to order "bo_order1" with following details:
      | name      | invalid percent |
      | type      | percent         |
      | value     | 0               |
    Then I should get error that cart rule has invalid min percent
    When I add discount to order "bo_order1" with following details:
      | name      | invalid percent |
      | type      | percent         |
      | value     | 101             |
    Then I should get error that cart rule has invalid max percent

  Scenario: Add invalid amount value
    Given order "bo_order1" does not have any invoices
    When I add discount to order "bo_order1" with following details:
      | name      | invalid amount |
      | type      | amount         |
      | value     | 0              |
    Then I should get error that cart rule has invalid min amount
    When I add discount to order "bo_order1" with following details:
      | name      | invalid amount |
      | type      | amount         |
      | value     | -1             |
    Then I should get error that cart rule has invalid min amount
    When I add discount to order "bo_order1" with following details:
      | name      | invalid amount |
      | type      | amount         |
      | value     | 10000          |
    Then I should get error that cart rule has invalid max amount
