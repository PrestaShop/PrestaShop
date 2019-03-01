@database-feature
Feature: Add product pack in cart
  As a customer
  I must be able to correctly add product packs in my cart

  Scenario: Check remaining quantity of pack when config is set to STOCK_TYPE_PACK_ONLY
    Given Specific shop configuration of "pack stock type" is set to STOCK_TYPE_PACK_ONLY
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    Then Remaining quantity of product named product6 should be 10
    Then Remaining quantity of product named product5 should be 50

  Scenario: Check remaining quantity of pack when config is set to STOCK_TYPE_PRODUCTS_ONLY
    Given Specific shop configuration of "pack stock type" is set to STOCK_TYPE_PRODUCTS_ONLY
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    Then Remaining quantity of product named product6 should be 5
    Then Remaining quantity of product named product5 should be 50

  Scenario: Check remaining quantity of pack when config is set to STOCK_TYPE_PACK_BOTH
    Given Specific shop configuration of "pack stock type" is set to STOCK_TYPE_PACK_BOTH
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    Then Remaining quantity of product named product6 should be 5
    Then Remaining quantity of product named product5 should be 50

  Scenario: Check pack if is in stock when config is set to STOCK_TYPE_PACK_ONLY
    Given Specific shop configuration of "pack stock type" is set to STOCK_TYPE_PACK_ONLY
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    Then pack with name product6 is in stock for quantity 10
    Then pack with name product6 is not in stock for quantity 11

  Scenario: Check pack if is in stock when config is set to STOCK_TYPE_PRODUCTS_ONLY
    Given Specific shop configuration of "pack stock type" is set to STOCK_TYPE_PRODUCTS_ONLY
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    Then pack with name product6 is in stock for quantity 5
    Then pack with name product6 is not in stock for quantity 6

  Scenario: Check pack if is in stock when config is set to STOCK_TYPE_PACK_BOTH
    Given Specific shop configuration of "pack stock type" is set to STOCK_TYPE_PACK_BOTH
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    Then pack with name product6 is in stock for quantity 5
    Then pack with name product6 is not in stock for quantity 6

  Scenario: Check product is correctly considered as a pack
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    Then product product6 is considered as a pack

  Scenario: Check pack/product count is correct when adding in pack if configuration is set to STOCK_TYPE_PACK_ONLY
    Given I have an empty default cart
    Given Specific shop configuration of "pack stock type" is set to STOCK_TYPE_PACK_ONLY
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    When I add product named product6 in my cart with quantity 2
    When I add product named product5 in my cart with quantity 30
    Then Quantity of product named product6 in my cart should be 2
    Then Quantity of product named product5 in my cart should be 30
    Then Deep quantity of product named product6 in my cart should be 2
    Then Deep quantity of product named product5 in my cart should be 30
    Then Distinct product count in my cart should be 2
    Then Remaining quantity of product named product6 should be 8
    Then Remaining quantity of product named product5 should be 20

  Scenario: Check pack/product count is correct when adding in pack if configuration is set to STOCK_TYPE_PRODUCTS_ONLY
    Given I have an empty default cart
    Given Specific shop configuration of "pack stock type" is set to STOCK_TYPE_PRODUCTS_ONLY
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    When I add product named product6 in my cart with quantity 2
    When I add product named product5 in my cart with quantity 30
    Then Quantity of product named product6 in my cart should be 2
    Then Quantity of product named product5 in my cart should be 30
    Then Deep quantity of product named product6 in my cart should be 2
    Then Deep quantity of product named product5 in my cart should be 50
    Then Distinct product count in my cart should be 2
    Then Remaining quantity of product named product6 should be 0
    Then Remaining quantity of product named product5 should be 0

  Scenario: Check pack/product count is correct when adding in pack if configuration is set to STOCK_TYPE_PACK_BOTH
    Given I have an empty default cart
    Given Specific shop configuration of "pack stock type" is set to STOCK_TYPE_PACK_BOTH
    Given there is a product with name product5 and price 23.86 and quantity 50
    Given there is a product with name product6 and price 12.34 and quantity 10
    Given product with name product6 is a pack containing quantity 10 of product named product5
    When I add product named product6 in my cart with quantity 2
    When I add product named product5 in my cart with quantity 30
    Then Quantity of product named product6 in my cart should be 2
    Then Quantity of product named product5 in my cart should be 30
    Then Deep quantity of product named product6 in my cart should be 2
    Then Deep quantity of product named product5 in my cart should be 50
    Then Distinct product count in my cart should be 2
    Then Remaining quantity of product named product6 should be 0
    Then Remaining quantity of product named product5 should be 0
