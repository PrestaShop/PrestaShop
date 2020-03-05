# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags delete-product-from-order
@reset-database-before-feature
Feature: Delete products from order in Back Office (BO)
  As a BO user
  I need to be able to delete products from the existing orders in BO

  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And customer "testCustomer" has an empty cart "dummy_cart"
    And cart "dummy_cart" delivery and invoice address country for customer "testCustomer" is "US"
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add 1 products "Brown bear cushion" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And order "bo_order1" should contain 2 products "Mug The best is yet to come"
    And order "bo_order1" should contain 1 products "Brown bear cushion"
    And Order "bo_order1" has following prices:
      | products      | $42.70   |
      | discounts     | $0.00    |
      | shipping      | $7.00    |
      | taxes         | $0.00    |
      | total         | $49.70   |

  @delete-product-from-order
  Scenario: I should not be able to delete product from delivered order
    When I update order "bo_order1" status to "Delivered"
    When I delete product "Mug The best is yet to come" from order "bo_order1"
    Then I should get error message "Delivered order cannot be modified."

  @delete-product-from-order
  Scenario: Delete product from order without invoice when shipping recalculation config is enabled
    Given Order "bo_order1" does not have any invoices
    And shipping recalculation config is enabled
    When I delete product "Mug The best is yet to come" from order "bo_order1"
    Then order "bo_order1" should contain 0 products "Mug The best is yet to come"
    And Order "bo_order1" should have following prices:
      | products      | $18.90  |
      | discounts     | $0.00   |
      | shipping      | $7.00   |
      | taxes         | $0.00   |
      | total         | $25.90  |

  @delete-product-from-order
  Scenario: Delete multiple products from order without invoice when shipping recalculation config is enabled
    Given Order "bo_order1" does not have any invoices
    And shipping recalculation config is enabled
    When I delete product "Mug The best is yet to come" from order "bo_order1"
    When I delete product "Brown bear cushion" from order "bo_order1"
    Then order "bo_order1" should contain 0 products "Mug The best is yet to come"
    And order "bo_order1" should contain 0 products "Brown bear cushion"
    And Order "bo_order1" should have following prices:
      | products      | $0.00  |
      | discounts     | $0.00  |
      | shipping      | $0.00  |
      | taxes         | $0.00  |
      | total         | $0.00  |

  @delete-product-from-order
  Scenario: Delete product from order with invoice when shipping recalculation config is enabled
    Given shipping recalculation config is enabled
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I delete product "Mug The best is yet to come" from order "bo_order1"
    Then order "bo_order1" should contain 0 products "Mug The best is yet to come"
    And Order "bo_order1" should have following prices:
      | products      | $18.90  |
      | discounts     | $0.00   |
      | shipping      | $7.00   |
      | taxes         | $0.00   |
      | total         | $25.90  |
    And invoice for order "bo_order1" should have following prices:
      | products                  | 18.90     |
      | discounts tax excluded    | 0.00      |
      | discounts tax included    | 0.00      |
      | shipping tax excluded     | 7.00      |
      | shipping tax included     | 7.00      |
      | total paid tax excluded   | 25.90     |
      | total paid tax included   | 25.90     |

  @delete-product-from-order
  Scenario: Delete all products from order with invoice when shipping recalculation config is enabled
    Given shipping recalculation config is enabled
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I delete product "Mug The best is yet to come" from order "bo_order1"
    When I delete product "Brown bear cushion" from order "bo_order1"
    Then order "bo_order1" should contain 0 products "Mug The best is yet to come"
    Then order "bo_order1" should contain 0 products "Brown bear cushion"
    And Order "bo_order1" should have following prices:
      | products      | $0.00  |
      | discounts     | $0.00  |
      | shipping      | $0.00  |
      | taxes         | $0.00  |
      | total         | $0.00  |
    And invoice for order "bo_order1" should have following prices:
      | products                  | 0.00     |
      | discounts tax excluded    | 0.00     |
      | discounts tax included    | 0.00     |
      | shipping tax excluded     | 0.00     |
      | shipping tax included     | 0.00     |
      | total paid tax excluded   | 0.00     |
      | total paid tax included   | 0.00     |

  @delete-product-from-order
  Scenario: Delete all products from order with invoice when shipping recalculation config is disabled.
    When I disable shipping recalculation config
    Then shipping recalculation config is disabled
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I delete product "Mug The best is yet to come" from order "bo_order1"
    When I delete product "Brown bear cushion" from order "bo_order1"
    Then order "bo_order1" should contain 0 products "Mug The best is yet to come"
    Then order "bo_order1" should contain 0 products "Brown bear cushion"
    And Order "bo_order1" should have following prices:
      | products      | $0.00  |
      | discounts     | $0.00  |
      | shipping      | $7.00  |
      | taxes         | $0.00  |
      | total         | $7.00  |
    And invoice for order "bo_order1" should have following prices:
      | products                  | 0.00     |
      | discounts tax excluded    | 0.00     |
      | discounts tax included    | 0.00     |
      | shipping tax excluded     | 7.00     |
      | shipping tax included     | 7.00     |
      | total paid tax excluded   | 7.00     |
      | total paid tax included   | 7.00     |
