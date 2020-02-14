# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags add-discounts-to-order
@reset-database-before-feature
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
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And Order "bo_order1" has following prices:
      | products      | $23.80   |
      | discounts     | $0.00    |
      | shipping      | $7.00    |
      | taxes         | $0.00    |
      | total         | $30.80   |

  @add-discounts-to-order
  Scenario: Add amount type discount to order which has no invoices
    Given Order "bo_order1" does not have any invoices
    When I add discount to order "bo_order1" with following details:
      | name      | discount fpf |
      | type      | amount       |
      | value     | 5.50         |
    Then Order "bo_order1" should have following prices:
      | products      | $23.80    |
      | discounts     | $5.50     |
      | shipping      | $7.00     |
      | taxes         | $0.00     |
      | total         | $25.30    |

  @add-discounts-to-order
  Scenario: Add percent type discount to order which has no invoices
    Given Order "bo_order1" does not have any invoices
    When I add discount to order "bo_order1" with following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    Then Order "bo_order1" should have following prices:
      | products      | $23.80    |
      | discounts     | $15.40    |
      | shipping      | $7.00     |
      | taxes         | $0.00     |
      | total         | $15.40    |

  @add-discounts-to-order
  Scenario: Add amount type discount to order and update single invoice
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add discount to order "bo_order1" with selected single invoice and following details:
      | name      | discount fpf |
      | type      | amount       |
      | value     | 5.50         |
    Then Order "bo_order1" should have following prices:
      | products      | $23.80    |
      | discounts     | $5.50     |
      | shipping      | $7.00     |
      | taxes         | $0.00     |
      | total         | $25.30    |
    And invoice for order "bo_order1" should have following prices:
      | products                  | 23.80     |
      | discounts tax excluded    | 5.50      |
      | discounts tax included    | 5.50      |
      | shipping tax excluded     | 7.00      |
      | shipping tax included     | 7.00      |
      | total paid tax excluded   | 25.30     |
      | total paid tax included   | 25.30     |

  @add-discounts-to-order
  Scenario: Add percent type discount to order and update single invoice
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add discount to order "bo_order1" with following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    Then Order "bo_order1" should have following prices:
      | products      | $23.80    |
      | discounts     | $15.40    |
      | shipping      | $7.00     |
      | taxes         | $0.00     |
      | total         | $15.40    |
    And invoice for order "bo_order1" should have following prices:
      | products                  | 23.80     |
      | discounts tax excluded    | 15.40     |
      | discounts tax included    | 15.40     |
      | shipping tax excluded     | 7.00      |
      | shipping tax included     | 7.00      |
      | total paid tax excluded   | 15.40     |
      | total paid tax included   | 15.40     |

  @add-discounts-to-order
  Scenario: Add percent type discount to order and update all invoices
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 11                      |
      | free_shipping | true                    |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      # discount of $7 appears due to single product free shipping (the shipping price is $7)
      # this discount does not reflect in invoice discounts.
      | discounts     | $7.00     |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $41.80    |
    When I add discount to order "bo_order1" with following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    And all invoices for order "bo_order1" should have following discounts:
      | discounts tax excluded    | 20.90     |
      | discounts tax included    | 20.90     |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $27.90    |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $20.90    |

  @add-discounts-to-order
  Scenario: Add amount type discount to order and update all invoices
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 11                      |
      | free_shipping | true                    |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $7.00     |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $41.80    |
    When I add discount to order "bo_order1" with following details:
      | name      | discount five |
      | type      | amount        |
      | value     | 5             |
    And all invoices for order "bo_order1" should have following discounts:
      | discounts tax excluded    | 5.00     |
      | discounts tax included    | 5.00     |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $12.00    |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $36.80    |

  @add-discounts-to-order
  Scenario: Add amount type discount twice to order and update all invoices
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 11                      |
      | free_shipping | true                    |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $7.00     |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $41.80    |
    When I add discount to order "bo_order1" with following details:
      | name      | discount five |
      | type      | amount        |
      | value     | 5             |
    And all invoices for order "bo_order1" should have following discounts:
      | discounts tax excluded    | 5.00     |
      | discounts tax included    | 5.00     |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $12.00    |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $36.80    |
    When I add discount to order "bo_order1" with following details:
      | name      | discount five |
      | type      | amount        |
      | value     | 5             |
    And all invoices for order "bo_order1" should have following discounts:
      | discounts tax excluded    | 10.00     |
      | discounts tax included    | 10.00     |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $17.00    |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $31.80    |

  @add-discounts-to-order
  Scenario: Add percent type discount to order twice and update all invoices
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 11                      |
      | free_shipping | true                    |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $7.00     |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $41.80    |
    When I add discount to order "bo_order1" with following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    And all invoices for order "bo_order1" should have following discounts:
      | discounts tax excluded    | 20.90     |
      | discounts tax included    | 20.90     |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $27.90    |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $20.90    |
    When I add discount to order "bo_order1" with following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    And all invoices for order "bo_order1" should have following discounts:
      | discounts tax excluded    | 31.35     |
      | discounts tax included    | 31.35     |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $38.35    |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $10.45    |

  @add-discounts-to-order
  Scenario: Add amount type discount and update all invoices to order in which products were added twice
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 11                      |
      | free_shipping | true                    |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $7.00     |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $41.80    |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 11                      |
      | free_shipping | true                    |
    Then Order "bo_order1" should have following prices:
      | products      | $45.80    |
      | discounts     | $14.00    |
      | shipping      | $21.00    |
      | taxes         | $0.00     |
      | total         | $52.80    |
    When I add discount to order "bo_order1" with following details:
      | name      | discount five |
      | type      | amount        |
      | value     | 5             |
    And all invoices for order "bo_order1" should have following discounts:
      | discounts tax excluded    | 5.00     |
      | discounts tax included    | 5.00     |
    Then Order "bo_order1" should have following prices:
      | products      | $45.80    |
      | discounts     | $19.00    |
      | shipping      | $21.00    |
      | taxes         | $0.00     |
      | total         | $47.80    |

  @add-discounts-to-order
  Scenario: Add percent type discount and update all invoices to order in which products were added twice
    When I generate invoice for "bo_order1" order
    Then order "bo_order1" should have invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 11                      |
      | free_shipping | true                    |
    Then Order "bo_order1" should have following prices:
      | products      | $34.80    |
      | discounts     | $7.00     |
      | shipping      | $14.00    |
      | taxes         | $0.00     |
      | total         | $41.80    |
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Mug Today is a good day |
      | amount        | 1                       |
      | price         | 11                      |
      | free_shipping | true                    |
    Then Order "bo_order1" should have following prices:
      | products      | $45.80    |
      | discounts     | $14.00    |
      | shipping      | $21.00    |
      | taxes         | $0.00     |
      | total         | $52.80    |
    When I add discount to order "bo_order1" with following details:
      | name      | discount fifty-fifty |
      | type      | percent              |
      | value     | 50                   |
    And all invoices for order "bo_order1" should have following discounts:
      | discounts tax excluded    | 26.40     |
      | discounts tax included    | 26.40     |
    Then Order "bo_order1" should have following prices:
      | products      | $45.80    |
      | discounts     | $40.40    |
      | shipping      | $21.00    |
      | taxes         | $0.00     |
      | total         | $26.40    |
