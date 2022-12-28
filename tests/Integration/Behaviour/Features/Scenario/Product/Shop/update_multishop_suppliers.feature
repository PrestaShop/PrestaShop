# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-suppliers
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@clear-cache-after-feature
@product-multi-shop
@update-multi-shop-suppliers
Feature: Copy product from shop to shop.
  As a BO user I want to be able to edit associate suppliers in a multishop context.

  # First init the required data for these tests, they are created in a scenario (not in the background) to avoid
  # duplicating them in the following scenarios
  Scenario: I create the required suppliers for this feature
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And language "french" with locale "fr-FR" exists
    And I add new supplier supplier1 with the following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,1              |
      | shops                   | [shop1]            |
    And I add new supplier supplier2 with the following properties:
      | name                    | my supplier 2      |
      | address                 | Donelaicio st. 2   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr two |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,2              |
      | shops                   | [shop2]            |
    And I add new supplier supplier3 with the following properties:
      | name                    | my supplier 3    |
      | address                 | Donelaicio st. 3 |
      | city                    | Kaunas           |
      | country                 | Lithuania        |
      | enabled                 | true             |
      | description[en-US]      | just a 3         |
      | meta title[en-US]       | my third supp    |
      | meta description[en-US] |                  |
      | meta keywords[en-US]    | sup,3            |
      | shops                   | [shop3]          |
    And I add new supplier supplier4 with the following properties:
      | name                    | my supplier 4    |
      | address                 | Donelaicio st. 4 |
      | city                    | Kaunas           |
      | country                 | Lithuania        |
      | enabled                 | true             |
      | description[en-US]      | just a 4         |
      | meta title[en-US]       | my fourth supp   |
      | meta description[en-US] |                  |
      | meta keywords[en-US]    | sup,4            |
      | shops                   | [shop4]          |
    And I add new supplier multiSupplier1 with the following properties:
      | name                    | multi supplier 1          |
      | address                 | Donelaicio st. A          |
      | city                    | Kaunas                    |
      | country                 | Lithuania                 |
      | enabled                 | true                      |
      | description[en-US]      | a multi supplier          |
      | meta title[en-US]       | my multi supplier nr one  |
      | meta description[en-US] |                           |
      | meta keywords[en-US]    | multi,sup,1               |
      | shops                   | [shop1,shop2,shop3,shop4] |
    Given I add product "product1" to shop shop1 with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And I copy product product1 from shop shop1 to shop shop2
    And I copy product product1 from shop shop1 to shop shop3
    And I copy product product1 from shop shop1 to shop shop4
    And product product1 type should be standard
    And product product1 should not have any suppliers assigned for shops "shop1,shop2,shop3,shop4"

  Scenario: I can associate suppliers depending on a shop context without removing other associations
    # Associate for different shops
    When I associate suppliers to product "product1" for shop shop1
      | supplier       | product_supplier       |
      | supplier1      | product1supplier1      |
      | multiSupplier1 | product1multiSupplier1 |
    And I associate suppliers to product "product1" for shop shop2
      | supplier       | product_supplier       |
      | supplier2      | product1supplier2      |
      | multiSupplier1 | product1multiSupplier1 |
    And I associate suppliers to product "product1" for shop shop3
      | supplier       | product_supplier       |
      | supplier3      | product1supplier3      |
      | multiSupplier1 | product1multiSupplier1 |
    And I associate suppliers to product "product1" for shop shop4
      | supplier       | product_supplier       |
      | supplier4      | product1supplier4      |
      | multiSupplier1 | product1multiSupplier1 |
    # Check associations
    Then product product1 should have the following suppliers assigned for shop shop1:
      | supplier1      |
      | multiSupplier1 |
    And product product1 should have the following suppliers assigned for shop shop2:
      | supplier2      |
      | multiSupplier1 |
    And product product1 should have the following suppliers assigned for shop shop3:
      | supplier3      |
      | multiSupplier1 |
    And product product1 should have the following suppliers assigned for shop shop4:
      | supplier4      |
      | multiSupplier1 |
    # Multi is first because the result is order by supplier name
    And product product1 should have following suppliers for shop shop1:
      | product_supplier       | supplier       | reference | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 |           | USD      | 0                  |
      | product1supplier1      | supplier1      |           | USD      | 0                  |
    And product product1 should have following suppliers for shop shop2:
      | product_supplier       | supplier       | reference | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 |           | USD      | 0                  |
      | product1supplier2      | supplier2      |           | USD      | 0                  |
    And product product1 should have following suppliers for shop shop3:
      | product_supplier       | supplier       | reference | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 |           | USD      | 0                  |
      | product1supplier3      | supplier3      |           | USD      | 0                  |
    And product product1 should have following suppliers for shop shop4:
      | product_supplier       | supplier       | reference | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 |           | USD      | 0                  |
      | product1supplier4      | supplier4      |           | USD      | 0                  |
    # Check default supplier for each shop (first one associated)
    And product product1 should have following supplier values for shop shop1:
      | default supplier           | supplier1 |
      | default supplier reference |           |
    And product product1 should have following supplier values for shop shop2:
      | default supplier           | supplier2 |
      | default supplier reference |           |
    And product product1 should have following supplier values for shop shop3:
      | default supplier           | supplier3 |
      | default supplier reference |           |
    And product product1 should have following supplier values for shop shop4:
      | default supplier           | supplier4 |
      | default supplier reference |           |

  Scenario: I can update the product suppliers, their data is shared on all the shops (especially the one common to all shops)
    When I update product product1 suppliers for shop shop1:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1supplier1      | supplier1      | my first supplier for product1  | USD      | 42                 |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
    Then product product1 should have following suppliers for shop shop1:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier1      | supplier1      | my first supplier for product1  | USD      | 42                 |
    And product product1 should have following suppliers for shop shop2:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier2      | supplier2      |                                 | USD      | 0                  |
    And product product1 should have following suppliers for shop shop3:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier3      | supplier3      |                                 | USD      | 0                  |
    And product product1 should have following suppliers for shop shop4:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier4      | supplier4      |                                 | USD      | 0                  |
    When I set product product1 default supplier to multiSupplier1 for shop shop3
    # Check that default supplier reference is updated appropriately
    And product product1 should have following supplier values for shop shop1:
      | default supplier           | supplier1                      |
      | default supplier reference | my first supplier for product1 |
    And product product1 should have following supplier values for shop shop2:
      | default supplier           | supplier2 |
      | default supplier reference |           |
    And product product1 should have following supplier values for shop shop3:
      | default supplier           | multiSupplier1                  |
      | default supplier reference | multishop supplier for product1 |
    And product product1 should have following supplier values for shop shop4:
      | default supplier           | supplier4 |
      | default supplier reference |           |
