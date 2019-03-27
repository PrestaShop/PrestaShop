# here we need to implement correct fixtures for taxrulegroup/taxrate/address

@reset-database-before-feature
Feature: Cart calculation with tax
  As a customer
  I must be able to have correct cart total when using taxes

  Scenario: empty cart
    Given I have an empty default cart
    When I set the delivery address in my cart to address id 2
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included
    Then my cart total should be 0.0 tax excluded
    Then my cart total using previous calculation method should be 0.0 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included
    Then my cart total should be 0.0 tax excluded
    Then my cart total using previous calculation method should be 0.0 tax excluded

  Scenario: tax #1: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given product "product1" has following tax rule group id: 32
    When I set the delivery address in my cart to address id 2
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 27.60448 tax included
    Then my cart total using previous calculation method should be 27.60448 tax included
    Then my cart total should be 26.812 tax excluded
    Then my cart total using previous calculation method should be 26.812 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 26.812 tax included
    Then my cart total using previous calculation method should be 26.812 tax included
    Then my cart total should be 26.812 tax excluded
    Then my cart total using previous calculation method should be 26.812 tax excluded

  Scenario: tax #2: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product5" with a price of 19.812 and 1000 items in stock
    Given product "product5" has following tax rule group id: 9
    When I set the delivery address in my cart to address id 4
    When I add 1 items of product "product5" in my cart
    Then my cart total should be 28.00072 tax included
    Then my cart total using previous calculation method should be 28.00072 tax included
    Then my cart total should be 26.812 tax excluded
    Then my cart total using previous calculation method should be 26.812 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 26.812 tax included
    Then my cart total using previous calculation method should be 26.812 tax included
    Then my cart total should be 26.812 tax excluded
    Then my cart total using previous calculation method should be 26.812 tax excluded

  Scenario: tax #3: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product5" with a price of 19.812 and 1000 items in stock
    Given product "product5" has following tax rule group id: 9
    When I set the delivery address in my cart to address id 1
    When I add 1 items of product "product5" in my cart
    Then my cart total should be 19.812 tax included
    Then my cart total using previous calculation method should be 19.812 tax included
    Then my cart total should be 19.812 tax excluded
    Then my cart total using previous calculation method should be 19.812 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 19.812 tax included
    Then my cart total using previous calculation method should be 19.812 tax included
    Then my cart total should be 19.812 tax excluded
    Then my cart total using previous calculation method should be 19.812 tax excluded

  Scenario: tax #1: one product in cart, quantity 3
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given product "product1" has following tax rule group id: 32
    When I set the delivery address in my cart to address id 2
    When I add 3 items of product "product1" in my cart
    Then my cart total should be 68.81344 tax included
    Then my cart total using previous calculation method should be 68.81344 tax included
    Then my cart total should be 66.436 tax excluded
    Then my cart total using previous calculation method should be 66.436 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 66.436 tax included
    Then my cart total using previous calculation method should be 66.436 tax included
    Then my cart total should be 66.436 tax excluded
    Then my cart total using previous calculation method should be 66.436 tax excluded

  Scenario: tax #2: one product in cart, quantity 3
    Given I have an empty default cart
    Given there is a product in the catalog named "product5" with a price of 19.812 and 1000 items in stock
    Given product "product5" has following tax rule group id: 9
    When I set the delivery address in my cart to address id 4
    When I add 3 items of product "product5" in my cart
    Then my cart total should be 70.00216 tax included
    Then my cart total using previous calculation method should be 70.00216 tax included
    Then my cart total should be 66.436 tax excluded
    Then my cart total using previous calculation method should be 66.436 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 66.436 tax included
    Then my cart total using previous calculation method should be 66.436 tax included
    Then my cart total should be 66.436 tax excluded
    Then my cart total using previous calculation method should be 66.436 tax excluded

  Scenario: tax #3: one product in cart, quantity 3
    Given I have an empty default cart
    Given there is a product in the catalog named "product5" with a price of 19.812 and 1000 items in stock
    Given product "product5" has following tax rule group id: 9
    When I set the delivery address in my cart to address id 1
    When I add 3 items of product "product5" in my cart
    Then my cart total should be 59.436 tax included
    Then my cart total using previous calculation method should be 59.436 tax included
    Then my cart total should be 59.436 tax excluded
    Then my cart total using previous calculation method should be 59.436 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 59.436 tax included
    Then my cart total using previous calculation method should be 59.436 tax included
    Then my cart total should be 59.436 tax excluded
    Then my cart total using previous calculation method should be 59.436 tax excluded

  Scenario: tax #1: 3 products in cart, several quantities
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given product "product1" has following tax rule group id: 32
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given product "product2" has following tax rule group id: 32
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given product "product3" has following tax rule group id: 32
    When I set the delivery address in my cart to address id 2
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 168.616 tax included
    Then my cart total using previous calculation method should be 168.616 tax included
    Then my cart total should be 162.4 tax excluded
    Then my cart total using previous calculation method should be 162.4 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 162.4 tax included
    Then my cart total using previous calculation method should be 162.4 tax included
    Then my cart total should be 162.4 tax excluded
    Then my cart total using previous calculation method should be 162.4 tax excluded

  Scenario: tax #2: 3 products in cart, several quantities
    Given I have an empty default cart
    Given there is a product in the catalog named "product5" with a price of 19.812 and 1000 items in stock
    Given product "product5" has following tax rule group id: 9
    Given there is a product in the catalog named "product6" with a price of 32.388 and 1000 items in stock
    Given product "product6" has following tax rule group id: 9
    Given there is a product in the catalog named "product7" with a price of 31.188 and 1000 items in stock
    Given product "product7" has following tax rule group id: 9
    When I set the delivery address in my cart to address id 4
    When I add 3 items of product "product5" in my cart
    When I add 2 items of product "product6" in my cart
    When I add 1 items of product "product7" in my cart
    Then my cart total should be 171.724 tax included
    Then my cart total using previous calculation method should be 171.724 tax included
    Then my cart total should be 162.4 tax excluded
    Then my cart total using previous calculation method should be 162.4 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 162.4 tax included
    Then my cart total using previous calculation method should be 162.4 tax included
    Then my cart total should be 162.4 tax excluded
    Then my cart total using previous calculation method should be 162.4 tax excluded

  Scenario: tax #3: 3 products in cart, several quantities
    Given I have an empty default cart
    Given there is a product in the catalog named "product5" with a price of 19.812 and 1000 items in stock
    Given product "product5" has following tax rule group id: 9
    Given there is a product in the catalog named "product6" with a price of 32.388 and 1000 items in stock
    Given product "product6" has following tax rule group id: 9
    Given there is a product in the catalog named "product7" with a price of 31.188 and 1000 items in stock
    Given product "product7" has following tax rule group id: 9
    When I set the delivery address in my cart to address id 1
    When I add 3 items of product "product5" in my cart
    When I add 2 items of product "product6" in my cart
    When I add 1 items of product "product7" in my cart
    Then my cart total should be 155.4 tax included
    Then my cart total using previous calculation method should be 155.4 tax included
    Then my cart total should be 155.4 tax excluded
    Then my cart total using previous calculation method should be 155.4 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 155.4 tax included
    Then my cart total using previous calculation method should be 155.4 tax included
    Then my cart total should be 155.4 tax excluded
    Then my cart total using previous calculation method should be 155.4 tax excluded
