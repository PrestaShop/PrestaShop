@database-feature
Feature: Cart calculation with carriers
  As a customer
  I must be able to have correct cart total when selecting carriers

  Scenario: Empty cart, carrier 1
    Given I have an empty default cart
    Given There is a zone with name zone1
    Given There is a zone with name zone2
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a country with name country2 and iso code US in zone named zone2
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is a state with name state2 and iso code TEST-2 in country named country2 and zone named zone2
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is an address with name address2 and post code 1 in country named country2 and state named state2
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 has a shipping fees of 3.1 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier1 has a shipping fees of 4.3 in zone with name zone2 for quantities between 0 and 10000
    Given There is a carrier with name carrier2
    Given Carrier with name carrier2 has a shipping fees of 5.7 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier2 has a shipping fees of 6.2 in zone with name zone2 for quantities between 0 and 10000
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier1
    Then Cart shipping fees should be 0.0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1, carrier 1
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a zone with name zone1
    Given There is a zone with name zone2
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a country with name country2 and iso code US in zone named zone2
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is a state with name state2 and iso code TEST-2 in country named country2 and zone named zone2
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is an address with name address2 and post code 1 in country named country2 and state named state2
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 has a shipping fees of 3.1 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier1 has a shipping fees of 4.3 in zone with name zone2 for quantities between 0 and 10000
    Given There is a carrier with name carrier2
    Given Carrier with name carrier2 has a shipping fees of 5.7 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier2 has a shipping fees of 6.2 in zone with name zone2 for quantities between 0 and 10000
    When I add product named product1 in my cart with quantity 1
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier1
    Then Cart shipping fees should be 5.1
    Then Expected total of my cart tax included should be 24.912
    Then Expected total of my cart tax included should be 24.912 with previous calculation method

  Scenario: one product in cart, quantity 3, carrier 1
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a zone with name zone1
    Given There is a zone with name zone2
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a country with name country2 and iso code US in zone named zone2
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is a state with name state2 and iso code TEST-2 in country named country2 and zone named zone2
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is an address with name address2 and post code 1 in country named country2 and state named state2
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 has a shipping fees of 3.1 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier1 has a shipping fees of 4.3 in zone with name zone2 for quantities between 0 and 10000
    Given There is a carrier with name carrier2
    Given Carrier with name carrier2 has a shipping fees of 5.7 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier2 has a shipping fees of 6.2 in zone with name zone2 for quantities between 0 and 10000
    When I add product named product1 in my cart with quantity 3
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier1
    Then Cart shipping fees should be 5.1
    Then Expected total of my cart tax included should be 64.536
    Then Expected total of my cart tax included should be 64.536 with previous calculation method

  Scenario: 3 products in cart, several quantities, carrier 1
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a zone with name zone1
    Given There is a zone with name zone2
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a country with name country2 and iso code US in zone named zone2
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is a state with name state2 and iso code TEST-2 in country named country2 and zone named zone2
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is an address with name address2 and post code 1 in country named country2 and state named state2
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 has a shipping fees of 3.1 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier1 has a shipping fees of 4.3 in zone with name zone2 for quantities between 0 and 10000
    Given There is a carrier with name carrier2
    Given Carrier with name carrier2 has a shipping fees of 5.7 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier2 has a shipping fees of 6.2 in zone with name zone2 for quantities between 0 and 10000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier1
    Then Cart shipping fees should be 5.1
    Then Expected total of my cart tax included should be 160.5
    Then Expected total of my cart tax included should be 160.5 with previous calculation method

  Scenario: Empty cart, carrier 2
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a zone with name zone1
    Given There is a zone with name zone2
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a country with name country2 and iso code US in zone named zone2
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is a state with name state2 and iso code TEST-2 in country named country2 and zone named zone2
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is an address with name address2 and post code 1 in country named country2 and state named state2
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 has a shipping fees of 3.1 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier1 has a shipping fees of 4.3 in zone with name zone2 for quantities between 0 and 10000
    Given There is a carrier with name carrier2
    Given Carrier with name carrier2 has a shipping fees of 5.7 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier2 has a shipping fees of 6.2 in zone with name zone2 for quantities between 0 and 10000
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier2
    Then Cart shipping fees should be 0.0
    Then Expected total of my cart tax included should be 0.0
    Then Expected total of my cart tax included should be 0.0 with previous calculation method

  Scenario: one product in cart, quantity 1, carrier 2
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a zone with name zone1
    Given There is a zone with name zone2
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a country with name country2 and iso code US in zone named zone2
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is a state with name state2 and iso code TEST-2 in country named country2 and zone named zone2
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is an address with name address2 and post code 1 in country named country2 and state named state2
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 has a shipping fees of 3.1 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier1 has a shipping fees of 4.3 in zone with name zone2 for quantities between 0 and 10000
    Given There is a carrier with name carrier2
    Given Carrier with name carrier2 has a shipping fees of 5.7 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier2 has a shipping fees of 6.2 in zone with name zone2 for quantities between 0 and 10000
    When I add product named product1 in my cart with quantity 1
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier2
    Then Cart shipping fees should be 7.7
    Then Expected total of my cart tax included should be 27.512
    Then Expected total of my cart tax included should be 27.512 with previous calculation method

  Scenario: one product in cart, quantity 3, carrier 2
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a zone with name zone1
    Given There is a zone with name zone2
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a country with name country2 and iso code US in zone named zone2
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is a state with name state2 and iso code TEST-2 in country named country2 and zone named zone2
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is an address with name address2 and post code 1 in country named country2 and state named state2
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 has a shipping fees of 3.1 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier1 has a shipping fees of 4.3 in zone with name zone2 for quantities between 0 and 10000
    Given There is a carrier with name carrier2
    Given Carrier with name carrier2 has a shipping fees of 5.7 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier2 has a shipping fees of 6.2 in zone with name zone2 for quantities between 0 and 10000
    When I add product named product1 in my cart with quantity 3
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier2
    Then Cart shipping fees should be 7.7
    Then Expected total of my cart tax included should be 67.136
    Then Expected total of my cart tax included should be 67.136 with previous calculation method

  Scenario: 3 products in cart, several quantities, carrier 2
    Given I have an empty default cart
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a zone with name zone1
    Given There is a zone with name zone2
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a country with name country2 and iso code US in zone named zone2
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is a state with name state2 and iso code TEST-2 in country named country2 and zone named zone2
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is an address with name address2 and post code 1 in country named country2 and state named state2
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 has a shipping fees of 3.1 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier1 has a shipping fees of 4.3 in zone with name zone2 for quantities between 0 and 10000
    Given There is a carrier with name carrier2
    Given Carrier with name carrier2 has a shipping fees of 5.7 in zone with name zone1 for quantities between 0 and 10000
    Given Carrier with name carrier2 has a shipping fees of 6.2 in zone with name zone2 for quantities between 0 and 10000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier2
    Then Cart shipping fees should be 7.7
    Then Expected total of my cart tax included should be 163.1
    Then Expected total of my cart tax included should be 163.1 with previous calculation method
