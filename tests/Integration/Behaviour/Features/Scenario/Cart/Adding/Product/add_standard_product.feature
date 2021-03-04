# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags add-standard-product

@reset-database-before-feature
@add-standard-product
Feature: Add product in cart
  As a customer
  I must be able to correctly add products in my cart

  Scenario: Add product(s) in cart
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Then the remaining available stock for product "product1" should be 1000
    When I add 11 items of product "product1" in my cart
    Then my cart should contain 11 units of product "product1", excluding items in pack
    Then the remaining available stock for product "product1" should be 989

  Scenario: Cannot add product in cart with quantity exceeding availability
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Then I am not allowed to add 1100 items of product "product1" in my cart
    Then my cart should contain 0 units of product "product1", excluding items in pack
    Then the remaining available stock for product "product1" should be 1000

  Scenario: Cannot add product in cart with minimal quantity enabled
    Given there is customer "testCustomer" with email "pub@prestashop.com"
    And  I create an empty cart "dummy_cart" for customer "testCustomer"
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And the product "product1" minimal quantity is 10
    When I add 5 products "product1" to the cart "dummy_cart"
    Then I should get error that minimum quantity of 10 must be added to cart
    Then my cart should contain 0 units of product "product1", excluding items in pack
    Then the remaining available stock for product "product1" should be 1000

  Scenario: Be able to add out of stock product if configuration allows it
    Given order out of stock products is allowed
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1100 items of product "product1" in my cart
    Then my cart should contain 1100 units of product "product1", excluding items in pack
    Then the remaining available stock for product "product1" should be -100

  Scenario: Change product quantity
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I change quantity of product "product1" in my cart with quantity 1 and operator up, result of change is OK
    Then my cart should contain 1 units of product "product1", excluding items in pack
    When I change quantity of product "product1" in my cart with quantity 1 and operator up, result of change is OK
    Then my cart should contain 2 units of product "product1", excluding items in pack
    When I change quantity of product "product1" in my cart with quantity 2 and operator up, result of change is OK
    Then my cart should contain 4 units of product "product1", excluding items in pack
    When I change quantity of product "product1" in my cart with quantity 2 and operator nothing, result of change is KO
    Then my cart should contain 4 units of product "product1", excluding items in pack
    When I change quantity of product "product1" in my cart with quantity 2 and operator down, result of change is OK
    Then my cart should contain 2 units of product "product1", excluding items in pack
    When I change quantity of product "product1" in my cart with quantity 1 and operator down, result of change is OK
    Then my cart should contain 1 units of product "product1", excluding items in pack
    When I change quantity of product "product1" in my cart with quantity 1 and operator down, result of change is OK
    Then my cart should contain 0 units of product "product1", excluding items in pack
    When I change quantity of product "product1" in my cart with quantity 1 and operator down, result of change is OK
    Then my cart should contain 0 units of product "product1", excluding items in pack
    When I change quantity of product "product1" in my cart with quantity 1 and operator nothing, result of change is OK
    Then my cart should contain 0 units of product "product1", excluding items in pack
