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

  Scenario: If the pack has availability preferences to deny order
    Given the product "product_pack" denies order if out of stock
    And the pack "product_pack" decrements pack only
    When I update quantity of product "product_pack" in the cart "cart_pack" to 11
    Then I should get an error that you have the maximum quantity available for this product
    And cart "cart_pack" should not contain product "product_pack"
    When I update quantity of product "product_pack" in the cart "cart_pack" to 10
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"

  Scenario: If the pack has availability preferences to deny order
    Given the product "product_pack" denies order if out of stock
    And the pack "product_pack" decrements products in pack only
    When I update quantity of product "product_pack" in the cart "cart_pack" to 6
    Then I should get an error that you have the maximum quantity available for this product
    And cart "cart_pack" should not contain product "product_pack"
    When I update quantity of product "product_pack" in the cart "cart_pack" to 5
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"

  Scenario: If the pack has availability preferences to deny order
    Given the product "product_pack" denies order if out of stock
    And the pack "product_pack" decrements both packs and products
    When I update quantity of product "product_pack" in the cart "cart_pack" to 6
    Then I should get an error that you have the maximum quantity available for this product
    And cart "cart_pack" should not contain product "product_pack"
    When I update quantity of product "product_pack" in the cart "cart_pack" to 5
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"

  Scenario: If the pack has availability preferences to allow order
    Given the product "product_pack" allows order if out of stock
    And the pack "product_pack" decrements pack only
    When I update quantity of product "product_pack" in the cart "cart_pack" to 11
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"

  Scenario: If the pack has availability preferences to allow order
    Given the product "product_pack" allows order if out of stock
    And the pack "product_pack" decrements products in pack only
    When I update quantity of product "product_pack" in the cart "cart_pack" to 6
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"

  Scenario: If the pack has availability preferences to allow order
    Given the product "product_pack" allows order if out of stock
    And the pack "product_pack" decrements both packs and products
    When I update quantity of product "product_pack" in the cart "cart_pack" to 11
    Then I should get no error
    And cart "cart_pack" should contain product "product_pack"
    When I delete product "product_pack" from cart "cart_pack"
    And cart "cart_pack" should not contain product "product_pack"
