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
    Then product product1 should have following suppliers:
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
    And I update product product1 suppliers:
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
    Then product product1 should have following suppliers:
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
    Then product product1 should have following suppliers:
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
    And I update product product3 suppliers:
      | product_supplier  | supplier  | reference                      | currency | price_tax_excluded |
      | product3supplier1 | supplier1 | my first supplier for product3 | USD      | 10                 |
    Then product product3 should have following suppliers:
      | product_supplier  | supplier  | reference                      | currency | price_tax_excluded |
      | product3supplier1 | supplier1 | my first supplier for product3 | USD      | 10                 |
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
    When I remove all associated product "product3" suppliers
    Then product product3 should not have any suppliers assigned
    And product product3 should not have a default supplier
    And product product3 default supplier reference should be empty
    # The wholesale value is not removed even if default supplier has been removed
    And product product3 should have following prices information:
      | price            | 0     |
      | ecotax           | 0     |
      | tax rules group  |       |
      | on_sale          | false |
      | wholesale_price  | 10    |
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
    Then product product4 should have following suppliers:
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
      Then product product5 should have following suppliers:
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
      Then product product5 should have following suppliers:
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
      Then product product5 should have following suppliers:
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
