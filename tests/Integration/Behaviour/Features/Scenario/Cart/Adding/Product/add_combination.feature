@reset-database-before-feature
Feature: Add product combination in cart
  As a customer
  I must be able to correctly add product combinations in my cart

  Scenario: Add combination in cart
    Given I have an empty default cart
    Given there is a product in the catalog named "product7" with a price of 24.324 and 1000 items in stock
    Given product "product7" has a combination named "combi1" with 500 items in stock
    Then the remaining available stock for combination "combi1" of product "product7" should be 500
    When I add 11 items of combination "combi1" of product "product7"
    Then I should have 11 items of combination "combi1" of product "product7" in my cart
    Then the remaining available stock for combination "combi1" of product "product7" should be 489

  Scenario: Cannot add combination in cart with quantity exceeding availability
    Given I have an empty default cart
    Given there is a product in the catalog named "product7" with a price of 19.812 and 1000 items in stock
    Given product "product7" has a combination named "combi1" with 500 items in stock
    Then I am not able to add 600 items of combination "combi1" of product "product7" in my cart
    Then I should have 0 items of combination "combi1" of product "product7" in my cart
    Then the remaining available stock for combination "combi1" of product "product7" should be 500

  Scenario: Be able to add out of stock combination if configuration allows it
    Given order out of stock products is allowed
    Given I have an empty default cart
    Given there is a product in the catalog named "product7" with a price of 19.812 and 1000 items in stock
    Given product "product7" has a combination named "combi1" with 500 items in stock
    When I add 600 items of combination "combi1" of product "product7"
    Then I should have 600 items of combination "combi1" of product "product7" in my cart
    Then the remaining available stock for combination "combi1" of product "product7" should be -100
