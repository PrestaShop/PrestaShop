# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags cart-updating-product-pack
@reset-database-before-feature
@cart-updating-product-pack
Feature: Add product pack in cart
  As a customer
  I must be able to correctly update product packs in my cart

  Background:
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And country "US" is enabled
    Given I create an empty cart "cart_pack" for customer "testCustomer"
    And there is a product in the catalog named "product_in_pack_1" with a price of 12.34 and 10 items in stock
    And there is a product in the catalog named "product_in_pack_2" with a price of 56.78 and 10 items in stock
    And there is a product in the catalog named "product_pack" with a price of 90.12 and 10 items in stock
    And product "product_pack" is a pack containing 1 items of product "product_in_pack_1"
    And product "product_pack" is a pack containing 2 items of product "product_in_pack_2"
    Then the available stock for product "product_pack" should be 10
    And the remaining available stock for product "product_pack" should be 10
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10

  Scenario: If the pack has availability preferences to deny order and only pack is decremented
    # As I check pack stock 10 is the limit
    Given the product "product_pack" denies order if out of stock
    And the pack "product_pack" decrements pack only
    When I update quantity of product "product_pack" in the cart "cart_pack" to 11
    Then I should get an error that you have the maximum quantity available for this pack
    And cart "cart_pack" should not contain product "product_pack"
    And the remaining available stock for product "product_pack" should be 10
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10
    When I update quantity of product "product_pack" in the cart "cart_pack" to 10
    Then I should get no error
    And the remaining available stock for product "product_pack" should be 0
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10
    And cart "cart_pack" should contain product "product_pack"
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"
    And the remaining available stock for product "product_pack" should be 10
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10

  Scenario: If the pack has availability preferences to deny order and only products in pack is decremented
    # As I check products stock, 5 is the limit (5 product_in_pack_1 & 10 product_in_pack_2)
    Given the product "product_pack" denies order if out of stock
    And the pack "product_pack" decrements products in pack only
    When I update quantity of product "product_pack" in the cart "cart_pack" to 6
    Then I should get an error that you have the maximum quantity available for this pack
    And cart "cart_pack" should not contain product "product_pack"
    ## The stock of pack is calculated from the stock of its unit
    And the remaining available stock for product "product_pack" should be 5
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10
    When I update quantity of product "product_pack" in the cart "cart_pack" to 5
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    And the remaining available stock for product "product_pack" should be 0
    And the remaining available stock for product "product_in_pack_1" should be 5
    And the remaining available stock for product "product_in_pack_2" should be 0
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"
    And the remaining available stock for product "product_pack" should be 5
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10

  Scenario: If the pack has availability preferences to deny order and both packs and products are decremented
    # As I check both, the products stock is limiting, 5 is the limit (5 product_in_pack_1 & 10 product_in_pack_2)
    Given the product "product_pack" denies order if out of stock
    And the pack "product_pack" decrements both packs and products
    When I update quantity of product "product_pack" in the cart "cart_pack" to 6
    Then I should get an error that you have the maximum quantity available for this pack
    And cart "cart_pack" should not contain product "product_pack"
    ## The stock of pack is calculated from the stock of its unit
    And the remaining available stock for product "product_pack" should be 5
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10
    When I update quantity of product "product_pack" in the cart "cart_pack" to 5
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"
    And the remaining available stock for product "product_pack" should be 5
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10

  Scenario: If the pack has availability preferences to allow order and only pack is decremented
    # As I check pack stock 10 is the limit
    Given the product "product_pack" allows order if out of stock
    And the pack "product_pack" decrements pack only
    When I update quantity of product "product_pack" in the cart "cart_pack" to 11
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    And the remaining available stock for product "product_pack" should be -1
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"
    And the remaining available stock for product "product_pack" should be 10
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10

  Scenario: If the pack has availability preferences to allow order and only products in pack is decremented
    # As I check products stock, 5 is the limit (5 product_in_pack_1 & 10 product_in_pack_2)
    Given the product "product_pack" allows order if out of stock
    And the pack "product_pack" decrements products in pack only
    When I update quantity of product "product_pack" in the cart "cart_pack" to 6
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    And the remaining available stock for product "product_pack" should be -1
    And the remaining available stock for product "product_in_pack_1" should be 4
    And the remaining available stock for product "product_in_pack_2" should be -2
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"
    And the remaining available stock for product "product_pack" should be 5
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10

  Scenario: If the pack has availability preferences to allow order and both packs and products are decremented
    # As I check both, the products stock is limiting, 5 is the limit (5 product_in_pack_1 & 10 product_in_pack_2)
    Given the product "product_pack" allows order if out of stock
    And the pack "product_pack" decrements both packs and products
    When I update quantity of product "product_pack" in the cart "cart_pack" to 11
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    And the remaining available stock for product "product_pack" should be -6
    And the remaining available stock for product "product_in_pack_1" should be -1
    And the remaining available stock for product "product_in_pack_2" should be -12
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"
    And the remaining available stock for product "product_pack" should be 5
    And the remaining available stock for product "product_in_pack_1" should be 10
    And the remaining available stock for product "product_in_pack_2" should be 10
