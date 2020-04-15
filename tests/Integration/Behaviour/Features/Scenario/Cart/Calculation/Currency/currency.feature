@reset-database-before-feature
Feature: Cart calculation with currencies
  As a customer
  I must be able to have correct cart total when using distinct currencies

  # USD / USD

  Scenario: Empty cart (default USD/ current USD)
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency1" is the current one
    Then I should have 0 different products in my cart
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency1" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 26.812 tax included
    Then my cart total using previous calculation method should be 26.812 tax included

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency1" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 3 items of product "product1" in my cart
    Then my cart total should be 66.44 tax included
    Then my cart total using previous calculation method should be 66.44 tax included

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency1" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 162.4 tax included
    Then my cart total using previous calculation method should be 162.4 tax included

  # USD / CHF

  Scenario: Empty cart (default USD/ current CHF)
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency2" is the current one
    Then I should have 0 different products in my cart
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency2" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 33.515 tax included
    Then my cart total using previous calculation method should be 33.515 tax included

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency2" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 3 items of product "product1" in my cart
    Then my cart total should be 83.1 tax included
    Then my cart total using previous calculation method should be 83.1 tax included

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency2" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 203.0 tax included
    Then my cart total using previous calculation method should be 203.0 tax included

  # CHF / USD

  Scenario: Empty cart (default CHF/ current USD)
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency2" is the default one
    Given currency "currency1" is the current one
    Then I should have 0 different products in my cart
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency2" is the default one
    Given currency "currency1" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 24.66704 tax included
    Then my cart total using previous calculation method should be 24.66704 tax included

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency2" is the default one
    Given currency "currency1" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 3 items of product "product1" in my cart
    Then my cart total should be 61.12 tax included
    Then my cart total using previous calculation method should be 61.12 tax included

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency2" is the default one
    Given currency "currency1" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 149.408 tax included
    Then my cart total using previous calculation method should be 149.408 tax included

  # USD / EUR

  Scenario: Empty cart (default USD/ current EUR)
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency3" is the current one
    Then I should have 0 different products in my cart
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency3" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 16.89156 tax included
    Then my cart total using previous calculation method should be 16.89156 tax included

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency3" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 3 items of product "product1" in my cart
    Then my cart total should be 41.85 tax included
    Then my cart total using previous calculation method should be 41.85 tax included

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency1" is the default one
    Given currency "currency3" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 102.312 tax included
    Then my cart total using previous calculation method should be 102.312 tax included

  # EUR / USD

  Scenario: Empty cart (default EUR/ current USD)
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency3" is the default one
    Given currency "currency1" is the current one
    Then I should have 0 different products in my cart
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency3" is the default one
    Given currency "currency1" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then my cart total should be 24.66704 tax included
    Then my cart total using previous calculation method should be 24.66704 tax included

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency3" is the default one
    Given currency "currency1" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    When I add 3 items of product "product1" in my cart
    Then my cart total should be 61.12 tax included
    Then my cart total using previous calculation method should be 61.12 tax included

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    Given currency "currency3" is the default one
    Given currency "currency1" is the current one
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    When I add 2 items of product "product2" in my cart
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product3" in my cart
    Then my cart total should be 149.408 tax included
    Then my cart total using previous calculation method should be 149.408 tax included
