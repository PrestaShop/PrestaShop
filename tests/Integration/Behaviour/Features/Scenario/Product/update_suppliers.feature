# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-suppliers
@restore-products-before-feature
@restore-currencies-after-feature
@clear-cache-before-feature
@update-suppliers
Feature: Update product suppliers from Back Office (BO)
  As a BO user
  I need to be able to update product suppliers from BO

  Background:
    Given shop "shop1" with name "test_shop" exists
    And single shop shop1 context is loaded
    And language "language1" with locale "en-US" exists
    And there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63

  Scenario: Update standard product suppliers
    And I add new supplier supplier1 with following properties:
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
    And I add new supplier supplier2 with following properties:
      | name                    | my supplier 2      |
      | address                 | Donelaicio st. 2   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr two |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,2              |
      | shops                   | [shop1]            |
    And I add new supplier supplier3 with following properties:
      | name                    | my supplier 3    |
      | address                 | Donelaicio st. 3 |
      | city                    | Kaunas           |
      | country                 | Lithuania        |
      | enabled                 | true             |
      | description[en-US]      | just a 3         |
      | meta title[en-US]       | my third supp    |
      | meta description[en-US] |                  |
      | meta keywords[en-US]    | sup,3            |
      | shops                   | [shop1]          |
    Given I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product product1 type should be standard
    And product product1 should not have any suppliers assigned
    # Association and update are performed by two distinct commands
    When I associate suppliers to product "product1"
      | supplier  | product_supplier  |
      | supplier1 | product1supplier1 |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
    And product product1 should have following suppliers:
      | product_supplier  | supplier  | reference | currency | price_tax_excluded |
      | product1supplier1 | supplier1 |           | USD      | 0                  |
    # Update product suppliers using their references
    When I update product product1 suppliers:
      | product_supplier  | supplier  | reference                      | currency | price_tax_excluded |
      | product1supplier1 | supplier1 | my first supplier for product1 | USD      | 10                 |
    Then product product1 should have following suppliers:
      | product_supplier  | supplier  | reference                      | currency | price_tax_excluded |
      | product1supplier1 | supplier1 | my first supplier for product1 | USD      | 10                 |
    # Default supplier is the first one
    And product product1 should have following supplier values:
      | default supplier           | supplier1                      |
      | default supplier reference | my first supplier for product1 |
    When I associate suppliers to product "product1"
      | supplier  | product_supplier  |
      | supplier1 | product1supplier1 |
      | supplier2 | product1supplier2 |
      | supplier3 | product1supplier3 |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
      | supplier3 |
    When I update product product1 suppliers:
      | product_supplier  | supplier  | reference                          | currency | price_tax_excluded |
      | product1supplier1 | supplier1 | my new first supplier for product1 | USD      | 10                 |
      | product1supplier2 | supplier2 | my second supplier for product1    | EUR      | 11                 |
      | product1supplier3 | supplier3 | my third supplier for product1     | EUR      | 20                 |
    Then product product1 should have following suppliers:
      | product_supplier  | supplier  | reference                          | currency | price_tax_excluded |
      | product1supplier1 | supplier1 | my new first supplier for product1 | USD      | 10                 |
      | product1supplier2 | supplier2 | my second supplier for product1    | EUR      | 11                 |
      | product1supplier3 | supplier3 | my third supplier for product1     | EUR      | 20                 |
    # Default supplier was already set it should be the same but reference is updated
    And product product1 should have following supplier values:
      | default supplier           | supplier1                          |
      | default supplier reference | my new first supplier for product1 |
    # Explicitly set default supplier for product
    When I set product product1 default supplier to supplier2
    And product product1 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |

  Scenario: Remove one of product suppliers
    Given product product1 should have following suppliers:
      | product_supplier  | supplier  | reference                          | currency | price_tax_excluded |
      | product1supplier1 | supplier1 | my new first supplier for product1 | USD      | 10                 |
      | product1supplier2 | supplier2 | my second supplier for product1    | EUR      | 11                 |
      | product1supplier3 | supplier3 | my third supplier for product1     | EUR      | 20                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    # Associate only ywo suppliers meaning one is removed
    When I associate suppliers to product "product1"
      | supplier  | product_supplier  |
      | supplier1 | product1supplier1 |
      | supplier2 | product1supplier2 |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And product product1 should have following suppliers:
      | product_supplier  | supplier  | reference                          | currency | price_tax_excluded |
      | product1supplier1 | supplier1 | my new first supplier for product1 | USD      | 10                 |
      | product1supplier2 | supplier2 | my second supplier for product1    | EUR      | 11                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product1 |
    # If default supplier is removed another one is automatically associated
    When I associate suppliers to product "product1"
      | supplier  | product_supplier     |
      | supplier3 | product1supplier3bis |
      | supplier1 | product1supplier1    |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier3 |
    And product product1 should have following suppliers:
      | product_supplier     | supplier  | reference                          | currency | price_tax_excluded |
      | product1supplier1    | supplier1 | my new first supplier for product1 | USD      | 10                 |
      | product1supplier3bis | supplier3 |                                    | USD      | 0                  |
    And product product1 should have following supplier values:
      | default supplier           | supplier3 |
      | default supplier reference |           |

  Scenario: Remove all associated product suppliers
    Given product product1 type should be standard
    And product product1 should have following suppliers:
      | product_supplier     | supplier  | reference                          | currency | price_tax_excluded |
      | product1supplier1    | supplier1 | my new first supplier for product1 | USD      | 10                 |
      | product1supplier3bis | supplier3 |                                    | USD      | 0                  |
    And product product1 should have following supplier values:
      | default supplier           | supplier3 |
      | default supplier reference |           |
    When I remove all associated product product1 suppliers
    Then product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty

  Scenario: Update product default supplier when it is not associated with product
    Given product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    When I set product product1 default supplier to supplier1
    Then I should get error that supplier is not associated with product
    And product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty

  Scenario: Standard product wholesale price should depend on default supplier price
    Given I add product "product3" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product product3 type should be standard
    And product product3 should not have any suppliers assigned
    And product product3 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 0     |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    When I associate suppliers to product "product3"
      | supplier  | product_supplier  |
      | supplier1 | product3supplier1 |
      | supplier2 | product3supplier2 |
    And I update product product3 suppliers:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product3supplier1 | supplier1 | my first supplier for product3  | USD      | 10                 |
      | product3supplier2 | supplier2 | my second supplier for product3 | USD      | 20                 |
    Then product product3 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And product product3 should have following suppliers:
      | product_supplier  | supplier  | reference                      | currency | price_tax_excluded |
      | product3supplier1 | supplier1 | my first supplier for product3 | USD      | 10                 |
      | product3supplier2 | supplier2 | my second supplier for product3 | USD      | 20                 |
    And product product3 should have following supplier values:
      | default supplier           | supplier1                      |
      | default supplier reference | my first supplier for product3 |
    # Product wholesale is updated with value of default supplier
    And product product3 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 10    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    # Changing the default supplier should also update the wholesale price
    When I set product product3 default supplier to supplier2
    Then product product3 should have following supplier values:
      | default supplier           | supplier2                       |
      | default supplier reference | my second supplier for product3 |
    And product product3 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 20    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    # The wholesale value is not removed even if default supplier has been removed
    When I remove all associated product "product3" suppliers
    Then product product3 should not have any suppliers assigned
    And product product3 should not have a default supplier
    And product product3 default supplier reference should be empty
    And product product3 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 20    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |

  Scenario: Updating standard product wholesale price should update default supplier price
    Given I add product "product4" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product product4 type should be standard
    And product product4 should not have any suppliers assigned
    And product product4 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 0     |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    When I associate suppliers to product "product4"
      | supplier  | product_supplier  |
      | supplier1 | product4supplier1 |
      | supplier2 | product4supplier2 |
    And I update product product4 suppliers:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product4supplier1 | supplier1 | my first supplier for product4  | USD      | 10                 |
      | product4supplier2 | supplier2 | my second supplier for product4 | EUR      | 11                 |
    Then product product4 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And product product4 should have following suppliers:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product4supplier1 | supplier1 | my first supplier for product4  | USD      | 10                 |
      | product4supplier2 | supplier2 | my second supplier for product4 | EUR      | 11                 |
    And product product4 should have following supplier values:
      | default supplier           | supplier1                      |
      | default supplier reference | my first supplier for product4 |
    # Product wholesale price has been updated as the default supplier was updated
    And product product4 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 10    |
      | unit_price       | 0     |
      | unit_price_ratio | 0     |
      | unity            |       |
    When I update product "product4" prices with following information:
      | wholesale_price  | 20    |
    # Updating the product wholesale price impacts the default supplier price
    Then product product4 should have following suppliers:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product4supplier1 | supplier1 | my first supplier for product4  | USD      | 20                 |
      | product4supplier2 | supplier2 | my second supplier for product4 | EUR      | 11                 |
    When I set product product4 default supplier to supplier2
    And product product4 should have following supplier values:
      | default supplier           | supplier2                       |
    When I update product "product4" prices with following information:
      | wholesale_price  | 30    |
    Then product product4 should have following suppliers:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product4supplier1 | supplier1 | my first supplier for product4  | USD      | 20                 |
      | product4supplier2 | supplier2 | my second supplier for product4 | EUR      | 30                 |

    Scenario: Associate suppliers without data
      Given I add product "product5" with following information:
        | name[en-US] | magic staff |
        | type        | standard    |
      And product product5 type should be standard
      And product product5 should not have any suppliers assigned
      When I associate suppliers to product "product5"
        | supplier  | product_supplier  |
        | supplier2 | product5supplier2 |
        | supplier1 | product5supplier1 |
      Then product product5 should have the following suppliers assigned:
        | supplier1 |
        | supplier2 |
      And product product5 should have following suppliers:
        | product_supplier  | supplier  | reference | currency | price_tax_excluded |
        | product5supplier1 | supplier1 |           | USD      | 0                  |
        | product5supplier2 | supplier2 |           | USD      | 0                  |
      # Default supplier is the first one
      And product product5 should have following supplier values:
        | default supplier           | supplier2  |
        | default supplier reference |            |
      # Update data before changing association
      When I update product product5 suppliers:
        | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
        | product5supplier1 | supplier1 | my first supplier for product5  | USD      | 10                 |
        | product5supplier2 | supplier2 | my second supplier for product5 | EUR      | 11                 |
      Then product product5 should have following suppliers:
        | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
        | product5supplier1 | supplier1 | my first supplier for product5  | USD      | 10                 |
        | product5supplier2 | supplier2 | my second supplier for product5 | EUR      | 11                 |
      # We associate again, the suppliers' data is not modified especially the default one which was already set
      When I associate suppliers to product "product5"
        | supplier  | product_supplier  |
        | supplier1 | product5supplier1 |
        | supplier2 | product5supplier2 |
      Then product product5 should have the following suppliers assigned:
        | supplier1 |
        | supplier2 |
      And product product5 should have following suppliers:
        | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
        | product5supplier1 | supplier1 | my first supplier for product5  | USD      | 10                 |
        | product5supplier2 | supplier2 | my second supplier for product5 | EUR      | 11                 |
      # Default supplier is still the same
      And product product5 should have following supplier values:
        | default supplier           | supplier2                       |
        | default supplier reference | my second supplier for product5 |
      # I associate new suppliers without default one, a new default supplier is chosen
      When I associate suppliers to product "product5"
        | supplier  | product_supplier  |
        | supplier1 | product5supplier1 |
        | supplier3 | product5supplier3 |
      Then product product5 should have the following suppliers assigned:
        | supplier1 |
        | supplier3 |
      And product product5 should have following suppliers:
        | product_supplier  | supplier  | reference                      | currency | price_tax_excluded |
        | product5supplier1 | supplier1 | my first supplier for product5 | USD      | 10                 |
        | product5supplier3 | supplier3 |                                | USD      | 0                  |
      # Default supplier is the first one
      And product product5 should have following supplier values:
        | default supplier           | supplier1                      |
        | default supplier reference | my first supplier for product5 |
      # Wholesale price should have been updated as well matching the new default supplier
      When I update product "product5" prices with following information:
        | wholesale_price  | 10    |
      # Finally, I can remove all suppliers ith one command
      When I remove all associated product product5 suppliers
      Then product product5 should not have any suppliers assigned
      And product product5 should not have a default supplier
      And product product5 default supplier reference should be empty

  Scenario: Update product suppliers without specifying the productSupplierId should also work
    Given I add product "product6" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product product6 type should be standard
    And product product6 should not have any suppliers assigned
    When I associate suppliers to product "product6"
      | supplier  | product_supplier  |
      | supplier2 | product6supplier2 |
      | supplier1 | product6supplier1 |
    Then product product6 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And product product6 should have following suppliers:
      | product_supplier  | supplier  | reference | currency | price_tax_excluded |
      | product6supplier1 | supplier1 |           | USD      | 0                  |
      | product6supplier2 | supplier2 |           | USD      | 0                  |
    And product product6 should have following supplier values:
      | default supplier           | supplier2  |
      | default supplier reference |            |
    # Update data without specifying the product supplier ID it should work as productId/supplierId is enough to match
    # an existing product supplier, we simply cannot assert that they match the provided productSupplierId since it is
    # not provided This feature is important in the form it allows performing both association of new suppliers and
    # update of such new suppliers even if we cannot know the productSupplierId in advance since it has not been created
    When I update product product6 suppliers:
      | supplier  | reference                       | currency | price_tax_excluded |
      | supplier1 | my first supplier for product6  | USD      | 10                 |
      | supplier2 | my second supplier for product6 | EUR      | 11                 |
    Then product product6 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And product product6 should have following suppliers:
      | product_supplier  | supplier  | reference                       | currency | price_tax_excluded |
      | product6supplier1 | supplier1 | my first supplier for product6  | USD      | 10                 |
      | product6supplier2 | supplier2 | my second supplier for product6 | EUR      | 11                 |

    Scenario: I delete a supplier the default suppliers are updated for affected products
      Given I add new supplier supplier3 with following properties:
        | name                    | my supplier 3        |
        | address                 | Donelaicio st. 3     |
        | city                    | Kaunas               |
        | country                 | Lithuania            |
        | enabled                 | true                 |
        | description[en-US]      | just a supplier      |
        | meta title[en-US]       | my supplier nr three |
        | meta description[en-US] |                      |
        | meta keywords[en-US]    | sup,3                |
        | shops                   | [shop1]              |
      And I add new supplier supplier4 with following properties:
        | name                    | my supplier 4       |
        | address                 | Donelaicio st. 4    |
        | city                    | Kaunas              |
        | country                 | Lithuania           |
        | enabled                 | true                |
        | description[en-US]      | just a supplier     |
        | meta title[en-US]       | my supplier nr four |
        | meta description[en-US] |                     |
        | meta keywords[en-US]    | sup,4               |
        | shops                   | [shop1]             |
      Given I add product "product7" with following information:
        | name[en-US] | magic staff |
        | type        | standard    |
      And product product7 type should be standard
      And product product7 should not have any suppliers assigned
      When I associate suppliers to product "product7"
        | supplier  | product_supplier  |
        | supplier4 | product7supplier4 |
        | supplier3 | product7supplier3 |
      And I update product product7 suppliers:
        | product_supplier  | supplier  | reference                    | currency | price_tax_excluded |
        | product7supplier3 | supplier3 | third supplier for product7  | EUR      | 11                 |
        | product7supplier4 | supplier4 | fourth supplier for product7 | USD      | 10                 |
      Then product product7 should have the following suppliers assigned:
        | supplier3 |
        | supplier4 |
      And product product7 should have following suppliers:
        | product_supplier  | supplier  | reference                    | currency | price_tax_excluded |
        | product7supplier3 | supplier3 | third supplier for product7  | EUR      | 11                 |
        | product7supplier4 | supplier4 | fourth supplier for product7 | USD      | 10                 |
      And product product7 should have following supplier values:
        | default supplier           | supplier4                    |
        | default supplier reference | fourth supplier for product7 |
      Given I add product "product8" with following information:
        | name[en-US] | magic staff |
        | type        | standard    |
      And product product8 type should be standard
      And product product8 should not have any suppliers assigned
      When I associate suppliers to product "product8"
        | supplier  | product_supplier  |
        | supplier4 | product8supplier4 |
      And I update product product8 suppliers:
        | product_supplier  | supplier  | reference                    | currency | price_tax_excluded |
        | product8supplier4 | supplier4 | fourth supplier for product8 | USD      | 10                 |
      Then product product8 should have the following suppliers assigned:
        | supplier4 |
      And product product8 should have following suppliers:
        | product_supplier  | supplier  | reference                    | currency | price_tax_excluded |
        | product8supplier4 | supplier4 | fourth supplier for product8 | USD      | 10                 |
      And product product8 should have following supplier values:
        | default supplier           | supplier4                    |
        | default supplier reference | fourth supplier for product8 |
      When I delete supplier supplier4
      Then product product7 should have the following suppliers assigned:
        | supplier3 |
      And product product7 should have following suppliers:
        | product_supplier  | supplier  | reference                    | currency | price_tax_excluded |
        | product7supplier3 | supplier3 | third supplier for product7  | EUR      | 11                 |
      And product product7 should have following supplier values:
        | default supplier           | supplier3                   |
        | default supplier reference | third supplier for product7 |
      And product product8 should not have any suppliers assigned
      And product product8 should have following supplier values:
        | default supplier           | |
        | default supplier reference | |

    Scenario: I disable a supplier associated to a product
      Given I add new supplier supplier5 with following properties:
        | name                    | my supplier 5       |
        | address                 | Donelaicio st. 5    |
        | city                    | Kaunas              |
        | country                 | Lithuania           |
        | enabled                 | true                |
        | description[en-US]      | just a supplier     |
        | meta title[en-US]       | my supplier nr five |
        | meta description[en-US] |                     |
        | meta keywords[en-US]    | sup,5               |
        | shops                   | [shop1]             |
      Given I add product "product9" with following information:
        | name[en-US] | magic staff |
        | type        | standard    |
      And product product9 type should be standard
      And product product9 should not have any suppliers assigned
      When I associate suppliers to product "product9"
        | supplier  | product_supplier  |
        | supplier5 | product9supplier5 |
        | supplier1 | product9supplier1 |
      And I update product product9 suppliers:
        | product_supplier  | supplier  | reference                   | currency | price_tax_excluded |
        | product9supplier1 | supplier1 | first supplier for product9 | EUR      | 11                 |
        | product9supplier5 | supplier5 | fifth supplier for product9 | USD      | 10                 |
      Then product product9 should have the following suppliers assigned:
        | supplier1 |
        | supplier5 |
      And product product9 should have following suppliers:
        | product_supplier  | supplier  | reference                   | currency | price_tax_excluded |
        | product9supplier1 | supplier1 | first supplier for product9 | EUR      | 11                 |
        | product9supplier5 | supplier5 | fifth supplier for product9 | USD      | 10                 |
      And product product9 should have following supplier values:
        | default supplier           | supplier5                   |
        | default supplier reference | fifth supplier for product9 |
      # Now I disable supplier
      When I toggle status for supplier supplier5
      Then supplier supplier5 should have following properties:
        | name                    | my supplier 5       |
        | address                 | Donelaicio st. 5    |
        | city                    | Kaunas              |
        | country                 | Lithuania           |
        | enabled                 | false               |
        | description[en-US]      | just a supplier     |
        | meta title[en-US]       | my supplier nr five |
        | meta description[en-US] |                     |
        | meta keywords[en-US]    | sup,5               |
        | shops                   | [shop1]             |
      # Product data don't change
      And product product9 should have following suppliers:
        | product_supplier  | supplier  | reference                   | currency | price_tax_excluded |
        | product9supplier1 | supplier1 | first supplier for product9 | EUR      | 11                 |
        | product9supplier5 | supplier5 | fifth supplier for product9 | USD      | 10                 |
      And product product9 should have following supplier values:
        | default supplier           | supplier5                   |
        | default supplier reference | fifth supplier for product9 |
      # We can still edit them
      When I update product product9 suppliers:
        | product_supplier  | supplier  | reference                   | currency | price_tax_excluded |
        | product9supplier1 | supplier1 | first supplier for product9 | EUR      | 22                 |
        | product9supplier5 | supplier5 | fifth supplier for product9 | USD      | 20                 |
      Then product product9 should have following suppliers:
        | product_supplier  | supplier  | reference                   | currency | price_tax_excluded |
        | product9supplier1 | supplier1 | first supplier for product9 | EUR      | 22                 |
        | product9supplier5 | supplier5 | fifth supplier for product9 | USD      | 20                 |
