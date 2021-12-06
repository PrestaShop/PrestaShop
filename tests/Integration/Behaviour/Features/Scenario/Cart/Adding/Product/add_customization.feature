@restore-all-tables-before-feature
Feature: Add product customization in cart
  As a customer
  I must be able to correctly add product customizations in my cart

  Scenario: Add customization in cart
    Given I have an empty default cart
    Given there is a product in the catalog named "product8" with a price of 26.364 and 30 items in stock
    Given product "product8" has a customization field named "custo1"
    When I add 11 items of customization "custo1" of product "product8"
    Then I should have 11 items of customization "custo1" of product "product8" in my cart
    Then the remaining available stock for customization "custo1" of product "product8" should be 19

  Scenario: Cannot add customization in cart with quantity exceeding availability
    Given I have an empty default cart
    Given there is a product in the catalog named "product8" with a price of 26.364 and 30 items in stock
    Given product "product8" has a customization field named "custo1"
    Then I am not able to add 41 items of customization "custo1" of product "product8" to my cart
    Then I should have 0 items of customization "custo1" of product "product8" in my cart
    Then the remaining available stock for customization "custo1" of product "product8" should be 30

  Scenario: Be able to add out of stock customization if configuration allows it
    Given order out of stock products is allowed
    Given I have an empty default cart
    Given there is a product in the catalog named "product8" with a price of 26.364 and 30 items in stock
    Given product "product8" has a customization field named "custo1"
    When I add 41 items of customization "custo1" of product "product8"
    Then I should have 41 items of customization "custo1" of product "product8" in my cart
    Then the remaining available stock for customization "custo1" of product "product8" should be -11
