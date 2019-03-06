@database-feature
Feature: Cart rule (percent) calculation with multiple cart rules without code
  As a customer
  I must be able to have correct cart total when adding cart rules

  Scenario: 3 products in cart, several quantities, 2x % global vouchers
    Given I have an empty default cart
    Given Shop configuration of PS_CART_RULE_FEATURE_ACTIVE is set to 1
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a cart rule with name cartrule14 and percent discount of 10.0% and priority of 12 and quantity of 1000 and quantity per user of 1000
    Given There is a cart rule with name cartrule15 and percent discount of 10.0% and priority of 13 and quantity of 1000 and quantity per user of 1000
    When I add product named product2 in my cart with quantity 2
    When I add product named product1 in my cart with quantity 3
    When I add product named product3 in my cart with quantity 1
    When I add cart rule named cartrule14 to my cart
    When I add cart rule named cartrule15 to my cart
    Then Expected total of my cart tax included should be 132.874
    #known to fail on previous
    #Then Expected total of my cart tax included should be 132.874 with previous calculation method
