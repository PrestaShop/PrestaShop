# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags unit-price-specific-price
@restore-products-before-feature
@clear-cache-before-feature
@unit-price-specific-price
@specific-prices
Feature: Calculate product unit price for Front Office with specific prices
  As a customer
  I need product unit prices to use the displayed product price with the stored unit price ratio

  Scenario: Fixed specific price should not be used to calculate unit price ratio
    Given I add product "product1" with following information:
      | name[en-US] | Unit price product |
      | type        | standard           |
    When I update product "product1" with following values:
      | price      | 100 |
      | unit_price | 10  |
    And I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | 0      |
      | includes tax    | false  |
      | fixed price     | 80     |
      | from quantity   | 1      |
    Then product product1 should have following prices information:
      | price            | 100 |
      | unit_price       | 10  |
      | unit_price_ratio | 10  |
    And product product1 should have following front office prices:
      | price_tax_exc           | 80 |
      | unit_price_ratio        | 10 |
      | unit_price_tax_excluded | 8  |
