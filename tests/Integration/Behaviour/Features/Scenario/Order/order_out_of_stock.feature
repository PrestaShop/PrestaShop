# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-out-of-stock
@reset-database-before-feature
@reset-product-price-cache
@order-out-of-stock
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

  Scenario: Update product in order with the exact amount of stock
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Product Max Stock" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 80                      |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 20
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 products "Test Product Max Stock"
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 110                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    And the available stock for product "Test Product Max Stock" should be 20
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 products "Test Product Max Stock"
    # I can decrease the number in stock (note: 80 + 30 > 100 to check the available quantity considers the amount in the order)
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 30                     |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 70
    And order "bo_order1" should have 32 products in total
    And order "bo_order1" should contain 30 products "Test Product Max Stock"
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 100                     |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 0
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 products "Test Product Max Stock"
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 99                      |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 1
    And order "bo_order1" should have 101 products in total
    And order "bo_order1" should contain 99 products "Test Product Max Stock"

  Scenario: Update combination in order with the exact amount of stock
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    And product "Test Product Max Stock" has combinations with following details:
      | reference | quantity | attributes         |
      | whiteM    | 150      | Size:M;Color:White |
      | whiteL    | 150      | Size:L;Color:White |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 150
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 300
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | combination   | whiteM                  |
      | amount        | 100                     |
      | price         | 15                      |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 50
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 200
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 combinations "whiteM" of product "Test Product Max Stock"
    When I edit combination "whiteM" of product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 160                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 50
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 200
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 combinations "whiteM" of product "Test Product Max Stock"
    # I can decrease the number in stock (note: 100 + 60 > 150 to check the available quantity considers the amount in the order)
    When I edit combination "whiteM" of product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 60                     |
      | price         | 15                     |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 90
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 240
    And order "bo_order1" should have 62 products in total
    And order "bo_order1" should contain 60 combinations "whiteM" of product "Test Product Max Stock"
    When I edit combination "whiteM" of product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 150                     |
      | price         | 15                      |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 0
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 150
    And order "bo_order1" should have 152 products in total
    And order "bo_order1" should contain 150 combinations "whiteM" of product "Test Product Max Stock"
    When I edit combination "whiteM" of product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 149                     |
      | price         | 15                      |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 1
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 151
    And order "bo_order1" should have 151 products in total
    And order "bo_order1" should contain 149 combinations "whiteM" of product "Test Product Max Stock"

  Scenario: Add product in order with the exact amount of stock (first add)
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Product Max Stock" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 101                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    Then the available stock for product "Test Product Max Stock" should be 100
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should contain 0 products "Test Product Max Stock"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 100                     |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 0
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 products "Test Product Max Stock"
    # Change status to paid so an invoice is created, thus allowing to add the product again on new invoice
    Given I update order "bo_order1" status to "Payment accepted"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 1                       |
      | price         | 15                      |
    Then I should get error that product is out of stock
    And the available stock for product "Test Product Max Stock" should be 0
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 products "Test Product Max Stock"

  Scenario: Add product in order with the exact amount of stock (second addition in new invoice)
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Product Max Stock" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 80                      |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 20
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 products "Test Product Max Stock"
    # Change status to paid so an invoice is created, thus allowing to add the product again on new invoice
    Given I update order "bo_order1" status to "Payment accepted"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 20                      |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 0
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 products "Test Product Max Stock"

  Scenario: Add combination in order with the exact amount of stock (first add)
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    And product "Test Product Max Stock" has combinations with following details:
      | reference | quantity | attributes         |
      | whiteM    | 150      | Size:M;Color:White |
      | whiteL    | 150      | Size:L;Color:White |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 150
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 300
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | combination   | whiteM                  |
      | amount        | 151                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 150
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 300
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should contain 0 combinations "whiteM" of product "Test Product Max Stock"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | combination   | whiteM                  |
      | amount        | 150                     |
      | price         | 15                      |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 0
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 150
    And order "bo_order1" should have 152 products in total
    And order "bo_order1" should contain 150 combinations "whiteM" of product "Test Product Max Stock"
    # Change status to paid so an invoice is created, thus allowing to add the product again on new invoice
    Given I update order "bo_order1" status to "Payment accepted"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | combination   | whiteM                  |
      | amount        | 1                       |
      | price         | 15                      |
    Then I should get error that product is out of stock
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 0
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 150
    And order "bo_order1" should have 152 products in total
    And order "bo_order1" should contain 150 combinations "whiteM" of product "Test Product Max Stock"

  Scenario: Add combination in order with the exact amount of stock (second addition in new invoice)
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    And product "Test Product Max Stock" has combinations with following details:
      | reference | quantity | attributes         |
      | whiteM    | 100      | Size:M;Color:White |
      | whiteL    | 100      | Size:L;Color:White |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 100
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 100
    And the available stock for product "Test Product Max Stock" should be 200
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | combination   | whiteM                  |
      | amount        | 80                      |
      | price         | 15                      |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 20
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 100
    And the available stock for product "Test Product Max Stock" should be 120
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 combinations "whiteM" of product "Test Product Max Stock"
    # Change status to paid so an invoice is created, thus allowing to add the product again on new invoice
    Given I update order "bo_order1" status to "Payment accepted"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | combination   | whiteM                  |
      | amount        | 20                      |
      | price         | 15                      |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 0
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 100
    And the available stock for product "Test Product Max Stock" should be 100
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 combinations "whiteM" of product "Test Product Max Stock"

  Scenario: Add product two times and empty stock but edit the first one after
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Product Max Stock" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 1                       |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 99
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should contain 1 products "Test Product Max Stock"
    # Change status to paid so an invoice is created, thus allowing to add the product again on new invoice
    Given I update order "bo_order1" status to "Payment accepted"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 99                      |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 0
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 products "Test Product Max Stock"
    # We can't target a specific OrderDetail but the first one is used so it matches our use case
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 100                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    Then the available stock for product "Test Product Max Stock" should be 0
    And order "bo_order1" should have 102 products in total
    And order "bo_order1" should contain 100 products "Test Product Max Stock"

  Scenario: Add combination two times and empty stock but edit the first one after
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    And product "Test Product Max Stock" has combinations with following details:
      | reference | quantity | attributes         |
      | whiteM    | 150      | Size:M;Color:White |
      | whiteL    | 150      | Size:L;Color:White |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 150
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 300
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | combination   | whiteM                  |
      | amount        | 1                       |
      | price         | 15                      |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 149
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 299
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should contain 1 combinations "whiteM" of product "Test Product Max Stock"
    # Change status to paid so an invoice is created, thus allowing to add the product again on new invoice
    Given I update order "bo_order1" status to "Payment accepted"
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | combination   | whiteM                  |
      | amount        | 149                     |
      | price         | 15                      |
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 0
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 150
    And order "bo_order1" should have 152 products in total
    And order "bo_order1" should contain 150 combinations "whiteM" of product "Test Product Max Stock"
    When I edit combination "whiteM" of product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 150                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    Then the available stock for combination "whiteM" of product "Test Product Max Stock" should be 0
    And the available stock for combination "whiteL" of product "Test Product Max Stock" should be 150
    And the available stock for product "Test Product Max Stock" should be 150
    And order "bo_order1" should have 152 products in total
    And order "bo_order1" should contain 150 combinations "whiteM" of product "Test Product Max Stock"

  Scenario: Update product that is allowed out of stock in order
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Product Max Stock" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 80                      |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 20
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 products "Test Product Max Stock"
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 110                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    And the available stock for product "Test Product Max Stock" should be 20
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 products "Test Product Max Stock"
    Given product "Test Product Max Stock" can be ordered out of stock
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 110                     |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be -10
    And order "bo_order1" should have 112 products in total
    And order "bo_order1" should contain 110 products "Test Product Max Stock"

  Scenario: Add product that is allowed out of stock in order
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Product Max Stock" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 110                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    Then the available stock for product "Test Product Max Stock" should be 100
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should contain 0 products "Test Product Max Stock"
    Given product "Test Product Max Stock" can be ordered out of stock
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 110                     |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be -10
    And order "bo_order1" should have 112 products in total
    And order "bo_order1" should contain 110 products "Test Product Max Stock"

  Scenario: Update product in order when shop configuration allows out of stock
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Product Max Stock" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 80                      |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be 20
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 products "Test Product Max Stock"
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 110                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    And the available stock for product "Test Product Max Stock" should be 20
    And order "bo_order1" should have 82 products in total
    And order "bo_order1" should contain 80 products "Test Product Max Stock"
    Given order out of stock products is allowed
    When I edit product "Test Product Max Stock" to order "bo_order1" with following products details:
      | amount        | 110                     |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be -10
    And order "bo_order1" should have 112 products in total
    And order "bo_order1" should contain 110 products "Test Product Max Stock"

  Scenario: Add product in order when shop configuration allows out of stock
    Given there is a product in the catalog named "Test Product Max Stock" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Product Max Stock" should be 100
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 110                     |
      | price         | 15                      |
    Then I should get error that product is out of stock
    Then the available stock for product "Test Product Max Stock" should be 100
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should contain 0 products "Test Product Max Stock"
    Given order out of stock products is allowed
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Product Max Stock  |
      | amount        | 110                     |
      | price         | 15                      |
    Then the available stock for product "Test Product Max Stock" should be -10
    And order "bo_order1" should have 112 products in total
    And order "bo_order1" should contain 110 products "Test Product Max Stock"
