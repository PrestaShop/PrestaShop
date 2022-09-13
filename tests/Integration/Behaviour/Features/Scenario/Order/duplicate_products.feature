# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags duplicate-products-in-order
@restore-all-tables-before-feature
@duplicate-products-in-order
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I have limitation about duplicate product in the order/invoices

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

  Scenario: Add the same product in an order without invoice is forbidden
    Given there is a product in the catalog named "Test Duplicate Product" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Duplicate Product" should be 100
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Test Duplicate Product  |
      | amount        | 30                      |
      | price         | 15                      |
    Then the available stock for product "Test Duplicate Product" should be 70
    And order "bo_order1" should have 32 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"
    And order "bo_order1" should have 0 invoices
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Test Duplicate Product  |
      | amount        | 30                      |
      | price         | 15                      |
    Then I should get error that adding duplicate product is forbidden
    And the available stock for product "Test Duplicate Product" should be 70
    And order "bo_order1" should have 32 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"

  Scenario: Add the same product to an invoice containing it is forbidden
    Given there is a product in the catalog named "Test Duplicate Product" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Duplicate Product" should be 100
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Test Duplicate Product  |
      | amount        | 30                      |
      | price         | 15                      |
    Then the available stock for product "Test Duplicate Product" should be 70
    And order "bo_order1" should have 32 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"
    And order "bo_order1" should have 0 invoices
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoice
    When I add products to order "bo_order1" to the last invoice and the following products details:
      | name          | Test Duplicate Product  |
      | amount        | 30                      |
      | price         | 15                      |
    Then I should get error that adding duplicate product is forbidden
    And the available stock for product "Test Duplicate Product" should be 70
    And order "bo_order1" should have 32 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"

  Scenario: Add product to an invoice not containing it is allowed
    Given there is a product in the catalog named "Test Duplicate Product" with a price of 15.0 and 100 items in stock
    Given there is a product in the catalog named "Test Duplicate Product 2" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Duplicate Product" should be 100
    Then the available stock for product "Test Duplicate Product 2" should be 100
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Test Duplicate Product  |
      | amount        | 30                      |
      | price         | 15                      |
    Then the available stock for product "Test Duplicate Product" should be 70
    Then the available stock for product "Test Duplicate Product 2" should be 100
    And order "bo_order1" should have 32 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"
    And order "bo_order1" should contain 0 products "Test Duplicate Product 2"
    And order "bo_order1" should have 0 invoices
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoice
    When I add products to order "bo_order1" to the last invoice and the following products details:
      | name          | Test Duplicate Product 2 |
      | amount        | 30                       |
      | price         | 15                       |
    Then the available stock for product "Test Duplicate Product 2" should be 70
    And the available stock for product "Test Duplicate Product" should be 70
    And order "bo_order1" should have 1 invoice
    And order "bo_order1" should have 62 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"
    And order "bo_order1" should contain 30 products "Test Duplicate Product 2"

  Scenario: Add product to previous invoice that didn't contain it
    Given there is a product in the catalog named "Test Duplicate Product" with a price of 15.0 and 100 items in stock
    Given there is a product in the catalog named "Test Duplicate Product 2" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Duplicate Product" should be 100
    Then the available stock for product "Test Duplicate Product 2" should be 100
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | Test Duplicate Product  |
      | amount        | 30                      |
      | price         | 15                      |
    Then the available stock for product "Test Duplicate Product" should be 70
    Then the available stock for product "Test Duplicate Product 2" should be 100
    And order "bo_order1" should have 32 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"
    And order "bo_order1" should contain 0 products "Test Duplicate Product 2"
    And order "bo_order1" should have 0 invoices
    Given I update order "bo_order1" status to "Payment accepted"
    And order "bo_order1" should have 1 invoice
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Duplicate Product 2 |
      | amount        | 30                       |
      | price         | 15                       |
    Then the available stock for product "Test Duplicate Product 2" should be 70
    And the available stock for product "Test Duplicate Product" should be 70
    And order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 62 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"
    And order "bo_order1" should contain 30 products "Test Duplicate Product 2"
    # Cannot add to the last invoice since it already contains the product
    When I add products to order "bo_order1" to the last invoice and the following products details:
      | name          | Test Duplicate Product 2 |
      | amount        | 30                       |
      | price         | 15                       |
    Then I should get error that adding duplicate product is forbidden
    And the available stock for product "Test Duplicate Product 2" should be 70
    And the available stock for product "Test Duplicate Product" should be 70
    And order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 62 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"
    And order "bo_order1" should contain 30 products "Test Duplicate Product 2"
    # But can add to the first since it does not contain it
    When I add products to order "bo_order1" to the first invoice and the following products details:
      | name          | Test Duplicate Product 2 |
      | amount        | 30                       |
      | price         | 15                       |
    Then the available stock for product "Test Duplicate Product 2" should be 40
    And the available stock for product "Test Duplicate Product" should be 70
    And order "bo_order1" should have 2 invoices
    And order "bo_order1" should have 92 products in total
    And order "bo_order1" should contain 30 products "Test Duplicate Product"
    And order "bo_order1" should contain 60 products "Test Duplicate Product 2"

    Scenario: two same products with different combination are perfectly allowed
      Given there is a product in the catalog named "My Product" with a price of 10.00 and 200 items in stock
      And product "My Product" has combinations with following details:
        | reference    | quantity | attributes |
        | combination1 | 100      | Size:L     |
        | combination2 | 100      | Size:M     |
      Then the available stock for combination "combination1" of product "My Product" should be 100
      And the available stock for combination "combination2" of product "My Product" should be 100
      When I add products to order "bo_order1" without invoice and the following products details:
        | name          | My Product    |
        | combination   | combination1  |
        | amount        | 30            |
        | price         | 10            |
      Then the available stock for combination "combination1" of product "My Product" should be 70
      And the available stock for combination "combination2" of product "My Product" should be 100
      Given I update order "bo_order1" status to "Payment accepted"
      Then order "bo_order1" should have 1 invoice
      When I add products to order "bo_order1" to the first invoice and the following products details:
        | name          | My Product    |
        | combination   | combination2  |
        | amount        | 30            |
        | price         | 10            |
      Then the available stock for combination "combination1" of product "My Product" should be 70
      Then the available stock for combination "combination2" of product "My Product" should be 70

  Scenario: Add the same product with same combination to an invoice containing it is forbidden
    Given there is a product in the catalog named "My Product" with a price of 10.00 and 200 items in stock
    And product "My Product" has combinations with following details:
      | reference    | quantity | attributes |
      | combination1 | 100      | Size:L     |
      | combination2 | 100      | Size:M     |
      Then the available stock for combination "combination1" of product "My Product" should be 100
    When I add products to order "bo_order1" without invoice and the following products details:
      | name          | My Product    |
      | combination   | combination1  |
      | amount        | 30            |
      | price         | 10            |
      Then the available stock for combination "combination1" of product "My Product" should be 70
    Given I update order "bo_order1" status to "Payment accepted"
    Then order "bo_order1" should have 1 invoice
    When I add products to order "bo_order1" to the first invoice and the following products details:
      | name          | My Product    |
      | combination   | combination1  |
      | amount        | 30            |
      | price         | 10            |
    Then I should get error that adding duplicate product is forbidden
    And the available stock for combination "combination1" of product "My Product" should be 70
    And order "bo_order1" should have 32 products in total
