# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-prices
@reset-database-before-feature
@update-prices
Feature: Update product price fields from Back Office (BO).
  As a BO user I want to be able to update product fields associated with price.

  Background:
    Given I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product product1 should have following prices information:
      | price              | 0     |
      | price_tax_included | 0     |
      | ecotax             | 0     |
      | tax rules group    |       |
      | on_sale            | false |
      | wholesale_price    | 0     |
      | unit_price         | 0     |
      | unity              |       |
      | unit_price_ratio   | 0     |

  Scenario: I update product prices
    And tax rules group named "US-AL Rate (4%)" exists
    When I update product "product1" prices with following information:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 900             |
      | unity              | bag of ten      |
    Then product product1 should have following prices information:
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
#      #todo: rounding issue. #19620
#      #| unit_price         | 900         |
      | unity              | bag of ten      |
      | unit_price_ratio   | 0.112211        |

  Scenario: I partially update product prices, providing only those values which I want to update
    Given I update product "product1" prices with following information:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 900             |
      | unity              | bag of ten      |
    Given product product1 should have following prices information:
      | price              | 100.99          |
      | price_tax_included | 105.0296        |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
#      #todo: rounding issue. #19620
#      #| unit_price         | 900         |
      | unity              | bag of ten      |
      | unit_price_ratio   | 0.112211        |
    When I update product "product1" prices with following information:
      | price | 200 |
    Then product product1 should have following prices information:
      | price              | 200             |
      | price_tax_included | 208             |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
#        #todo: rounding issue. #19620
#        #| unit_price         | 900         |
      | unity              | bag of ten      |
      | unit_price_ratio   | 0.222222        |
    When I update product "product1" prices with following information:
      | ecotax  | 5.5   |
      | on_sale | false |
    Then product product1 should have following prices information:
      | price              | 200             |
      | price_tax_included | 208             |
      | ecotax             | 5.5             |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | false           |
      | wholesale_price    | 70              |
#        #todo: rounding issue. #19620
#        #| unit_price         | 900         |
      | unity              | bag of ten      |
      | unit_price_ratio   | 0.222222        |

  Scenario: I update product prices with negative values
    Given I update product "product1" prices with following information:
      | price           | 50              |
      | ecotax          | 3               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 10              |
      | unit_price      | 500             |
      | unity           | bag of ten      |
    And product product1 should have following prices information:
      | price              | 50              |
      | price_tax_included | 52              |
      | ecotax             | 3               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 10              |
      | unit_price         | 500             |
      | unity              | bag of ten      |
      | unit_price_ratio   | 0.1             |
    When I update product "product1" prices with following information:
      | price | -20 |
    Then I should get error that product "price" is invalid
    When I update product "product1" prices with following information:
      | ecotax | -2 |
    Then I should get error that product "ecotax" is invalid
    When I update product "product1" prices with following information:
      | wholesale_price | -35 |
    Then I should get error that product "wholesale_price" is invalid
    When I update product "product1" prices with following information:
      | unit_price | -300 |
    Then I should get error that product "unit_price" is invalid
    And product product1 should have following prices information:
      | price              | 50              |
      | price_tax_included | 52              |
      | ecotax             | 3               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 10              |
      | unit_price         | 500             |
      | unity              | bag of ten      |
      | unit_price_ratio   | 0.1             |

  Scenario: I update product unit price when product price is 0
    When I update product "product1" prices with following information:
      | unit_price | 300 |
    Then product product1 should have following prices information:
      | price      | 0 |
      | unit_price | 0 |
    And product product1 should have following prices information:
      | price              | 0     |
      | price_tax_included | 0     |
      | ecotax             | 0     |
      | tax rules group    |       |
      | on_sale            | false |
      | wholesale_price    | 0     |
      | unit_price         | 0     |
      | unity              |       |
      | unit_price_ratio   | 0     |

  Scenario: I update product unit price along with product price
    When I update product "product1" prices with following information:
      | price      | 20  |
      | unit_price | 500 |
    Then product product1 should have following prices information:
      | price              | 20   |
      | price_tax_included | 20   |
      | unit_price         | 500  |
      | unit_price_ratio   | 0.04 |
    When I update product "product1" prices with following information:
      | price      | 0   |
      | unit_price | 500 |
    Then product product1 should have following prices information:
      | price              | 0 |
      | price_tax_included | 0 |
      | unit_price         | 0 |
      | unit_price_ratio   | 0 |

  Scenario: I update product tax the price tax included is impacted
    When I update product "product1" prices with following information:
      | price      | 20  |
    Then product product1 should have following prices information:
      | price              | 20 |
      | price_tax_included | 20 |
      | tax rules group    |    |
    When I update product "product1" prices with following information:
      | tax rules group | US-AL Rate (4%) |
    Then product product1 should have following prices information:
      | price              | 20              |
      | price_tax_included | 20.80           |
      | tax rules group    | US-AL Rate (4%) |
    When I update product "product1" prices with following information:
      | tax rules group | US-FL Rate (6%) |
    Then product product1 should have following prices information:
      | price              | 20              |
      | price_tax_included | 21.20           |
      | tax rules group    | US-FL Rate (6%) |

  Scenario: I update product prices providing non-existing tax rules group
    Given I update product "product1" prices with following information:
      | tax rules group | US-AL Rate (4%) |
    And product product1 should have following prices information:
      | tax rules group | US-AL Rate (4%) |
    When I update product "product1" prices and apply non-existing tax rules group
    Then I should get error that tax rules group does not exist
    And product product1 should have following prices information:
      | tax rules group | US-AL Rate (4%) |
