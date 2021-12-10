@restore-all-tables-before-feature
Feature: Cart calculation with only products and gift wrapping
  As a customer
  I must be able to have correct cart total when adding products, and selecting gift wrapping

  Scenario: Empty cart
    Given shop configuration for "PS_GIFT_WRAPPING" is set to 1
    Given shop configuration for "PS_GIFT_WRAPPING_PRICE" is set to 5.3
    Given I have an empty default cart
    When I select gift wrapping
    Then I should have 0 different products in my cart
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1
    Given shop configuration for "PS_GIFT_WRAPPING" is set to 1
    Given shop configuration for "PS_GIFT_WRAPPING_PRICE" is set to 5.3
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    When I select gift wrapping
    Then my cart total should be 32.112 tax included
    Then my cart total using previous calculation method should be 32.112 tax included

  Scenario: one product in cart, quantity 3
    Given shop configuration for "PS_GIFT_WRAPPING" is set to 1
    Given shop configuration for "PS_GIFT_WRAPPING_PRICE" is set to 5.3
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 3 items of product "product1" in my cart
    When I select gift wrapping
    Then my cart total should be 71.736 tax included
    Then my cart total using previous calculation method should be 71.736 tax included

  Scenario: 3 products in cart, several quantities
    Given shop configuration for "PS_GIFT_WRAPPING" is set to 1
    Given shop configuration for "PS_GIFT_WRAPPING_PRICE" is set to 5.3
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    When I select gift wrapping
    Then my cart total should be 167.7 tax included
    Then my cart total using previous calculation method should be 167.7 tax included
