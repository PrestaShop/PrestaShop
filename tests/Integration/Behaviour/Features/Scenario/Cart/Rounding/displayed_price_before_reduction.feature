# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags cart-displayed-price-before-reduction
@restore-all-tables-before-feature
@cart-displayed-price-before-reduction
Feature: Displayed price and price before reduction of a cart line
  As a customer
  I must see the same amount for the price of a product and for its price before reduction,
  whichever rounding mode the shop uses

  Scenario: A price with more decimals than the currency, rounded down
    Given I have an empty default cart
    And specific shop configuration for "rounding mode" is set to round down
    And there is a product in the catalog named "product1" with a price of 600.008 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then the displayed price and price before reduction of product "product1" in my cart should be identical

  Scenario: The same price rounded up
    Given I have an empty default cart
    And specific shop configuration for "rounding mode" is set to round up
    And there is a product in the catalog named "product1" with a price of 600.001 and 1000 items in stock
    When I add 1 items of product "product1" in my cart
    Then the displayed price and price before reduction of product "product1" in my cart should be identical
