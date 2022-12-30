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
    # For all other shops the first associated was the multi shop one during the first association command
    And product product1 should have following supplier values for shop shop2:
      | default supplier           | multiSupplier1 |
      | default supplier reference |                |
    And product product1 should have following supplier values for shop shop3:
      | default supplier           | multiSupplier1 |
      | default supplier reference |                |
    And product product1 should have following supplier values for shop shop4:
      | default supplier           | multiSupplier1 |
      | default supplier reference |                |

  Scenario: I can update the product suppliers, their data is shared on all the shops (especially the one common to all shops)
    When I update product product1 suppliers for shop shop1:
      | product_supplier       | supplier       | reference                      | currency | price_tax_excluded |
      | product1supplier1      | supplier1      | my first supplier for product1 | USD      | 42                 |
      | product1multiSupplier1 | multiSupplier1 | temporary modif                | USD      | 10                 |
    And I update product product1 suppliers for shop shop2:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product1supplier2 | supplier2 | my second supplier for product1 | USD      | 69                 |
    And I update product product1 suppliers for shop shop3:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1supplier3      | supplier3      | my third supplier for product1  | USD      | 99                 |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
    Then product product1 should have following suppliers for shop shop1:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier1      | supplier1      | my first supplier for product1  | USD      | 42                 |
    And product product1 should have following suppliers for shop shop2:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier2      | supplier2      | my second supplier for product1 | USD      | 69                 |
    And product product1 should have following suppliers for shop shop3:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier3      | supplier3      | my third supplier for product1  | USD      | 99                 |
    And product product1 should have following suppliers for shop shop4:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier4      | supplier4      |                                 | USD      | 0                  |
    When I set product product1 default supplier to supplier2 for shop shop2
    When I set product product1 default supplier to supplier4 for shop shop4
    # Check that default supplier reference is updated appropriately
    And product product1 should have following supplier values for shop shop1:
      | default supplier           | supplier1                      |
      | default supplier reference | my first supplier for product1 |
    And product product1 should have following supplier values for shop shop2:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    And product product1 should have following supplier values for shop shop3:
      | default supplier           | multiSupplier1                  |
      | default supplier reference | multishop supplier for product1 |
    And product product1 should have following supplier values for shop shop4:
      | default supplier           | supplier4 |
      | default supplier reference |           |

  Scenario: Remove one of product suppliers
    # Remove association of supplier1
    When I associate suppliers to product "product1" for shop shop1
      | supplier       | product_supplier       |
      | multiSupplier1 | product1multiSupplier1 |
    Then product product1 should have the following suppliers assigned for shop shop1:
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
    Then product product1 should have following suppliers for shop shop1:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
    And product product1 should have following suppliers for shop shop2:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier2      | supplier2      | my second supplier for product1 | USD      | 69                 |
    And product product1 should have following suppliers for shop shop3:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier3      | supplier3      | my third supplier for product1  | USD      | 99                 |
    And product product1 should have following suppliers for shop shop4:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product1multiSupplier1 | multiSupplier1 | multishop supplier for product1 | USD      | 51                 |
      | product1supplier4      | supplier4      |                                 | USD      | 0                  |
    # Check that default supplier has been updated on shop 1
    And product product1 should have following supplier values for shop shop1:
      | default supplier           | multiSupplier1                  |
      | default supplier reference | multishop supplier for product1 |
    And product product1 should have following supplier values for shop shop2:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    And product product1 should have following supplier values for shop shop3:
      | default supplier           | multiSupplier1                  |
      | default supplier reference | multishop supplier for product1 |
    And product product1 should have following supplier values for shop shop4:
      | default supplier           | supplier4 |
      | default supplier reference |           |

  Scenario: Remove all associated product suppliers
    # By removing all suppliers for shop3 we remove the association on multiSupplier1 for all shops
    When I remove all associated product product1 suppliers for shop shop3
    # No more suppliers assigned for shop1 since it only had multiSupplier1 left
    Then product product1 should not have any suppliers assigned for shop shop1
    And product product1 should have the following suppliers assigned for shop shop2:
      | supplier2 |
    # No more suppliers assigned for shop3
    And product product1 should not have any suppliers assigned for shop shop3
    And product product1 should have the following suppliers assigned for shop shop4:
      | supplier4 |
    # No more suppliers details dor shop1
    And product product1 should not have suppliers infos for shop shop1
    And product product1 should have following suppliers for shop shop2:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product1supplier2 | supplier2 | my second supplier for product1 | USD      | 69                 |
    # No more suppliers details dor shop3
    And product product1 should not have suppliers infos for shop shop3
    And product product1 should have following suppliers for shop shop4:
      | product_supplier  | supplier  | reference | currency | price_tax_excluded |
      | product1supplier4 | supplier4 |           | USD      | 0                  |
    And product product1 should not have a default supplier for shop shop1
    And product product1 default supplier reference should be empty for shop shop1
    And product product1 should have following supplier values for shop shop2:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    And product product1 should not have a default supplier for shop shop3
    And product product1 default supplier reference should be empty for shop shop3
    And product product1 should have following supplier values for shop shop4:
      | default supplier           | supplier4 |
      | default supplier reference |           |
    # Now remove all suppliers for all shops
    When I remove all associated product product1 suppliers for all shops
    Then product product1 should not have any suppliers assigned for shops "shop1,shop2,shop3,shop4"
    And product product1 should not have suppliers infos for shops "shop1,shop2,shop3,shop4"
    And product product1 should not have a default supplier for shops "shop1,shop2,shop3,shop4"
    And product product1 default supplier reference should be empty for shops "shop1,shop2,shop3,shop4"

  Scenario: Update product default supplier when it is not associated with product or shop
    Given product product1 should not have any suppliers assigned for shops "shop1,shop2,shop3,shop4"
    And product product1 should not have a default supplier
    When I set product product1 default supplier to supplier1 for shop shop1
    Then I should get error that supplier is not associated with product
    And product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty
    When I associate suppliers to product "product1" for shop shop1
      | supplier  | product_supplier       |
      | supplier2 | product1multiSupplier2 |
    Then I should get error that supplier is not associated with shop
    When I set product product1 default supplier to supplier1 for shop shop2
    Then I should get error that supplier is not associated with shop

  Scenario: Standard product wholesale price should depend on default supplier price
    Given I add product "product2" to shop shop2 with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product product2 type should be standard for shop shop2
    And I copy product product2 from shop shop2 to shop shop3
    And product product2 should not have any suppliers assigned for shops "shop2,shop3"
    And product product2 should have following prices information for shops "shop2,shop3":
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 0     |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    # I associate two suppliers including the multi shop one so it will impact shop3 as well
    When I associate suppliers to product "product2" for shop shop2
      | supplier       | product_supplier       |
      | supplier2      | product2supplier2      |
      | multiSupplier1 | product2multiSupplier1 |
    And I update product product2 suppliers for shop shop2:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product2supplier2      | supplier2      | my second supplier for product2 | USD      | 20                 |
      | product2multiSupplier1 | multiSupplier1 | multishop supplier for product2 | USD      | 51                 |
    Then product product2 should have the following suppliers assigned for shop shop2:
      | supplier2      |
      | multiSupplier1 |
    Then product product2 should have the following suppliers assigned for shop shop3:
      | multiSupplier1 |
    And product product2 should have following suppliers for shop shop2:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product2multiSupplier1 | multiSupplier1 | multishop supplier for product2 | USD      | 51                 |
      | product2supplier2      | supplier2      | my second supplier for product2 | USD      | 20                 |
    And product product2 should have following suppliers for shop shop3:
      | product_supplier       | supplier       | reference                       | currency | price_tax_excluded |
      | product2multiSupplier1 | multiSupplier1 | multishop supplier for product2 | USD      | 51                 |
    And product product2 should have following supplier values for shop shop2:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product2 |
    And product product2 should have following supplier values for shop shop3:
      | default supplier           | multiSupplier1                  |
      | default supplier reference | multishop supplier for product2 |
    # Product wholesale is updated with value of default supplier for shop2 only
    And product product2 should have following prices information for shop shop2:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 20    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    And product product2 should have following prices information for shop shop3:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 51    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    # Changing the default supplier should also update the wholesale price
    When I set product product2 default supplier to multiSupplier1 for shop shop2
    Then product product2 should have following supplier values for shops "shop2,shop3":
      | default supplier           | multiSupplier1                  |
      | default supplier reference | multishop supplier for product2 |
    And product product2 should have following prices information for shops "shop2,shop3":
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 51    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    # The wholesale value is not removed even if default supplier has been removed
    When I remove all associated product "product2" suppliers for shop shop2
    Then product product2 should not have any suppliers assigned for shops shop2,shop3
    And product product2 should not have a default supplier for shops shop2,shop3
    And product product2 default supplier reference should be empty for shops shop2,shop3
    And product product2 should have following prices information for shops "shop2,shop3":
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 51    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |

  Scenario: Updating standard product wholesale price should update default supplier price
    # Prepare a product each has two associated suppliers, one individual to its shop a the multishop one
    # Only one of them (shop2) uses the multishop one as its default supplier
    Given there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    And I add product "product3" to shop shop1 with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And I copy product product3 from shop shop1 to shop shop2
    And I copy product product3 from shop shop2 to shop shop3
    And I copy product product3 from shop shop2 to shop shop4
    And product product3 type should be standard for shops "shop1,shop2,shop3,shop4"
    And product product3 should not have any suppliers assigned for shops "shop1,shop2,shop3,shop4"
    And product product3 should have following prices information for shops "shop1,shop2,shop3,shop4":
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 0     |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    When I associate suppliers to product "product3" for shop shop1
      | supplier       | product_supplier       |
      | supplier1      | product3supplier1      |
    And I associate suppliers to product "product3" for shop shop3
      | supplier       | product_supplier       |
      | supplier3      | product3supplier3      |
    And I associate suppliers to product "product3" for shop shop4
      | supplier       | product_supplier       |
      | supplier4      | product3supplier4      |
    # Finish by associating on shop2 with multiSupplier1 as first one (so default for this shop) this associates it to all other
    # shops in the process but they still have their "shop-only" supplier defined as default
    And I associate suppliers to product "product3" for shop shop2
      | supplier       | product_supplier       |
      | multiSupplier1 | product3multiSupplier1 |
      | supplier2      | product3supplier2      |
    And I update product product3 suppliers for shop shop1:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3supplier1      | supplier1      | my first supplier for product3     | USD      | 11                 |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 21                 |
    And I update product product3 suppliers for shop shop2:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product3supplier2 | supplier2 | my second supplier for product3 | USD      | 12                 |
    And I update product product3 suppliers for shop shop3:
      | product_supplier  | supplier  | reference                      | currency | price_tax_excluded |
      | product3supplier3 | supplier3 | my third supplier for product3 | USD      | 13                 |
    And I update product product3 suppliers for shop shop4:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product3supplier4 | supplier4 | my fourth supplier for product3 | USD      | 14                 |
    And product product3 should have the following suppliers assigned for shop shop1:
      | supplier1      |
      | multiSupplier1 |
    And product product3 should have the following suppliers assigned for shop shop2:
      | supplier2      |
      | multiSupplier1 |
    And product product3 should have the following suppliers assigned for shop shop3:
      | supplier3      |
      | multiSupplier1 |
    And product product3 should have the following suppliers assigned for shop shop4:
      | supplier4      |
      | multiSupplier1 |
    And product product3 should have following suppliers for shop shop1:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 21                 |
      | product3supplier1      | supplier1      | my first supplier for product3     | USD      | 11                 |
    And product product3 should have following suppliers for shop shop2:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 21                 |
      | product3supplier2      | supplier2      | my second supplier for product3    | USD      | 12                 |
    And product product3 should have following suppliers for shop shop3:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 21                 |
      | product3supplier3      | supplier3      | my third supplier for product3     | USD      | 13                 |
    And product product3 should have following suppliers for shop shop4:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 21                 |
      | product3supplier4      | supplier4      | my fourth supplier for product3    | USD      | 14                 |
    And product product3 should have following supplier values for shop shop1:
      | default supplier           | supplier1                      |
      | default supplier reference | my first supplier for product3 |
    And product product3 should have following supplier values for shop shop2:
      | default supplier           | multiSupplier1                     |
      | default supplier reference | my multishop supplier for product3 |
    And product product3 should have following supplier values for shop shop3:
      | default supplier           | supplier3                      |
      | default supplier reference | my third supplier for product3 |
    And product product3 should have following supplier values for shop shop4:
      | default supplier           | supplier4                       |
      | default supplier reference | my fourth supplier for product3 |
    # Product wholesale price has been updated as the default supplier was updated
    And product product3 should have following prices information for shop shop1:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 11    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    And product product3 should have following prices information for shop shop2:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 21    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    And product product3 should have following prices information for shop shop3:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 13    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    And product product3 should have following prices information for shop shop4:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 14    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    # Now I update wholesale price for miscellaneous shops this should update the appropriate suppliers
    When I update product "product3" with following values for shop shop1:
      | wholesale_price | 31 |
    And I update product "product3" with following values for shop shop2:
      | wholesale_price | 32 |
    And I update product "product3" with following values for shop shop3:
      | wholesale_price | 33 |
    And I update product "product3" with following values for shop shop4:
      | wholesale_price | 34 |
    # Updating the product wholesale price impacts the default supplier price
    Then product product3 should have following suppliers for shop shop1:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 32                 |
      | product3supplier1      | supplier1      | my first supplier for product3     | USD      | 31                 |
    # For shop2 the supplier2 wasn't updated since it's not the default one, however multiSupplier1 is updated for all shops
    And product product3 should have following suppliers for shop shop2:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 32                 |
      | product3supplier2      | supplier2      | my second supplier for product3    | USD      | 12                 |
    And product product3 should have following suppliers for shop shop3:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 32                 |
      | product3supplier3      | supplier3      | my third supplier for product3     | USD      | 33                 |
    And product product3 should have following suppliers for shop shop4:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 32                 |
      | product3supplier4      | supplier4      | my fourth supplier for product3    | USD      | 34                 |
    # Now common multishop is associated to to shops which are modified one after another
    When I set product product3 default supplier to multiSupplier1 for shop shop4
    Then product product3 should have following supplier values for shops "shop2,shop4":
      | default supplier           | multiSupplier1                     |
      | default supplier reference | my multishop supplier for product3 |
    When I update product "product3" with following values for shop shop4:
      | wholesale_price | 44 |
    # Check that multiSupplier1 is updated everywhere
    Then product product3 should have following suppliers for shop shop1:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 44                 |
      | product3supplier1      | supplier1      | my first supplier for product3     | USD      | 31                 |
    And product product3 should have following suppliers for shop shop2:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 44                 |
      | product3supplier2      | supplier2      | my second supplier for product3    | USD      | 12                 |
    And product product3 should have following suppliers for shop shop3:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 44                 |
      | product3supplier3      | supplier3      | my third supplier for product3     | USD      | 33                 |
    And product product3 should have following suppliers for shop shop4:
      | product_supplier       | supplier       | reference                          | currency | price_tax_excluded |
      | product3multiSupplier1 | multiSupplier1 | my multishop supplier for product3 | EUR      | 44                 |
      | product3supplier4      | supplier4      | my fourth supplier for product3    | USD      | 34                 |
    # Since wholesale prices along with default's supplier price are in sync, the shop2 wholesale price must be updated as well
    And product product3 should have following prices information for shop shop2,shop4:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 44    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
