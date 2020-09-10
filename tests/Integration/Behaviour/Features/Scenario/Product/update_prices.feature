# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-prices
@reset-database-before-feature
@update-prices
Feature: Update product price fields from Back Office (BO).
  As a BO user I want to be able to update product fields associated with price.

  Scenario: I update product prices
    Given I add product "product1" with following information:
      | name       | en-US:magic staff   |
      | is_virtual | false               |
    And product product1 should have following prices information:
      | price              | 0           |
      | ecotax             | 0           |
      | tax rules group    |             |
      | on_sale            | false       |
      | wholesale_price    | 0           |
      | unit_price         | 0           |
      | unity              |             |
      | unit_price_ratio   | 0           |
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
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
#      #todo: rounding issue. #19620
#      #| unit_price         | 900         |
      | unity              | bag of ten  |
      | unit_price_ratio   | 0.112211    |

    Scenario: I partially update product prices, providing only those values which I want to update
      Given product product1 should have following prices information:
        | price              | 100.99          |
        | ecotax             | 0               |
        | tax rules group    | US-AL Rate (4%) |
        | on_sale            | true            |
        | wholesale_price    | 70              |
#      #todo: rounding issue. #19620
#      #| unit_price         | 900         |
        | unity              | bag of ten  |
        | unit_price_ratio   | 0.112211    |
      When I update product "product1" prices with following information:
        | price              | 200         |
      Then product product1 should have following prices information:
        | price              | 200             |
        | ecotax             | 0               |
        | tax rules group    | US-AL Rate (4%) |
        | on_sale            | true            |
        | wholesale_price    | 70              |
#        #todo: rounding issue. #19620
#        #| unit_price         | 900         |
        | unity              | bag of ten      |
        | unit_price_ratio   | 0.222222        |
      When I update product "product1" prices with following information:
        | ecotax              | 5.5            |
        | on_sale             | false          |
      Then product product1 should have following prices information:
        | price              | 200             |
        | ecotax             | 5.5             |
        | tax rules group    | US-AL Rate (4%) |
        | on_sale            | false           |
        | wholesale_price    | 70              |
#        #todo: rounding issue. #19620
#        #| unit_price         | 900         |
        | unity              | bag of ten      |
        | unit_price_ratio   | 0.222222        |

      Scenario: I update product prices with negative values
        Given I add product "product2" with following information:
          | name       | en-US: white hat  |
          | is_virtual | false             |
        And I update product "product2" prices with following information:
          | price           | 50           |
        And product product2 should have following prices information:
          | price           | 50           |
          | ecotax          | 0            |
          | wholesale_price | 0            |
          | unit_price      | 0            |
        When I update product "product2" prices with following information:
          | price           | -20          |
        Then I should get error that product "price" is invalid
        When I update product "product2" prices with following information:
          | ecotax          | -2           |
        Then I should get error that product "ecotax" is invalid
        When I update product "product2" prices with following information:
          | wholesale_price | -35          |
        Then I should get error that product "wholesale_price" is invalid
        When I update product "product2" prices with following information:
          | unit_price      | -300         |
        Then I should get error that product "unit_price" is invalid

      Scenario: I update product unit price when product price is 0
        Given I add product "product3" with following information:
          | name       | en-US: black hat  |
          | is_virtual | false             |
        And product product3 should have following prices information:
          | price           | 0            |
          | unit_price      | 0            |
        When I update product "product3" prices with following information:
          | unit_price      | 300          |
        Then product product3 should have following prices information:
          | price           | 0            |
          | unit_price      | 0            |

      Scenario: I update product unit price along with product price
        Given I add product "product4" with following information:
          | name       | en-US: blue dress  |
          | is_virtual | false              |
        And product product4 should have following prices information:
          | price            | 0            |
          | unit_price       | 0            |
        When I update product "product4" prices with following information:
          | price            | 20           |
          | unit_price       | 500          |
        Then product product4 should have following prices information:
          | price            | 20           |
          | unit_price       | 500          |
          | unit_price_ratio | 0.04         |
        When I update product "product4" prices with following information:
          | price            | 0            |
          | unit_price       | 500          |
        Then product product4 should have following prices information:
          | price            | 0            |
          | unit_price       | 0            |
          | unit_price_ratio | 0            |

      Scenario: I update product prices providing non-existing tax rules group
        Given I add product "product5" with following information:
          | name       | en-US: black tie     |
          | is_virtual | false                |
        And product product5 should have following prices information:
          | tax rules group |           |
        When I update product "product5" prices and apply non-existing tax rules group
        Then I should get error that product "tax rules group" is invalid
