@reset-database-before-feature
Feature: Add product pack in cart
  As a customer
  I must be able to correctly add product packs in my cart

  Scenario: Check remaining quantity of pack when config is set to STOCK_TYPE_PACK_ONLY
    Given specific shop configuration for "pack stock type" is set to decrement packs only
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    Then the remaining available stock for product "product6" should be 10
    Then the remaining available stock for product "product5" should be 50

  Scenario: Check remaining quantity of pack when config is set to STOCK_TYPE_PRODUCTS_ONLY
    Given specific shop configuration for "pack stock type" is set to decrement products only
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    Then the remaining available stock for product "product6" should be 5
    Then the remaining available stock for product "product5" should be 50

  Scenario: Check remaining quantity of pack when config is set to STOCK_TYPE_PACK_BOTH
    Given specific shop configuration for "pack stock type" is set to decrement both packs and products
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    Then the remaining available stock for product "product6" should be 5
    Then the remaining available stock for product "product5" should be 50

  Scenario: Check pack if is in stock when config is set to STOCK_TYPE_PACK_ONLY
    Given specific shop configuration for "pack stock type" is set to decrement packs only
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    Then pack "product6" has enough stock for an order of 10 items
    Then pack "product6" has not enough stock for an order of 11 items

  Scenario: Check pack if is in stock when config is set to STOCK_TYPE_PRODUCTS_ONLY
    Given specific shop configuration for "pack stock type" is set to decrement products only
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    Then pack "product6" has enough stock for an order of 5 items
    Then pack "product6" has not enough stock for an order of 6 items

  Scenario: Check pack if is in stock when config is set to STOCK_TYPE_PACK_BOTH
    Given specific shop configuration for "pack stock type" is set to decrement both packs and products
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    Then pack "product6" has enough stock for an order of 5 items
    Then pack "product6" has not enough stock for an order of 6 items

  Scenario: Check product is correctly considered as a pack
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    Then product "product6" is considered as a pack

  Scenario: Check pack/product count is correct when adding in pack if configuration is set to STOCK_TYPE_PACK_ONLY
    Given I have an empty default cart
    Given specific shop configuration for "pack stock type" is set to decrement packs only
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    When I add 2 items of product "product6" in my cart
    When I add 30 items of product "product5" in my cart
    Then my cart should contain 2 units of product "product6", including items in pack
    Then my cart should contain 30 units of product "product5", including items in pack
    Then my cart should contain 2 units of product "product6", excluding items in pack
    Then my cart should contain 30 units of product "product5", excluding items in pack
    Then I should have 2 different products in my cart
    Then the remaining available stock for product "product6" should be 8
    Then the remaining available stock for product "product5" should be 20

  Scenario: Check pack/product count is correct when adding in pack if configuration is set to STOCK_TYPE_PRODUCTS_ONLY
    Given I have an empty default cart
    Given specific shop configuration for "pack stock type" is set to decrement products only
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    When I add 2 items of product "product6" in my cart
    When I add 30 items of product "product5" in my cart
    Then my cart should contain 2 units of product "product6", excluding items in pack
    Then my cart should contain 30 units of product "product5", excluding items in pack
    Then my cart should contain 2 units of product "product6", including items in pack
    Then my cart should contain 50 units of product "product5", including items in pack
    Then I should have 2 different products in my cart
    Then the remaining available stock for product "product6" should be 0
    Then the remaining available stock for product "product5" should be 0

  Scenario: Check pack/product count is correct when adding in pack if configuration is set to STOCK_TYPE_PACK_BOTH
    Given I have an empty default cart
    Given specific shop configuration for "pack stock type" is set to decrement both packs and products
    Given there is a product in the catalog named "product5" with a price of 23.86 and 50 items in stock
    Given there is a product in the catalog named "product6" with a price of 12.34 and 10 items in stock
    Given product "product6" is a pack containing 10 items of product "product5"
    When I add 2 items of product "product6" in my cart
    When I add 30 items of product "product5" in my cart
    Then my cart should contain 2 units of product "product6", excluding items in pack
    Then my cart should contain 30 units of product "product5", excluding items in pack
    Then my cart should contain 2 units of product "product6", including items in pack
    Then my cart should contain 50 units of product "product5", including items in pack
    Then I should have 2 different products in my cart
    Then the remaining available stock for product "product6" should be 0
    Then the remaining available stock for product "product5" should be 0
