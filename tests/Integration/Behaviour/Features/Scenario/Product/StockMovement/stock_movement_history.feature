# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s stock_movement_history --tags product-stock-movement-history
@restore-all-tables-before-feature
@clear-cache-before-feature
@product-stock-movement-history
@stock-movement-history
Feature: Search stock movement history from Back Office (BO)
  As a BO user
  I need to be able to search stock movement history for a product from BO

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And there is a product in the catalog named "product1" with a price of 17.0 and 100 items in stock

  Scenario: I can search the last 5 rows of stock movement history by default and paginate through history
    # First create a cart with 2 product1 and order it
    When I create an empty cart "dummy_cart1" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart1"
    And I add 2 products "product1" to the cart "dummy_cart1"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart1                |
      | message             | order1                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    And the available stock for product "product1" should be 98
    # Then create a second cart with 3 product1, order it without paying (no stock movement the quantity is reserved)
    When I create an empty cart "dummy_cart2" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart2"
    And I add 3 products "product1" to the cart "dummy_cart2"
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart2                |
      | message             | order2                     |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    # Even though the quantity is reserved and no stock movement was generated the available quantity is correctly updated
    And the available stock for product "product1" should be 95
    # Then update the product stock from BO by adding 10 more products
    When I update product "product1" stock with following information:
      | delta_quantity | 10 |
    Then the available stock for product "product1" should be 105
    # Then order 4 product1 (status delivered)
    When I create an empty cart "dummy_cart3" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart3"
    And I add 4 products "product1" to the cart "dummy_cart3"
    And I add order "bo_order3" with the following details:
      | cart                | dummy_cart3                |
      | message             | order3                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    And the available stock for product "product1" should be 101
    # Then order 5 product1 (status delivered)
    When I create an empty cart "dummy_cart4" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart4"
    And I add 5 products "product1" to the cart "dummy_cart4"
    And I add order "bo_order4" with the following details:
      | cart                | dummy_cart4                |
      | message             | order4                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    And the available stock for product "product1" should be 96
    # Now update product quantity of product1 by 5
    When I update product "product1" stock with following information:
      | delta_quantity | 5 |
    Then the available stock for product "product1" should be 101
    # Order 3 product1 (status delivered)
    When I create an empty cart "dummy_cart5" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart5"
    And I add 3 products "product1" to the cart "dummy_cart5"
    And I add order "bo_order5" with the following details:
      | cart                | dummy_cart5                |
      | message             | order5                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    And the available stock for product "product1" should be 98
    # Order 1 product1 (status delivered)
    When I create an empty cart "dummy_cart6" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart6"
    And I add 1 products "product1" to the cart "dummy_cart6"
    And I add order "bo_order6" with the following details:
      | cart                | dummy_cart6                |
      | message             | order6                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    And the available stock for product "product1" should be 97
    # Order 2 product1 (status delivered)
    When I create an empty cart "dummy_cart7" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart7"
    And I add 2 products "product1" to the cart "dummy_cart7"
    And I add order "bo_order7" with the following details:
      | cart                | dummy_cart7                |
      | message             | order7                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    And the available stock for product "product1" should be 95
    # Now we check at the stock movements
    When I search stock movement history of product "product1" I should get following results:
      | type   | first_name | last_name | delta_quantity |
      | range  |            |           | -6             |
      | single | Puff       | Daddy     | 5              |
      | range  |            |           | -9             |
      | single | Puff       | Daddy     | 10             |
      # Since no stock movement is generated until the order is shipped this range only has a quantity of -2,
      # not -5 because second order is still waiting for payment
      | range  |            |           | -2             |
    When I search stock movement history of product "product1" with offset 0 and limit 6 I should get following results:
      | type   | first_name | last_name | delta_quantity |
      | range  |            |           | -6             |
      | single | Puff       | Daddy     | 5              |
      | range  |            |           | -9             |
      | single | Puff       | Daddy     | 10             |
      | range  |            |           | -2             |
      | single | Puff       | Daddy     | 100            |
    When I search stock movement history of product "product1" with offset 1 and limit 5 I should get following results:
      | type   | first_name | last_name | delta_quantity |
      | single | Puff       | Daddy     | 5              |
      | range  |            |           | -9             |
      | single | Puff       | Daddy     | 10             |
      | range  |            |           | -2             |
      | single | Puff       | Daddy     | 100            |
    When I search stock movement history of product "product1" with offset 2 and limit 3 I should get following results:
      | type   | first_name | last_name | delta_quantity |
      | range  |            |           | -9             |
      | single | Puff       | Daddy     | 10             |
      | range  |            |           | -2             |
