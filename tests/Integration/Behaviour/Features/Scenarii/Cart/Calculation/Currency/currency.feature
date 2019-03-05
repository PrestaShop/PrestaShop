@database-feature
Feature: Cart calculation with currencies
  As a customer
  I must be able to have correct cart total when using distinct currencies

  # USD / USD

  Scenario: Empty cart (default USD/ current USD)
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency1 is the current one
    Then Distinct product count in my cart should be 0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency1 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 26.812
    Then Expected total of my cart tax included should be 26.812 with previous calculation method

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency1 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 66.44
    Then Expected total of my cart tax included should be 66.44 with previous calculation method

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency1 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 162.4
    Then Expected total of my cart tax included should be 162.4 with previous calculation method

  # USD / CHF

  Scenario: Empty cart (default USD/ current CHF)
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency2 is the current one
    Then Distinct product count in my cart should be 0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency2 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 33.515
    Then Expected total of my cart tax included should be 33.515 with previous calculation method

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency2 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 83.05
    Then Expected total of my cart tax included should be 83.05 with previous calculation method

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency2 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 203.0
    Then Expected total of my cart tax included should be 203.0 with previous calculation method

  # CHF / USD

  Scenario: Empty cart (default CHF/ current USD)
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency2 is the default one
    Given Currency with name currency1 is the current one
    Then Distinct product count in my cart should be 0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency2 is the default one
    Given Currency with name currency1 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 24.66704
    Then Expected total of my cart tax included should be 24.66704 with previous calculation method

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency2 is the default one
    Given Currency with name currency1 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 61.12
    Then Expected total of my cart tax included should be 61.12 with previous calculation method

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency2 is the default one
    Given Currency with name currency1 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 149.408
    Then Expected total of my cart tax included should be 149.408 with previous calculation method

  # USD / EUR

  Scenario: Empty cart (default USD/ current EUR)
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency3 is the current one
    Then Distinct product count in my cart should be 0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency3 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 16.89156
    Then Expected total of my cart tax included should be 16.89156 with previous calculation method

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency3 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 41.85
    Then Expected total of my cart tax included should be 41.85 with previous calculation method

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency1 is the default one
    Given Currency with name currency3 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 102.312
    Then Expected total of my cart tax included should be 102.312 with previous calculation method

  # EUR / USD

  Scenario: Empty cart (default EUR/ current USD)
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency3 is the default one
    Given Currency with name currency1 is the current one
    Then Distinct product count in my cart should be 0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency3 is the default one
    Given Currency with name currency1 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 1
    Then Expected total of my cart tax included should be 24.66704
    Then Expected total of my cart tax included should be 24.66704 with previous calculation method

  Scenario: one product in cart, quantity 3
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency3 is the default one
    Given Currency with name currency1 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    When I add product named product1 in my cart with quantity 3
    Then Expected total of my cart tax included should be 61.12
    Then Expected total of my cart tax included should be 61.12 with previous calculation method

  Scenario: 3 products in cart, several quantities
    Given I have an empty default cart
    Given There is a currency with name currency1 and iso code USD and change rate of 0.92
    Given There is a currency with name currency2 and iso code CHF and change rate of 1.25
    Given There is a currency with name currency3 and iso code EUR and change rate of 0.63
    Given Currency with name currency3 is the default one
    Given Currency with name currency1 is the current one
    Given there is a product with name product1 and price 19.812 and quantity 1000
    Given there is a product with name product2 and price 32.388 and quantity 1000
    Given there is a product with name product3 and price 31.188 and quantity 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    Then Expected total of my cart tax included should be 149.408
    Then Expected total of my cart tax included should be 149.408 with previous calculation method
