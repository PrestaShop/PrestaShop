@restore-all-tables-before-feature
Feature: Cart lines of one product with a specific price rule from a minimum quantity
  As a customer
  I must see on each cart line the reduction its own quantity gets

  Scenario: the line below the minimum quantity comes first
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule2" with a percent discount of 15% and minimum quantity of 2
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given product "product1" has a customization field named "text1"
    When I add 1 items of a new customization of product "product1"
    When I add 3 items of a new customization of product "product1"
    Then the line of product "product1" with quantity 1 in my cart should have a reduction of 0
    Then the line of product "product1" with quantity 3 in my cart should have a reduction of 2.97

  Scenario: the line above the minimum quantity comes first
    Given I have an empty default cart
    Given there is a specific price rule named "priceRule2" with a percent discount of 15% and minimum quantity of 2
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given product "product1" has a customization field named "text1"
    When I add 3 items of a new customization of product "product1"
    When I add 1 items of a new customization of product "product1"
    Then the line of product "product1" with quantity 3 in my cart should have a reduction of 2.97
    Then the line of product "product1" with quantity 1 in my cart should have a reduction of 0
