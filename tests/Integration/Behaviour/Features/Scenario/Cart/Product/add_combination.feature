@restore-all-tables-before-feature
Feature: Add product combination in cart
  As a customer
  I must be able to correctly add product combinations in my cart

  @add-combinations-to-cart
  Scenario: Add combination in cart
    Given I have an empty default cart
    Given there is a product in the catalog named "product7" with a price of 24.324 and 1000 items in stock
    Given product "product7" has a combination named "combi1" with 500 items in stock
    Then the remaining available stock for combination "combi1" of product "product7" should be 500
    When I add 11 items of combination "combi1" of product "product7"
    Then I should have 11 items of combination "combi1" of product "product7" in my cart
    Then the remaining available stock for combination "combi1" of product "product7" should be 489

  @add-combinations-to-cart
  Scenario: Cannot add combination in cart with quantity exceeding availability
    Given I have an empty default cart
    Given there is a product in the catalog named "product7" with a price of 19.812 and 1000 items in stock
    Given product "product7" has a combination named "combi1" with 500 items in stock
    Then I am not able to add 600 items of combination "combi1" of product "product7" in my cart
    Then I should have 0 items of combination "combi1" of product "product7" in my cart
    Then the remaining available stock for combination "combi1" of product "product7" should be 500

  @add-combinations-to-cart
  Scenario: Cannot add product with combination in cart with minimal quantity enabled
    Given there is customer "testCustomer" with email "pub@prestashop.com"
    And  I create an empty cart "dummy_cart" for customer "testCustomer"
    And there is a product in the catalog named "product7" with a price of 19.812 and 1000 items in stock
    And product "product7" has a combination named "combi1" with 500 items in stock
    And the combination "combi1" of the product "product7" has a minimal quantity of 10
    When I add 5 items of combination "combi1" of the product "product7" to the cart "dummy_cart"
    Then I should get error that minimum quantity of 10 must be added to cart
    Then I should have 0 items of combination "combi1" of product "product7" in my cart
    Then the remaining available stock for combination "combi1" of product "product7" should be 500

  @add-combinations-to-cart
  Scenario: Be able to add out of stock combination if configuration allows it
    Given order out of stock products is allowed
    Given I have an empty default cart
    Given there is a product in the catalog named "product7" with a price of 19.812 and 1000 items in stock
    Given product "product7" has a combination named "combi1" with 500 items in stock
    When I add 600 items of combination "combi1" of product "product7"
    Then I should have 600 items of combination "combi1" of product "product7" in my cart
    Then the remaining available stock for combination "combi1" of product "product7" should be -100

  @add-combinations-to-cart
  Scenario: Product combinations are taken into account by price rules based on quantity
    Given there is a specific price rule named "priceRuleCombination" with an amount discount of 5 and minimum quantity of 3
    Given I have an empty default cart
    Given there is a product in the catalog named "product7" with a price of 24.324 and 1000 items in stock
    Given product "product7" has a combination named "combi1" with 100 items in stock
    Given product "product7" has a combination named "combi2" with 100 items in stock
    When I add 1 items of combination "combi1" of product "product7"
    When I add 1 items of combination "combi2" of product "product7"
    Then I should have 1 items of combination "combi1" of product "product7" in my cart
    Then I should have 1 items of combination "combi2" of product "product7" in my cart
    Then The price of each product "product7" after reduction should be 24.324
    When I add 1 items of combination "combi2" of product "product7"
    Then I should have 2 items of combination "combi2" of product "product7" in my cart
    Then The price of each product "product7" after reduction should be 19.324
